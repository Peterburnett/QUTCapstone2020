<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

    class payment_settings_form extends moodleform { 

        function definition()   {
            global $DB;
            
            $thisform = $this->_form;
            $courseid = $this->_customdata['id'];

            $thisform->addElement('text', 'coursecost', 'Course Cost');
            $thisform->setType('coursecost', PARAM_INT);

            $tablename = 'tool_paymentplugin_course';
            $cost = 0;
            
            if ($DB->record_exists($tablename, ['courseid' => $courseid]))   {
                $record = $DB->get_record($tablename, ['courseid' => $courseid]);
                $cost = $record->cost;
            }

            $thisform->setDefault('coursecost', $cost);

            // Need to add course id to url somehow
            $this->add_action_buttons(true);
        }

    } 
    // DB->insert_record(); // https://docs.moodle.org/dev/Data_manipulation_API
    // https://docs.moodle.org/dev/XMLDB_editor
    // https://docs.moodle.org/dev/Upgrade_API#install.php
