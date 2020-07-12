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
 * A form used for payment information to be inserted into
 *
 * File         test_payment_form.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @author      Haruki Nakagawa
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_paymentplugin\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/formslib.php");

class test_payment_form extends \moodleform {
    function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'accountnumber', get_string('paymentaccountnumber', 'tool_paymentplugin'));
        $mform->setType('accountnumber', PARAM_TEXT);
        
        $mform->addElement('password', 'password', get_string('paymentpassword', 'tool_paymentplugin'));
        $mform->setType('password', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('paymentsubmit', 'tool_paymentplugin'));
    }

    //function validation() {
        
    //}
}
