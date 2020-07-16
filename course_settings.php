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
 */

// Load moodle.
require_once(__DIR__.'/../../../config.php');

// Get course id.
$courseid = optional_param('id', 0, PARAM_INT);

// Login checks.
require_login($courseid, true);
if (isguestuser()) {
        throw new require_login_exception('Guests are not permitted to access this page.');
}

$PAGE->set_url('/admin/tool/paymentplugin/course_settings.php');
$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
$PAGE->set_title("Testng?"/*get_string('coursesettings_management:title', 'tool_paymentplugin')*/);
$PAGE->set_cacheable(false); // Look this up

// if ($node = $PAGE->settingsnav->find())

// $OUTPUT = $PAGE->get_renderer('tool_paymentplugin'); // Do I need to make this renderer, or can it be avoided?


echo $OUTPUT->header();

// Insert settings here.

echo $OUTPUT->footer();
