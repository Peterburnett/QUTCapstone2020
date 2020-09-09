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
 * Listens for Instant Payment Notification from PayPal
 *
 * This script waits for Payment notification from PayPal,
 * then double checks that data by sending it back to PayPal.
 * If PayPal verifies this then it sets up the enrolment for that
 * user.
 *
 * @package    paymentgateway_paypal
 * @copyright  2010 Eugene Venter
 * @copyright  2015 Daniel Neis
 * @copyright  MAHQ
 * @author     Eugene Venter - based on code by others
 * @author     Daniel Neis - based on code by others
 * @author     Haruki Nakagawa - based on code by others
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This file do not require login because paypal service will use to confirm transactions.
// @codingStandardsIgnoreLine
require("../../../../../config.php");

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir.'/enrollib.php');

// PayPal does not like when we return error messages here,
// the custom handler just logs exceptions and stops.
set_exception_handler('paymentgateway_paypal_ipn_exception_handler');

// Keep out casual intruders.
if (empty($_POST) or !empty($_GET)) {
    print_error("Sorry, you can not use the script that way.");
}

// Read all the data from PayPal and get it ready for later;
// we expect only valid UTF-8 encoding, it is the responsibility
// of user to set it up properly in PayPal business account.
$req = 'cmd=_notify-validate';

$data = new stdClass();

foreach ($_POST as $key => $value) {
        $req .= "&$key=".urlencode($value);
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
$result = $c->post($location, $req, $options);

if ($c->get_errno()) {
    throw new moodle_exception('errpaypalconnect', 'enrol_paypal', '', array('url' => $paypaladdr, 'result' => $result),
        json_encode($data));
}

// Connection is OK, so now we post the data to validate it.

// Now read the response and check if everything is OK.

if (strlen($result) > 0) {
    if (strcmp($result, "VERIFIED") == 0) {          // VALID PAYMENT!
        // Enrol user.
        $enrol = enrol_get_plugin('payment');
        $enrolinstance = $DB->get_record('enrol', array('enrol' => 'payment', 'courseid' => $data->courseid));
        $enrol->enrol_user($enrolinstance, $data->userid);
    } else {
        print_error("Transaction failed to verify.");
    }
} else {
    print_error("Failure on result");
}






/**
 * Silent exception handler.
 *
 * @param Exception $ex
 * @return void - does not return. Terminates execution!
 */
function paymentgateway_paypal_ipn_exception_handler($ex) {
    $info = get_exception_info($ex);

    $logerrmsg = "paymentgateway_paypal IPN exception handler: ".$info->message;
    if (debugging('', DEBUG_NORMAL)) {
        $logerrmsg .= ' Debug: '.$info->debuginfo."\n".format_backtrace($info->backtrace, true);
    }
    mtrace($logerrmsg);
    exit(0);
}
