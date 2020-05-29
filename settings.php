<?php

    defined('MOODLE_INTERNAL') || die();

    // Admin Controls:
    // https://docs.moodle.org/dev/Admin_settings

    if ($hassiteconfig) {

        // Create new settings cataegory/folder for the plugin
        $ADMIN->add('tools', new admin_category('tool_paymentplugin_folder', get_string('pluginname', 'tool_paymentplugin'), false));

        // Add settings page A to the folder
        $global_settingsA = new admin_settingpage('tool_paymentplugin_GsettingsA', get_string('GsettingsA', 'tool_paymentplugin'));
        $ADMIN->add('tool_paymentplugin_folder', $global_settingsA);

        // Add settings page B to the folder
        $global_settingsB = new admin_settingpage('tool_paymentplugin_globalsettingsB', get_string('GsettingsB', 'tool_paymentplugin'));
        $ADMIN->add('tool_paymentplugin_folder', $global_settingsB);

        // Add settings page C to the folder
        $global_settingsC = new admin_settingpage('tool_paymentplugin_globalsettingsC', get_string('GsettingsC', 'tool_paymentplugin'));
        $ADMIN->add('tool_paymentplugin_folder', $global_settingsC);

        /**
         * 
         * Global Settings Page A Setup
         * 
         * 
         */
        // Add Heading
        $global_settingsA->add(new admin_setting_heading('tool_paymentplugin_GsettingsA/heading', get_string('GsettingsA_header', 'tool_paymentplugin'), 
            get_string('GsettingsA_desc', 'tool_paymentplugin')));

        // Add checkboxes
        $global_settingsA->add(new admin_setting_configcheckbox('tool_paymentplugin_GsettingsA/disablePurchases', get_string('GsettingsA_disablePurchase', 'tool_paymentplugin'), 
            get_string('GsettingsA_disablePurchase_desc', 'tool_paymentplugin'), 0));   
        $global_settingsA->add(new admin_setting_configcheckbox('tool_paymentplugin_GsettingsA/checkbox2', get_string('GsettingsA_check2', 'tool_paymentplugin'), 
            get_string('GsettingsA_check2_desc', 'tool_paymentplugin'), 0));
        
        // Add multi select
        $exampleselections = [
            'Option A' => get_string('GsettingsA_multi1_selectionA', 'tool_paymentplugin'),
            'Option B' => get_string('GsettingsA_multi1_selectionB', 'tool_paymentplugin'),
            'Option C' => get_string('GsettingsA_multi1_selectionC', 'tool_paymentplugin')
        ];
        $global_settingsA->add(new admin_setting_configmultiselect('tool_paymentplugin_GsettingsA/multi1', get_string('GsettingsA_multi1', 'tool_paymentplugin'), 
            get_string('GsettingsA_multi1_desc', 'tool_paymentplugin'), [], $exampleselections));
        

        // Setting value access test/example
        $entryField = new admin_setting_configtext('tool_paymentplugin_test_entryfield', 'Entry Field:', 'The box below will hold this value', '');
        if ($entryField->get_setting() == '') $entryField->write_setting("For Example...");
        $global_settingsA->add($entryField);
        $resultBox = new admin_setting_configtextarea('tool_paymentplugin_test_result', 'Result: ', '', '');
        // Do not save the contents of this box
        $resultBox->$nosave = true;
        $resultBox->write_setting('Got result from above field: "'.$entryField->get_setting().'" and here it is again, but it was gathered in a different way: "'.$CFG->tool_paymentplugin_test_entryfield.'"');
        $global_settingsA->add($resultBox);

        // Add int textbox
        $global_settingsA->add(new admin_setting_configtext_with_maxlength('tool_paymentplugin_GsettingsA/text1', get_string('GsettingsA_text1', 'tool_paymentplugin'), 
            /*get_string('GsettingsA_text1_desc', 'tool_paymentplugin')*/ $CFG->tool_paymentplugin_GsettingsA_text2, '', PARAM_INT, 1, 3));

        // Add text textbox
        $global_settingsA->add(new admin_setting_configtext_with_maxlength('tool_paymentplugin_GsettingsA_text2', get_string('GsettingsA_text2', 'tool_paymentplugin'), 
            get_string('GsettingsA_text2_desc', 'tool_paymentplugin'), '', PARAM_TEXT, 10, 20));

        // Add email textbox
        $global_settingsA->add(new admin_setting_configtext_with_maxlength('tool_paymentplugin_GsettingsA/text3', get_string('GsettingsA_text3', 'tool_paymentplugin'), 
            get_string('GsettingsA_text3_desc', 'tool_paymentplugin'), '', PARAM_EMAIL, 0, 70));
        
    }
