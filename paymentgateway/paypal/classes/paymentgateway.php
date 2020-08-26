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
 * Class for a specific payment gateway object.
 *
 * File         paymentgateway.php
 * Encoding     UTF-8
 *
 * @package     paymentgateway_paypal
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace paymentgateway_paypal;

defined ('MOODLE_INTERNAL') || die();

class paymentgateway extends \tool_paymentplugin\paymentgateway\object_paymentgateway {
    public function payment_button($courseid) {
        global $CFG, $USER, $DB;

        // Get IDs from subplugin settings.
        $sandboxid = 'Ac77CRgg9lq_gvxT2dmf9DryDowLdBCwMafuVLDgdLHfHyYgF5kgSlG-uWziX9RgJ8yhB5ZYCWIbEsQl';
        $productionid = 'placeholdertext';
        // Make sure to add $CFG->usepaypalsandbox = 1; to config if only testing.
        $clientid = empty($CFG->usepaypalsandbox) ? $productionid : $sandboxid;

        // Get button display options from subplugin settings.
        $buttonsize      = 'small';
        $buttoncolour    = 'gold';
        $buttonshape     = 'pill';

        // Get course price and currency from course settings.
        $amount          = '0.01';
        $currency        = 'USD';

        // Get various info.
        $course          = $DB->get_record('course', array('id' => $courseid));
        $context         = \context_course::instance($course->id);
        $coursefullname  = format_string($course->fullname, true, array('context' => $context));
        $userfirstname   = $USER->firstname;
        $userlastname    = $USER->lastname;
        $useremail       = $USER->email;

        // Custom parameter that holds user ID and course ID for the IPN page to read.
        $custom          = $USER->id . '-' . $course->id;

        
}
}
