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
 * Form for the course settings page.
 *
 * @package     tool_paymentplugin
 * @author      Mitchell Halpin, Haruki Nakagawa
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace tool_paymentplugin\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class course_settings_form extends \moodleform {

    /**
     * Creates the form for course settings.
     * 
     * @return void
     */
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
        $this->add_action_buttons(true);
    }

    /**
     * Additional validation checks
     * 
     * @return array of errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['coursecost'] < 0) {
            $errors['coursecost'] = get_string('errorcoursecost', 'tool_paymentplugin');
        }
        return $errors;
    }
}