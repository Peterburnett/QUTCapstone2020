<?php

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

        $coursenode = $navigation;

        $containernode = navigation_node::create(get_string('coursesettings:title', 'tool_paymentplugin'), null, navigation_node::TYPE_CONTAINER);
        $coursenode->add_node($containernode);

        $url = new moodle_url('/admin/tool/paymentplugin/course_settings.php');
        $settingnode = navigation_node::create(get_string('coursesettings_management:title', 'tool_paymentplugin'), $url, navigation_node::TYPE_SETTING);
        $containernode->add_node($settingnode);

    }
