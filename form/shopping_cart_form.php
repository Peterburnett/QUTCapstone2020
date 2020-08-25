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
 * File         shopping_cart_form.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class shopping_cart_form extends moodleform {

    public function definition() {
        global $DB, $OUTPUT;

        $thisform = $this->_form;

        $thisform->addElement('table');
        /*$thisform->addElement('html', '
        <div class="qheader">
            <table width=50%>
                <tr>
                    <th>Course Name</th>
                    <th>Course Cost</th>
                    <th>Somethin Else</th>
                </tr>
                <tr>
                    <td>SmartPeopleStuff</td>
                    <td>$100.50</td>
                    <td>Somethin</td>
                </tr>
            </style>
        </div>
        ');*/
    }
}