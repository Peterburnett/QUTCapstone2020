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
 * External functions for tool_paymentplugin.
 *
 * @package   tool_paymentplugin
 * @author    Haruki Nakagawa
 * @copyright 2020 MAHQ
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_paymentplugin\external;

defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_value;

require_once("$CFG->libdir/externallib.php");

class external extends external_api {

    /**
     * Returns description of check_enrolled() parameters.
     * @return external_function_parameters
     */
    public static function check_enrolled_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course'),
                'userid' => new external_value(PARAM_INT, 'id of user')
            )
        );
    }

    /**
     * Checks database to see if a user is enrolled in a course, and redirects the user if so.
     *
     * @param int $courseid
     * @param int $userid
     * @return bool
     */
    public static function check_enrolled($courseid, $userid) {
        global $DB;

        $params = self::validate_parameters(self::check_enrolled_parameters(), array(
            'courseid' => $courseid,
            'userid' => $userid
        ));

        $courseid = $params['courseid'];
        $userid = $params['userid'];

        $query = "SELECT *
                    FROM {user_enrolments}
                    JOIN {enrol} ON {user_enrolments}.enrolid = {enrol}.id
                   WHERE {enrol}.courseid = $courseid AND {user_enrolments}.userid = $userid";

        $enrolled = $DB->record_exists_sql($query);
        return $enrolled;
    }

    /**
     * Returns description of check_enrolled() return values.
     * @return external_value the value returned from the function.
     */
    public static function check_enrolled_returns() {
        return new external_value(PARAM_BOOL, 'Whether the user is enrolled in the course.');
    }
}
