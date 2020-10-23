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
 * @copyright  MAHQ
 * @author     Haruki Nakagawa - based on code by others
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use paymentgateway_paypal\ipn;
// This file do not require login because paypal service will use to confirm transactions.
// @codingStandardsIgnoreLine
require(__DIR__."/../../../../../config.php");

require_once($CFG->libdir . '/filelib.php');

// PayPal does not like when we return error messages here,
// the custom handler just logs exceptions and stops.
set_exception_handler(\paymentgateway_paypal\util::get_exception_handler());

// Keep out casual intruders.
if (empty($_POST) or !empty($_GET)) {
    print_error(get_string('error:purchasescript', 'paymentgateway_paypal'));
    die();
}

$ipn = new ipn();
$data = $ipn->process_ipn();
$result = $ipn->validate($data);
$ipn->submit_data($result, $data);

// Restore the exception handler to what it was before.
restore_exception_handler();
