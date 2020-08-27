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
 * Creates a settings page for a course.
 *
 * File         course_settings.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

require_once(__DIR__.'/../../../config.php');
use tool_paymentplugin\form\course_settings_form;

$courseid = optional_param('id', 0, PARAM_INT);
if (empty($courseid)) {
        throw new moodle_exception('No valid course id detected.');
}

$course = get_course($courseid);
require_login($courseid, true);
$coursecontext = context_course::instance(course_get_format($course)->get_course()->id);
require_capability('moodle/course:create', $coursecontext);

// Set up the page.
$title = get_string('coursesettings_management:title', 'tool_paymentplugin');
$PAGE->set_url('/admin/tool/paymentplugin/course_settings.php');
$PAGE->set_pagelayout('admin'); // What this do?
$PAGE->set_context(context_course::instance($courseid));
$PAGE->set_cacheable(false); // What this do?

$PAGE->set_heading($title);
$PAGE->navbar->add($title, new moodle_url('/admin/tool/paymentplugin/course_settings.php'));

// Displaying the page.
echo $OUTPUT->header();

// Create settings form.
$args = array(
    'course' => $course,
    'id' => $courseid,
    );
$paymentform = new course_settings_form(new moodle_url('/admin/tool/paymentplugin/course_settings.php',
     array('id' => $courseid)), $args);

if (($formdata = $paymentform->get_data()) && !($paymentform->is_cancelled())) {
    $tablename = 'tool_paymentplugin_course';
    $cost = $formdata->coursecost;

    if ($DB->record_exists($tablename, ['courseid' => $courseid])) {
        $record = $DB->get_record($tablename, ['courseid' => $courseid]);
        $record->cost = $cost;
        $DB->update_record($tablename, $record);
    } else {
        $record = (object) array('courseid' => $courseid, 'cost' => $cost);
        $DB->insert_record($tablename, $record);
    }
}
$paymentform->display();

echo $OUTPUT->footer();
