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
 * File         payment_settings_form.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class payment_settings_form extends moodleform {

    public function definition() {
        global $DB;

        $thisform = $this->_form;
        $courseid = $this->_customdata['id'];

        $thisform->addElement('text', 'coursecost', 'Course Cost');
        $thisform->setType('coursecost', PARAM_INT);

        $tablename = 'tool_paymentplugin_course';
        $cost = 0;

        if ($DB->record_exists($tablename, ['courseid' => $courseid])) {
            $record = $DB->get_record($tablename, ['courseid' => $courseid]);
            $cost = $record->cost;
        }

        $thisform->setDefault('coursecost', $cost);

        // Need to add course id to url somehow
        $this->add_action_buttons(true);
    }
} 
// DB->insert_record(); // https://docs.moodle.org/dev/Data_manipulation_API
// https://docs.moodle.org/dev/XMLDB_editor
// https://docs.moodle.org/dev/Upgrade_API#install.php
