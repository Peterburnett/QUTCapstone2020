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
 * The page a user goes to when purchasing a course.
 *
 * @package     tool_paymentplugin
 * @author      Haruki Nakagawa
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

use tool_paymentplugin\plugininfo\paymentgateway;
require_once(__DIR__ . '/../../../config.php');

// Login & Permission Checks.
require_login();

// Page Setup.
$courseid = required_param('id', PARAM_INT);

// Check if purchase exists and if user is enrolled.
if ($DB->record_exists('tool_paymentplugin_purchases', array('courseid' => $courseid, 'userid' => $USER->id, 'success' => 1))) {
    if (is_enrolled(context_course::instance($courseid))) {
        redirect(new moodle_url("$CFG->wwwroot/course/view.php?id=$courseid"), "You have already purchased this course.");
    }
}

// Page Setup.
$PAGE->set_url(new moodle_url('/admin/tool/paymentplugin/purchase.php', array('id' => $courseid)));
$course = $DB->get_record('course', array('id' => $courseid));
$context = \context_course::instance($course->id);
$PAGE->set_context($context);

// Call Javascript.
$PAGE->requires->js_call_amd('tool_paymentplugin/purchase', 'purchasecheck', array($course->id, $USER->id));

// Page Display.
$PAGE->set_title(get_string('purchasepagetitle', 'tool_paymentplugin'));
$PAGE->set_heading(get_string('purchasepagetitle', 'tool_paymentplugin'));
echo $OUTPUT->header();

// Notifiy user of course name and price.
$courseinfo = new stdClass;
$courseinfo->name = format_string($course->fullname, true, array('context' => $context));
$record = $DB->get_record('tool_paymentplugin_course', ['courseid' => $courseid]);
$courseinfo->cost = $record->cost;
$courseinfo->currency = strtoupper(get_config('tool_paymentplugin', 'currency'));
echo get_string('purchasepagecourse', 'tool_paymentplugin', $courseinfo);

// Display Payment Gateway Form.
if (count(paymentgateway::get_all_enabled_gateway_objects()) != 0) {
    $paymentform = new tool_paymentplugin\form\purchase(new moodle_url(
        '/admin/tool/paymentplugin/purchase.php',
        array('id' => $courseid)), array('id' => $courseid));
    $paymentform->display();
} else {
    throw new moodle_exception(get_string('errornothingenabled', 'tool_paymentplugin'));
}

echo $OUTPUT->footer();
