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

namespace paymentgateway_paypal;

class ipn {

    /** @var string The request to be sent back to PayPal for validation */
    private $req;

    /**
     * Reads all data from an IPN. Extracts all data to $data from it and creates 
     * $req to be used later.
     * 
     * @param object $post The IPN POST request.
     * @return object $data Data from the IPN that has been processed.
     * @throws \moodle_exception
     */
    public function process_ipn($post) {

        $this->req = 'cmd=_notify-validate';

        $data = new \stdClass();
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

        // Brain is doing dumb, there has to be a cleaner way to do this.
        $data->txn_type             = isset($postdata->txn_type)          ? $postdata->txn_type          : null;
        $data->business             = isset($postdata->business)          ? $postdata->business          : null;
        $data->charset              = isset($postdata->charset)           ? $postdata->charset           : null;
        $data->receiver_email       = isset($postdata->receiver_email)    ? $postdata->receiver_email    : null;
        $data->receiver_id          = isset($postdata->receiver_id)       ? $postdata->receiver_id       : null;
        $data->residence_country    = isset($postdata->residence_country) ? $postdata->residence_country : null;
        $data->test_ipn             = isset($postdata->test_ipn)          ? $postdata->test_ipn          : null;
        $data->txn_id               = $postdata->txn_id;
        $data->first_name           = isset($postdata->first_name)        ? $postdata->first_name        : null;
        $data->last_name            = isset($postdata->last_name)         ? $postdata->last_name         : null;
        $data->payer_id             = isset($postdata->payer_id)          ? $postdata->payer_id          : null;
        $data->item_name            = isset($postdata->item_name1)        ? $postdata->item_name1        : null;
        $data->mc_currency          = isset($postdata->mc_currency)       ? $postdata->mc_currency       : null;
        $data->mc_gross             = $postdata->mc_gross;
        $data->payment_date         = isset($postdata->payment_date)      ? $postdata->payment_date      : null;
        $data->userid               = (int)$custom[0];
        $data->courseid             = (int)$custom[1];

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
}

