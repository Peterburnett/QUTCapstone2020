<?php

    defined('MOODLE_INTERNAL') || die();

    // Admin Controls:
    // https://docs.moodle.org/dev/Admin_settings

    if ($hassiteconfig) {

        // Create new settings cataegory/folder for the plugin
        $ADMIN->add('tools', new admin_category('tool_paymentplugin_folder', get_string('pluginname', 'tool_paymentplugin'), false));

        // Add settings page A to the folder
        $global_settings = new admin_settingpage('tool_paymentplugin_globalsettings', get_string('gsettings', 'tool_paymentplugin'));
        $ADMIN->add('tool_paymentplugin_folder', $global_settings);

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
        $global_settings->add(new admin_setting_heading('tool_paymentplugin_globalsettings/heading', get_string('gsettingsheader', 'tool_paymentplugin'), 
            get_string('gsettingsdesc', 'tool_paymentplugin')));

        // Add checkboxes
        $global_settings->add(new admin_setting_configcheckbox('tool_paymentplugin_globalsettings/disablePurchases', get_string('gsettingsdisablepurchase', 'tool_paymentplugin'), 
            get_string('gsettingsdisablepurchasedesc', 'tool_paymentplugin'), 0));   
        $global_settings->add(new admin_setting_configcheckbox('tool_paymentplugin_globalsettings/checkbox2', get_string('gsettingscheck2', 'tool_paymentplugin'), 
            get_string('gsettingscheck2desc', 'tool_paymentplugin'), 0));
        
        // Add multi select
        $exampleselections = [
            'Option A' => get_string('gsettingsmulti1selectionA', 'tool_paymentplugin'),
            'Option B' => get_string('gsettingsmulti1selectionB', 'tool_paymentplugin'),
            'Option C' => get_string('gsettingsmulti1selectionC', 'tool_paymentplugin')
        ];
        $global_settings->add(new admin_setting_configmultiselect('tool_paymentplugin_globalsettings/multi1', get_string('gsettingsmulti1', 'tool_paymentplugin'), 
            get_string('gsettingsmulti1desc', 'tool_paymentplugin'), [], $exampleselections));
        
        // Add int textbox
        $global_settings->add(new admin_setting_configtext_with_maxlength('tool_paymentplugin_globalsettings/text1', get_string('gsettingstext1', 'tool_paymentplugin'), 
            get_string('gsettingstext1desc', 'tool_paymentplugin'), '', PARAM_INT, 1, 3));

        // Add text textbox
        $global_settings->add(new admin_setting_configtext_with_maxlength('tool_paymentplugin_globalsettings/text2', get_string('gsettingstext2', 'tool_paymentplugin'), 
            get_string('gsettingstext2desc', 'tool_paymentplugin'), '', PARAM_TEXT, 10, 20));

        // Add email textbox
        $global_settings->add(new admin_setting_configtext_with_maxlength('tool_paymentplugin_globalsettings/text3', get_string('gsettingstext3', 'tool_paymentplugin'), 
            get_string('gsettingstext3desc', 'tool_paymentplugin'), '', PARAM_EMAIL, 0, 70));
        
    }
