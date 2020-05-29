<?php

    defined('MOODLE_INTERNAL') || die();

    /**
     * @param navigation_node $navigation
     */
    function tool_paymentplugin_extend_course_settings($navigation)    {


        // Ref:
        // https://github.com/catalyst/moodle-tool_mfa/blob/master/lib.php
        //
        // Ask what these if statements are for
        // what is $navigation?
        // where to call this from?
        // etc

        $url = new moodle_url('/admin/tool/paymentplugin/course_settings.php');
        $node = navigation_node::create('text?', $url, navigation_node::TYPE_SETTING);
        $usernode = $navigation->find('useraccount', navigation_node::TYPE_CONTAINER)
        $usernode->add_node($node);


    }

