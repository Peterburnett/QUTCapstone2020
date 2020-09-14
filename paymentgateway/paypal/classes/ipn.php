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

// TODO: Ensure receiving 2 of the same IPN does not result in
// user getting enrolled twice. Create table in database to check for
// duplicate transaction IDs. Remember to still deal with duplicate IPNs
// with the usual process, just don't enrol the user a second time.

namespace paymentgateway_paypal;

use moodle_exception;

class ipn {

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

    /**
     * Takes specific data from an IPN processed in process_ipn.
     *
     * @param object $postdata
     * @return object $data
     */
    private function set_data($postdata) {
        $data = new \stdClass();
        $properties = ['txn_type', 'business', 'charset', 'parent_txn_id', 'receiver_id', 'receiver_email', 'receiver_id',
                       'residence_country', 'resend', 'test_ipn', 'txn_id', 'first_name', 'last_name', 'payer_id', 'item_name',
                       'mc_currency', 'mc_gross', 'payment_date'];

        foreach ($properties as $property) {
            if (property_exists($postdata, $property)) {
                $data->$property = $postdata->$property;
            } else {
                $data->$property = null;
            }
        }

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

    public function process_data($result, $data) {
        global $DB;

        $paypalgateway = \tool_paymentplugin\plugininfo\paymentgateway::get_gateway_object('paypal');

        if (strlen($result) > 0) {
            if (strcmp($result, "VERIFIED") == 0) {          // VALID PAYMENT!
                $data->verified = 1;

                $this->is_ipn_data_correct($data);

                // From here onwards we know that there is nothing wrong with this transaction.
                // Add successful transaction to transaction history.
                $data->success = 1;
                $paypalgateway->add_txn_to_db($data);
        
                // Make sure IPN is not a duplicate of one that has been processed already.
                if ($DB->record_exists('paymentgateway_paypal', array('txn_id' => $data->txn_id))) {
                    // Enrol user.
                    $paypalgateway->paymentplugin_enrol($data->courseid, $data->userid);
                }

            } else if (strcmp ($result, "INVALID") == 0) { // ERROR
                $data->verified = 0;
                $data->error_info = get_string('erroripninvalid', 'paymentgateway_paypal');
                $paypalgateway->add_txn_to_db($data);
                throw new moodle_exception('erroripninvalid', 'paymentgateway_paypal', '', null, json_encode($data));
            }
        }
    }

    /**
     * Checks if anything is wrong with transaction data, and deals
     * with errors by adding them to error_info.
     *
     * @param object $data
     * @return void
     * @throws moodle_exception
     */
    private function is_ipn_data_correct(&$data) {
        global $DB;

        $error = False;

        $currency = get_config('paymentgateway_paypal', 'currency');
        $cost = $DB->get_field('tool_paymentplugin_course', 'cost', array('courseid' => $data->courseid));

        // Check that course price and currency matches.
        $error_info = "";
        if ($data->mc_currency != $currency) {
            $error = True;
            $error_info .= get_string('erroripncurrency', 'paymentgateway_paypal') . " ";
        }
        if ($data->mc_gross != $cost) {
            $error = True;
            $error_info .= get_string('erroripncost', 'paymentgateway_paypal') . " ";
        }

        // Check that courseid and userid are valid.
        if (!$DB->record_exists('course', array('id' => $data->courseid))) {
            $error = True;
            $error_info .= get_string('erroripncourseid', 'paymentgateway_paypal') . " ";
        }
        if (!$DB->record_exists('user', array('id' => $data->userid))) {
            $error = True;
            $error_info .= get_string('erroripnuserid', 'paymentgateway_paypal') . " ";
        }

        if ($error) {
            // Leave record of unsuccessful purchase in database with error details.
            $data->error_info = $error_info;
            $paypalgateway = \tool_paymentplugin\plugininfo\paymentgateway::get_gateway_object('paypal');
            $paypalgateway->add_txn_to_db($data);
            throw new moodle_exception('erroripn', 'paymentgateway_paypal', '', null, json_encode($data));
        }
    }
}
