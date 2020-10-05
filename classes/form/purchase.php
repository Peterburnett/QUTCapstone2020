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
 * Course Purchase Form.
 *
 * @package     tool_paymentplugin
 * @author      Haruki Nakagawa
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace tool_paymentplugin\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
use tool_paymentplugin\plugininfo\paymentgateway;

class purchase extends \moodleform {

    /**
     * Creates the form for course purchases.
     *
     * @return void
     */
    public function definition() {
        global $DB;

        $thisform = $this->_form;
        $courseid = $this->_customdata['id'];

        $html = \html_writer::start_div('tool_paymentplugin purchase_buttons');
        $paymentgateways = paymentgateway::get_all_enabled_gateway_objects();
        foreach ($paymentgateways as $paymentgateway) {
            $html .= $paymentgateway->payment_button($courseid);
        }
        $html .= \html_writer::end_div();

        $thisform->addElement('html', $html);
    }
}
