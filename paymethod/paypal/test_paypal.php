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
 * on the course enrolment page.
 *
 * File         test_payment.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_login();

$id = required_param('id', PARAM_INT);

$PAGE->set_context(CONTEXT_COURSE::instance($id));
$PAGE->set_url(new moodle_url('/admin/tool/paymentplugin/test_paypal.php', array('id'=>$id)));
$PAGE->set_title("test paypal payment");
$PAGE->set_heading("paypal payment");
// Using raw strings instead of get_string because this file will not be used.

echo $OUTPUT->header();

echo $OUTPUT->footer();
