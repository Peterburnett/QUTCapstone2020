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
 * description
 *
 * File         course_settings.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
    require_once('/../../config.php');
    $cmid = required_param('id', PARAM_INT);
    $cm = get_coursemodule_from_id('paymentplugin', $cmid, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, true, $cm);
    $PAGE->set_url('/mod/test/view.php', array('id' => $cm->id));
    $PAGE->set_pagelayout('standard'); 
    $PAGE->set_title('Payment Plugin Title');
    $PAGE->set_heading('Payment Plugin Heading');

