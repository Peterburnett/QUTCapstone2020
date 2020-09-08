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
 * File         payment.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

use tool_paymentplugin\plugininfo\paymentgateway;

require_once(__DIR__ . '/../../../config.php');
require_login();

$courseid = required_param('id', PARAM_INT);

$PAGE->set_url(new moodle_url('/admin/tool/paymentplugin/purchase.php', array('id' => $courseid)));
$course = $DB->get_record('course', array('id' => $courseid));
$context = \context_course::instance($course->id);
$PAGE->set_context($context);
$PAGE->set_title(get_string('purchasepagetitle', 'tool_paymentplugin'));
$PAGE->set_heading(get_string('purchasepagetitle', 'tool_paymentplugin'));

$courseinfo = new stdClass;
$courseinfo->name = format_string($course->fullname, true, array('context' => $context));
$tablename = 'tool_paymentplugin_course';
$record = $DB->get_record($tablename, ['courseid' => $courseid]);
$courseinfo->cost = $record->cost;

echo $OUTPUT->header();

echo get_string('purchasepagecourse', 'tool_paymentplugin', $courseinfo);

if (count(paymentgateway::get_all_enabled_gateway_objects()) != 0){
    $args = array('id' => $courseid);
    $paymentform = new tool_paymentplugin\form\purchase_form(new moodle_url(
        '/admin/tool/paymentplugin/test_paypal_sdk.php',
        array('id' => $courseid)), $args);
    $paymentform->display();
}
else {
    throw new moodle_exception(get_string('errornothingenabled', 'tool_paymentplugin'));
}

echo $OUTPUT->footer();
