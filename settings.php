<?php

    defined('MOODLE_INTERNAL') || die();

    // Admin Controls:
    // https://docs.moodle.org/dev/Admin_settings

    if ($hassiteconfig) {

        // Create settings pages
        $global_settingsA = new admin_settingpage('tool_paymentplugin_GsettingsA', get_string('GsettingsA', 'tool_paymentplugin'));
        $global_settingsB = new admin_settingpage('tool_paymentplugin_globalsettingsB', get_string('GsettingsB', 'tool_paymentplugin'));
        $global_settingsC = new admin_settingpage('tool_paymentplugin_globalsettingsC', get_string('GsettingsC', 'tool_paymentplugin'));

        // Create a category in the admin tree
        $paymentplugincat = new admin_category('tool_paymentplugin_folder', get_string('pluginname', 'tool_paymentplugin'), false);
        $paymentplugincat->add('tool_paymentplugin_folder', $global_settingsA);
        $paymentplugincat->add('tool_paymentplugin_folder', $global_settingsB);
        $paymentplugincat->add('tool_paymentplugin_folder', $global_settingsC);
        // Add the category to the tree
        $ADMIN->add('tools', $paymentplugincat);


        // Create settings
        $heading = new admin_setting_heading('tool_paymentplugin_GsettingsA/heading', $global_settingsA->visiblename, 
            get_string('GsettingsA_desc', 'tool_paymentplugin'));

        $disableallcheck = new admin_setting_configcheckbox('tool_paymentplugin_GsettingsA/disablePurchases', get_string('GsettingsA_disablePurchase', 'tool_paymentplugin'), 
            get_string('GsettingsA_disablePurchase_desc', 'tool_paymentplugin'), 0);

        $checkbox2 = new admin_setting_configcheckbox('tool_paymentplugin_GsettingsA/checkbox2', get_string('GsettingsA_check2', 'tool_paymentplugin'), 
            get_string('GsettingsA_check2_desc', 'tool_paymentplugin'), 0);

        $exampleselections = [
                'Option A' => get_string('GsettingsA_multi1_selectionA', 'tool_paymentplugin'),
                'Option B' => get_string('GsettingsA_multi1_selectionB', 'tool_paymentplugin'),
                'Option C' => get_string('GsettingsA_multi1_selectionC', 'tool_paymentplugin')
            ];
        $multiselect = new admin_setting_configmultiselect('tool_paymentplugin_GsettingsA/multi1', get_string('GsettingsA_multi1', 'tool_paymentplugin'), 
            get_string('GsettingsA_multi1_desc', 'tool_paymentplugin'), [], $exampleselections);

        $entryField = new admin_setting_configtext('tool_paymentplugin_test_entryfield', 'Entry Field:', 'The box below will hold this value', '');
        if ($entryField->get_setting() == '') $entryField->write_setting("For Example...");

        $resultBox = new admin_setting_configtextarea('tool_paymentplugin_test_result', 'Result: ', '', '');
        $resultBox->nosave = true; // Dont save settings results
        $resultBox->write_setting('Got result from above field: "'.$entryField->get_setting().'" and here it is again, but it was gathered in a different way: "'.$CFG->tool_paymentplugin_test_entryfield.'"');
        
        $textboxnumbersonly = new admin_setting_configtext_with_maxlength('tool_paymentplugin_GsettingsA/text1', get_string('GsettingsA_text1', 'tool_paymentplugin'), 
            get_string('GsettingsA_text1_desc', 'tool_paymentplugin'), '', PARAM_INT, 1, 3);
        
        $textboxtextonly = new admin_setting_configtext_with_maxlength('tool_paymentplugin_GsettingsA_text2', get_string('GsettingsA_text2', 'tool_paymentplugin'), 
            get_string('GsettingsA_text2_desc', 'tool_paymentplugin'), '', PARAM_TEXT, 10, 20);

        $textboxemailonly = new admin_setting_configtext_with_maxlength('tool_paymentplugin_GsettingsA/text3', get_string('GsettingsA_text3', 'tool_paymentplugin'), 
            get_string('GsettingsA_text3_desc', 'tool_paymentplugin'), '', PARAM_EMAIL, 0, 70);
        

        // Add settings
        $global_settingsA->add($heading);
        $global_settingsA->add($disableallcheck);   
        $global_settingsA->add($checkbox2);
        $global_settingsA->add($multiselect);
        $global_settingsA->add($entryField);
        $global_settingsA->add($resultBox);
        $global_settingsA->add($textboxnumbersonly);
        $global_settingsA->add($textboxtextonly);
        $global_settingsA->add($textboxemailonly);
        
    }
