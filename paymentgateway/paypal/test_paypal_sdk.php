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
 * A temporary test page for testing the sending of a user to paypal for a course purchase.
 * This page is for test purposes, and will later be replaced by a form that appears
 * on the course enrolment page.  This uses the more up-to-date SDK integration of the smart buttons.
 * https://developer.paypal.com/docs/checkout/integrate/#
 *
 * File         test_paypal_sdk.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @author      Haruki Nakagawa - based on code by others
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_paymentplugin\plugininfo\paymentgateway;

require_once(__DIR__ . '/../../../../../config.php');
require_login();

$courseid = required_param('id', PARAM_INT);

$PAGE->set_context(CONTEXT_COURSE::instance($courseid));
$PAGE->set_url(new moodle_url('/admin/tool/paymentplugin/paymentgateway/paypal/test_paypal_sdk.php', array('id' => $courseid)));
$PAGE->set_title("test paypal payment");
$PAGE->set_heading("paypal payment");


echo $OUTPUT->header();

$paymentgateways = paymentgateway::get_all_enabled_gateway_objects();
foreach ($paymentgateways as $paymentgateway) {
  echo $paymentgateway -> payment_button($courseid);
}

echo $OUTPUT->footer();
