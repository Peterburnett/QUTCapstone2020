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
 * File containing functions and constants for the payment plugin module
 *
 * File         lib.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    defined('MOODLE_INTERNAL') || die();

    /**
     * @param navigation_node $navigation
     * @see lib\navigationlib.php
     * @see https://docs.moodle.org/dev/Navigation_API
     */
    function tool_paymentplugin_extend_navigation_course($navigation, $course, $coursecontext)    {
        // Add new navigation node to the 'courseadmin' node.
        // 'couseadmin' is where this function is called from so we dont need to find it via:
        // $coursenode = $navigation->find('courseadmin', navigation_node::TYPE_COURSE);

        // NOTE: Better suited location may be in more->users->Enrolment methods. Something to consider.

        if (has_capability('moodle/course:create', $coursecontext)) {
            $coursenode = $navigation;

            $containernode = navigation_node::create(get_string('coursesettings:title', 'tool_paymentplugin'), null, navigation_node::TYPE_CONTAINER);
            $coursenode->add_node($containernode);

            $url = new moodle_url('/admin/tool/paymentplugin/course_settings.php', array('id'=>$course->id));
            $settingnode = navigation_node::create(get_string('coursesettings_management:title', 'tool_paymentplugin'), $url, navigation_node::TYPE_SETTING);
            $containernode->add_node($settingnode);
        }
    }
