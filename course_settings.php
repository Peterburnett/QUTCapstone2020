<?php
        // Load moodle

use core\session\exception;

require_once(__DIR__.'/../../../config.php');
        require_once('payment_settings_form.php');

        // Login check
        $courseid = optional_param('id', 0, PARAM_INT);

        if (empty($courseid))  {
                throw new moodle_exception('No valid course id detected.');
        }

        $course = get_course($courseid);
        require_login($courseid, true);
        if (isguestuser())      {
                throw new require_login_exception('Guests are not permitted to access this page.');
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

        // The settings
        $args = array(
                'course' => $course,
                'id' => $courseid,
                );
        $paymentform = new payment_settings_form(new moodle_url('/admin/tool/paymentplugin/course_settings.php', array('id'=>$courseid)), $args);

        if ($paymentform->is_cancelled())       {
                // Cancel
        }
        else if ($formdata = $paymentform->get_data())      {
                $tablename = 'tool_paymentplugin_course';
                $cost = $formdata->coursecost;
                
                if ($DB->record_exists($tablename, ['courseid' => $courseid]))   {
                        $record = $DB->get_record($tablename, ['courseid' => $courseid]);
                        $record->cost = $cost;
                        $DB->update_record($tablename, $record);
                }
                else    {
                        $record = (object) array('courseid' => $courseid, 'cost' => $cost);
                        $DB->insert_record($tablename, $record);
                }
        } 
        // Display the form
        $paymentform->display();

        echo $OUTPUT->footer();
