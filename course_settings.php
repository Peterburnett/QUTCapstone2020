<?php
        // Load moodle
        require_once(__DIR__.'/../../../config.php');
        require_once('payment_settings_form.php');

        // Login check
        $courseid = optional_param('id', 0, PARAM_INT);
        $returnto = optional_param('returnto', 0, PARAM_ALPHANUM); // Not implemented
        $returnurl = optional_param('returnurl', '', PARAM_LOCALURL); // Not implemented

        if ($courseid)  {
                $course = get_course($courseid);
                require_login($courseid, true);
                if (isguestuser())      {
                        throw new require_login_exception('Guests are not permitted to access this page.');
                }
        }
        else{
                // Throw error, I guess?
        }

        // Setup Page
        $title = get_string('coursesettings_management:title', 'tool_paymentplugin');
        $PAGE->set_url('/admin/tool/paymentplugin/course_settings.php');
        $PAGE->set_pagelayout('admin'); // What this do?
        $PAGE->set_context(context_course::instance($courseid));
        $PAGE->set_cacheable(false); // What this do?

        $PAGE->set_heading($title);
        $PAGE->navbar->add($title, new moodle_url('/admin/tool/paymentplugin/course_settings.php'));

        // Display Page
        echo $OUTPUT->header();

        if ($courseid)        {
                $args = array(
                        'course' => $course,
                        // 'category' => $category,
                        // 'editoroptions' => $editoroptions,
                        'returnto' => $returnto
                        // 'returnurl' => $returnurl
                    );
                $paymentform = new payment_settings_form(null, $args);
                $paymentform->display();
        }

        echo $OUTPUT->footer();
