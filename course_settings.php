<?php

        // Load moodle
        require_once(__DIR__.'/../../../config.php');

        // Get course id
        $courseid = optional_param('id', 0, PARAM_INT);

        // Login checks
        require_login($courseid, true);
        if (isguestuser())      {
                throw new require_login_exception('Guests are not permitted to access this page.');
        }

        $PAGE->set_url('/admin/tool/paymentplugin/course_settings.php');
        $PAGE->set_pagelayout('admin');
        $PAGE->set_context($context);
        $PAGE->set_title("Testng?"/*get_string('coursesettings_management:title', 'tool_paymentplugin')*/);
        $PAGE->set_cacheable(false); // Look this up

        //if ($node = $PAGE->settingsnav->find())

        //$OUTPUT = $PAGE->get_renderer('tool_paymentplugin'); // Do I need to make this renderer, or can it be avoided?


        echo $OUTPUT->header();

        // Settings

        echo $OUTPUT->footer();