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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// TODO: Ensure receiving 2 of the same IPN does not result in 
// user getting enrolled twice. Create table in database to check for
// duplicate transaction IDs. Remember to still deal with duplicate IPNs
// with the usual process, just don't enrol the user a second time.

class ipn {

    /** @var string The  */
    private $req;

    public function process_ipn($post) {
        // Read all the data from PayPal and get it ready for later;
        // we expect only valid UTF-8 encoding, it is the responsibility
        // of user to set it up properly in PayPal business account.
        $this->req = 'cmd=_notify-validate';

        $data = new stdClass();

        foreach ($post as $key => $value) {
                $this->req .= "&$key=".urlencode($value);
                $data->$key = fix_utf8($value);
        }

        if (empty($data->custom)) {
            throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Missing request param: custom');
        }

        $custom = explode('-', $data->custom);
        unset($data->custom);

        if (empty($custom) || count($custom) < 2) {
            throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Invalid value of the request param: custom');
        }

        $data->userid           = (int)$custom[0];
        $data->courseid         = (int)$custom[1];
        $data->payment_gross    = $data->mc_gross;
        $data->payment_currency = $data->mc_currency;
        $data->timeupdated      = time();

        return $data;
    }

    public function validate($data) {
        global $CFG;
        
        // Open a connection back to PayPal to validate the data.
        $paypaladdr = empty($CFG->usepaypalsandbox) ? 'ipnpb.paypal.com' : 'ipnpb.sandbox.paypal.com';
        $c = new curl();
        $options = array(
            'returntransfer' => true,
            'httpheader' => array('application/x-www-form-urlencoded', "Host: $paypaladdr"),
            'timeout' => 30,
            'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
        );
        $location = "https://$paypaladdr/cgi-bin/webscr";
        $result = $c->post($location, $this->req, $options);

        if ($c->get_errno()) {
            throw new moodle_exception('errpaypalconnect', 'enrol_paypal', '', array('url' => $paypaladdr, 'result' => $result),
                json_encode($data));
        }

        return $result;
    }
}

