<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

    class payment_settings_form extends moodleform { 
        protected $course;
        protected $context;

        function definition()   {
            $course        = $this->_customdata['course'];
            // $category      = $this->_customdata['category'];
            $returnto      = $this->_customdata['returnto'];

            $thisform = $this->_form;

            $thisform->addElement('text', 'coursecost', 'Course Cost');
            $thisform->setType('coursecost', PARAM_INT);

            // Copied from edit_form.php
            $buttonarray = array();
            $classarray = array('class' => 'form-submit');
            if ($returnto !== 0) {
                $buttonarray[] = &$thisform->createElement('submit', 'saveandreturn', get_string('savechangesandreturn'), $classarray);
            }
            $buttonarray[] = &$thisform->createElement('submit', 'saveanddisplay', get_string('savechangesanddisplay'), $classarray);
            $buttonarray[] = &$thisform->createElement('cancel');
            $thisform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
            $thisform->closeHeaderBefore('buttonar');

            //  $this->set_data($course); // What this do?
        }


    }