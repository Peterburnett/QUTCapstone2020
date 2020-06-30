<?php

    defined('MOODLE_INTERNAL') || die();

    // Admin Controls:
    // https://docs.moodle.org/dev/Admin_settings

    if ($hassiteconfig) {

        // Create settings pages
        $global_settingsA = new admin_settingpage('tool_paymentplugin_gsettings', get_string('gsettings', 'tool_paymentplugin'));
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
        $heading = new admin_setting_heading('tool_paymentplugin_gsettings/heading', $global_settingsA->visiblename, 
            get_string('gsettingsdesc', 'tool_paymentplugin'));

        $disableallcheck = new admin_setting_configcheckbox('tool_paymentplugin_gsettings/disablePurchases', get_string('gsettingsdisablepurchase', 'tool_paymentplugin'), 
            get_string('gsettingsdisablepurchasedesc', 'tool_paymentplugin'), 0);

        $checkbox2 = new admin_setting_configcheckbox('tool_paymentplugin_gsettings/checkbox2', get_string('gsettingscheck2', 'tool_paymentplugin'), 
            get_string('gsettingscheck2desc', 'tool_paymentplugin'), 0);

        $exampleselections = [
                'Option A' => get_string('gsettingsmulti1selectionA', 'tool_paymentplugin'),
                'Option B' => get_string('gsettingsmulti1selectionB', 'tool_paymentplugin'),
                'Option C' => get_string('gsettingsmulti1selectionC', 'tool_paymentplugin')
            ];
        $multiselect = new admin_setting_configmultiselect('tool_paymentplugin_gsettings/multi1', get_string('gsettingsmulti1', 'tool_paymentplugin'), 
            get_string('gsettingsmulti1desc', 'tool_paymentplugin'), [], $exampleselections);

        $entryField = new admin_setting_configtext('tool_paymentplugin_test_entryfield', 'Entry Field:', 'The box below will hold this value', '');
        if ($entryField->get_setting() == '') $entryField->write_setting("For Example...");

        $resultBox = new admin_setting_configtextarea('tool_paymentplugin_test_result', 'Result: ', '', '');
        // $resultBox->nosave = false; // Dont save settings results
        $resultBox->write_setting('Got result from above field. "'.$entryField->get_setting().'" | "'.get_config('tool_paymentplugin', 'tool_paymentplugin_test_entryfield').'" | "'.$CFG->tool_paymentplugin_test_entryfield.'"');
        //echo ;
        $textboxnumbersonly = new admin_setting_configtext_with_maxlength('tool_paymentplugin_gsettings/text1', get_string('gsettingstext1', 'tool_paymentplugin'), 
            get_string('gsettingstext1desc', 'tool_paymentplugin'), '', PARAM_INT, 1, 3);
        
        $textboxtextonly = new admin_setting_configtext_with_maxlength('tool_paymentplugin_gsettings', get_string('gsettingstext2', 'tool_paymentplugin'), 
            get_string('gsettingstext2desc', 'tool_paymentplugin'), '', PARAM_TEXT, 10, 20);

        $textboxemailonly = new admin_setting_configtext_with_maxlength('tool_paymentplugin_gsettings/text3', get_string('gsettingstext3', 'tool_paymentplugin'), 
            get_string('gsettingstext3desc', 'tool_paymentplugin'), '', PARAM_EMAIL, 0, 70);
        

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
