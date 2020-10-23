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
 * Declaration of web service functions for tool_paymentplugin.
 *
 * @package   tool_paymentplugin
 * @author    Haruki Nakagawa
 * @copyright 2020 MAHQ
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

$functions = array(
    'tool_paymentplugin_check_enrolled' => array(
        'classname'     => 'tool_paymentplugin\external\external',
        'methodname'    => 'check_enrolled',
        'classpath'     => 'admin/tool/paymentplugin/classes/external/external.php',
        'description'   => 'Checks database to see if a user is enrolled in a course.',
        'type'          => 'read',
        'ajax'          => true
    )
);
