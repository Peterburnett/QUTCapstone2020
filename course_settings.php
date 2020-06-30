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
        $PAGE->set_pagelayout('admin'); // What this do?
        $PAGE->set_context($context);
        $PAGE->set_title($title/*get_string('coursesettings_management:title', 'tool_paymentplugin')*/);
        $PAGE->set_cacheable(false); // Look this up

        // $PAGE->navbar->add($node->get_content(), $node->action());
        $PAGE->navbar->add($title, new moodle_url('/admin/tool/paymentplugin/coursesettings.php'));

        //if ($node = $PAGE->settingsnav->find())

        //$OUTPUT = $PAGE->get_renderer('tool_paymentplugin'); // Do I need to make this renderer, or can it be avoided?


        echo $OUTPUT->header();

        //echo $OUTPUT->heading_with_help($title, 'paymentsettings', 'payments');
        echo $OUTPUT->heading($title);

        // Settings
        echo  "Course id = ".$id;

        //if ($id != null)        {
                $paymentform = new payment_settings_form();
                $paymentform->display();
        //}

        echo $OUTPUT->footer();
