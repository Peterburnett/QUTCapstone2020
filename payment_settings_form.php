<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

    class payment_settings_form extends moodleform { 
        // protected $course;
        // protected $context;

        function definition()   {
            $thisform = $this->_form;

            $thisform->addElement('text', 'coursecost', 'Course Cost');
            $thisform->setType('coursecost', PARAM_INT);
        }


    }