<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * IPN class that handles verification and enrolment of IPNs.
 *
 * @package    paymentgateway_paypal
 * @copyright  MAHQ
 * @author     Haruki Nakagawa - based on code by others
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter - based on code by others
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paymentgateway_paypal;

class ipn {

    /**
     * Takes specific data from an IPN processed in process_ipn.
     *
     * @param object $postdata
     * @return object $data
     */
    private function set_data($postdata) {
        $data = new \stdClass();
        $properties = ['txn_type', 'business', 'charset', 'parent_txn_id', 'receiver_id', 'receiver_email', 'receiver_id',
                       'residence_country', 'resend', 'test_ipn', 'txn_id', 'first_name', 'last_name', 'payer_id', 'item_name1',
                       'mc_currency', 'mc_gross', 'payment_date', 'payment_status', 'pending_reason'];

        foreach ($properties as $property) {
            if (property_exists($postdata, $property)) {
                $data->$property = $postdata->$property;
            } else {
                $data->$property = null;
            }
        }

        return $data;
    }

    /** @var string The request to be sent back to PayPal for validation */
    private $req;

    /**
     * Reads all data from an IPN. Extracts data from it and creates $req for later use.
     *
     * @param object $post The IPN POST request.
     * @return object $data Data from the IPN that has been processed.
     * @throws \moodle_exception
     */
    public function process_ipn($post) {

        $this->req = 'cmd=_notify-validate';

        $postdata = new \stdClass();
        foreach ($post as $key => $value) {
                $this->req .= "&$key=".urlencode($value);
                $postdata->$key = fix_utf8($value);
        }

        if (empty($postdata->custom)) {
            throw new \moodle_exception('invalidrequest', 'core_error', '', null, 'Missing request param: custom');
        }

        $custom = explode('-', $postdata->custom);

        if (empty($custom) || count($custom) < 2) {
            throw new \moodle_exception('invalidrequest', 'core_error', '', null, 'Invalid value of the request param: custom');
        }

        $data = $this->set_data($postdata);

        $data->userid = (int)$custom[0];
        $data->courseid = (int)$custom[1];

        return $data;
    }

    public function validate($data) {
        global $CFG;

        // Open a connection back to PayPal to validate the data.
        $paypaladdr = empty($CFG->usepaypalsandbox) ? 'ipnpb.paypal.com' : 'ipnpb.sandbox.paypal.com';
        $c = new \curl();
        $options = array(
            'returntransfer' => true,
            'httpheader' => array('application/x-www-form-urlencoded', "Host: $paypaladdr"),
            'timeout' => 30,
            'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
        );
        $location = "https://$paypaladdr/cgi-bin/webscr";
        $result = $c->post($location, $this->req, $options);

        if ($c->get_errno()) {
            throw new \moodle_exception('errpaypalconnect', 'enrol_paypal', '', array('url' => $paypaladdr, 'result' => $result),
                json_encode($data));
        }

        return $result;
    }

    private function check_status($data) {
        $status = $data->payment_status;
        if ($status == "Completed" || $status == "Processed") {
            // Enrol the user.
            throw new \moodle_exception("Status is completed");
        } else if ($status == "Failed" || $status == "Denied") {
            // Notify student that payment failed (notify admin too or no?)
        } else if ($status == "Pending") {
            // don't do anything to the current enrolment
            // Notify student and admin that payment is pending
            // Notify admin of pending_reason, but only tell student that payment is pending
            // and to contact admin for details
        }
    }

    /**
     * Checks if anything is wrong with transaction data, and deals
     * with errors by adding them to error_info.
     *
     * @param object $data
     * @return bool $noerror
     */
    private function is_ipn_data_correct(&$data) {
        global $DB;

        $noerror = true;

        $currency = get_config('paymentgateway_paypal', 'currency');
        $cost = $DB->get_field('tool_paymentplugin_course', 'cost', array('courseid' => $data->courseid));

        // Check that course price and currency matches.
        $error_info = "";
        if ($data->mc_currency != $currency) {
            $error = false;
            $error_info .= get_string('erroripncurrency', 'paymentgateway_paypal') . " ";
        }
        if ($data->mc_gross != $cost) {
            $error = false;
            $error_info .= get_string('erroripncost', 'paymentgateway_paypal') . " ";
        }

        // Check that courseid and userid are valid.
        if (!$DB->record_exists('course', array('id' => $data->courseid))) {
            $error = false;
            $error_info .= get_string('erroripncourseid', 'paymentgateway_paypal') . " ";
        }
        if (!$DB->record_exists('user', array('id' => $data->userid))) {
            $error = false;
            $error_info .= get_string('erroripnuserid', 'paymentgateway_paypal') . " ";
        }

        if (!$noerror) {
            // Leave record of unsuccessful purchase in database with error details.
            $data->error_info = $error_info;
        }

        return $noerror;
    }

    public function process_data($result, $data) {

        $paypalgateway = \tool_paymentplugin\plugininfo\paymentgateway::get_gateway_object('paypal');

        if (strcmp ($result, "INVALID") == 0) {                 // INVALID PAYMENT
            $data->verified = 0;
            $data->error_info = get_string('erroripninvalid', 'paymentgateway_paypal');
            throw new \moodle_exception('erroripninvalid', 'paymentgateway_paypal', '', null, json_encode($data));
        } else if (strcmp($result, "VERIFIED") == 0) {          // VALID PAYMENT
            $data->verified = 1;
            $noerror = $this->is_ipn_data_correct($data);
            if ($noerror) {
                $paypalgateway->submit_purchase($data);
            }
        }
    }
}
