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

use dml_exception;

defined ('MOODLE_INTERNAL') || die();

/**
 * Contains the main functions for process and assessing the vadality of the ipn data given by Paypal.
 */
class ipn {

    const INVALID_IPN = 'INVALID';
    const VERIFIED_IPN = 'VERIFIED';

    /** @var array The list of all properties that are needed from paypal. */
    private static $properties = ['txn_type', 'business', 'charset', 'parent_txn_id', 'receiver_id', 'receiver_email',
                       'residence_country', 'resend', 'test_ipn', 'txn_id', 'first_name', 'last_name', 'payer_id', 'item_name1',
                       'mc_currency', 'mc_gross', 'payment_date', 'payment_status', 'pending_reason'];

    /** @var string The request to be sent back to PayPal for validation. */
    private $validationrequest;

    private function create_validation_request() {
        $this->validationrequest = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value) {
            $this->validationrequest .= "&$key=".urlencode($value);
        }
    }

    private function sanitise_post_data() : \stdClass {
        $required = ['txn_id', 'payment_status', 'verified'];

        $data = new \stdClass();
        foreach (self::$properties as $property) {
            if (in_array($property, $required)) {
                $data->$property = required_param($property, PARAM_RAW);
            } else {
                $data->$property = optional_param($property, null, PARAM_RAW);
            }
        }
        $data->custom = required_param('custom', PARAM_RAW);
        return $data;
    }

    /**
     * Reads all data from an IPN. Extracts data from it and creates $validationrequest for later use.
     *
     * @param object $post The IPN POST request.
     * @return object $data Data from the IPN that has been processed.
     * @throws \moodle_exception
     */
    public function process_ipn() : \stdClass {

        $postextract = $this->sanitise_post_data();
        $this->create_validation_request();

        $custom = explode('-', $postextract->custom);
        if (empty($custom) || count($custom) < 2) {
            throw new \moodle_exception('invalidrequest', 'core_error', '', null,
                get_string('error:invalidcustom', 'paymentgateway_paypal'));
        }

        $postextract->userid = (int) $custom[0];
        $postextract->courseid = (int) $custom[1];
        unset($postextract->custom);
        $postextract->payment_date = (string) strtotime($postextract->payment_date);

        return $postextract;
    }

    /**
     * Validates the transaction data.
     *
     * @param \stdclass $data The transaction data.
     */
    public function validate(\stdClass $data) : string {
        global $CFG;

        // Open a connection back to PayPal to validate the data.
        $paypaladdress = empty($CFG->usepaypalsandbox) ? 'ipnpb.paypal.com' : 'ipnpb.sandbox.paypal.com';
        $c = new \curl();
        $options = array(
            'returntransfer' => true,
            'httpheader' => array('application/x-www-form-urlencoded', "Host: $paypaladdress"),
            'timeout' => 30,
            'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
        );
        $location = "https://$paypaladdress/cgi-bin/webscr";
        $result = $c->post($location, $this->validationrequest, $options);

        if ($c->get_errno()) {
            throw new \moodle_exception('errpaypalconnect', 'enrol_paypal', '', array('url' => $paypaladdress, 'result' => $result),
                json_encode($data));
        }

        return $result;
    }

    /**
     * Checks if anything is wrong with transaction data, and deals
     * with errors by adding them to errorinfo.
     *
     * @param object $data
     * @return bool $noerror
     */
    private function error_check(\stdClass &$data) {
        global $DB;

        $noerror = true;

        $currency = get_config('tool_paymentplugin', 'currency');
        $cost = $DB->get_field('tool_paymentplugin_course', 'cost', array('courseid' => $data->courseid));

        // Check that course exists.
        $errorinfo = "";
        try {
            get_course($data->courseid);
        } catch (dml_exception $e) {
            $noerror = false;
            $errorinfo .= get_string('erroripncourseid', 'paymentgateway_paypal') . " ";
        }

        // Check that course price and currency matches. Only give price error if course exists.
        if ($data->mc_currency != $currency) {
            $noerror = false;
            $errorinfo .= get_string('erroripncurrency', 'paymentgateway_paypal') . " ";
        }
        if ($data->mc_gross != $cost && $DB->record_exists('course', array('id' => $data->courseid))) {
            $noerror = false;
            $errorinfo .= get_string('erroripncost', 'paymentgateway_paypal') . " ";
        }

        // Check that userid is valid.
        if (!\core_user::get_user($data->userid)) {
            $noerror = false;
            $errorinfo .= get_string('erroripnuserid', 'paymentgateway_paypal') . " ";
        }

        if (!$noerror) {
            // Remove trailing whitespace.
            $errorinfo = rtrim($errorinfo);
            // Leave record of unsuccessful purchase in database with error details.
            $data->errorinfo = $errorinfo;
        }

        return $noerror;
    }

    /**
     * This function sends the transaction data through to the gateway to be filtered and the transaction, actioned.
     *
     * @param string $result 'INVALID' if the IPN could not be verified. 'VERIFIED' if successful.
     * @param \stdclass $data The transaction data.
     */
    public function submit_data(string $result, \stdClass $data) : int {
        $paypalgateway = \tool_paymentplugin\plugininfo\paymentgateway::get_gateway_object('paypal');

        if (strcmp($result, self::INVALID_IPN) == 0) {                 // INVALID PAYPAL IPN.
            $data->verified = 0;
            $data->errorinfo = get_string('erroripninvalid', 'paymentgateway_paypal');
            throw new \moodle_exception('erroripninvalid', 'paymentgateway_paypal', '', null, json_encode($data));
        } else if (strcmp($result, self::VERIFIED_IPN) == 0) {          // VALID PAYPAL IPN.
            $data->verified = 1;
            $this->error_check($data);
            $res = $paypalgateway->submit_purchase_data($data);
            return $res;
        }
    }
}
