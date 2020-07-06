<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

    class payment_settings_form extends moodleform { 
        protected $course;
        protected $context;

        function definition()   {
            $course        = $this->_customdata['course'];
            // $category      = $this->_customdata['category'];

            $thisform = $this->_form;

            $thisform->addElement('text', 'coursecost', 'Course Cost');
            $thisform->setType('coursecost', PARAM_INT);

            // Finally set the current form data
            $this->set_data($course);
        }


    }