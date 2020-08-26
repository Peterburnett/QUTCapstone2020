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
use tool_paymentplugin\shopping\shopping_session;

class shopping_cart_form extends moodleform {

    public function definition() {
        global $DB, $OUTPUT;

        $thisform = $this->_form;

        $htmlMain = '
        <style>
            #cart {
                display:flex;
                flex-direction:column;
            }
            #page-footer {
                order:2;
            }
            #cart-table {
                order:1;
            }
        </style>
        <div class="cart" style="order:1;">
            <div class="cart-table">
                <table width=50%>
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Course Cost</th>
                    </tr>
        ';
        $htmlEnd = '
                </table>
            </div>
        </div>
        ';

        $carttable = '';
        $cartcontents = shopping_session::getcart();
        $total = 0;
        if (!is_null($cartcontents)) {
            foreach ($cartcontents as $courseid)    {
                if (!is_null($courseid)) {
                    if (array_key_exists("button-".$courseid, $_POST)) {
                        shopping_session::removefromcart($courseid);
                        continue;
                    }

                    $recordA = $DB->get_record('tool_paymentplugin_course', ['courseid' => $courseid]);
                    $cost = 0;//$recordA->cost;
                    $total += $cost;
                    $recordB = $DB->get_record('course', ['id' => $courseid]);
                    $name = 'TEMP';//$recordB->fullname;
                    

                    $carttable .= '
                    <tr>
                    <td>'.$courseid.'</td>
                    <td>'.$name.'</td>
                    <td>'.$cost.'</td>
                    <td><form method="post"><input type="submit" name="button-'.$courseid.'" value="Remove"/></form></td>
                    </tr>
                    ';
                }
            }
        }

        $carttable .= '
        <tr>
        <td></td>
        <td><strong>Total</strong></td>
        <td>'.$total.'</td>
        </tr>
        ';

        $carthtml = $htmlMain.$carttable.$htmlEnd;

        $thisform->addElement('html', $carthtml);

        
    }
}