<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Creates a settings page for a course.
 *
 * File         course_settings.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
    defined('MOODLE_INTERNAL') || die();

// Admin Controls:
// https://docs.moodle.org/dev/Admin_settings

if ($hassiteconfig) {

    // Create settings pages
    $globalsettings = new admin_settingpage('tool_paymentplugin_gsettings', get_string('gsettings', 'tool_paymentplugin'));

    // Create a category in the admin tree
    $paymentplugincat = new admin_category('tool_paymentplugin_folder', get_string('pluginname', 'tool_paymentplugin'), false);
    $paymentplugincat->add('tool_paymentplugin_folder', $globalsettings);

    // Add the category to the tree
    $ADMIN->add('tools', $paymentplugincat);


    // Create settings
    $heading = new admin_setting_heading('tool_paymentplugin_gsettings/heading', $globalsettings->visiblename,
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

    $entryfield = new admin_setting_configtext('tool_paymentplugin_test_entryfield', 'Entry Field:', 'The box below will hold this value', '');
    if ($entryfield->get_setting() == '') {
        $entryfield->write_setting("For Example...");
    }

    $resultbox = new admin_setting_configtextarea('tool_paymentplugin_test_result', 'Result: ', '', '');
    // $resultbox->nosave = false; // Dont save settings results
    $resultbox->write_setting('Got result from above field. "'.$entryfield->get_setting()
    .'" | "'.get_config('tool_paymentplugin', 'tool_paymentplugin_test_entryfield').'" | "'.$CFG->tool_paymentplugin_test_entryfield.'"');
    // echo ;
    $textboxnumbersonly = new admin_setting_configtext_with_maxlength('tool_paymentplugin_gsettings/text1', get_string('gsettingstext1', 'tool_paymentplugin'),
        get_string('gsettingstext1desc', 'tool_paymentplugin'), '', PARAM_INT, 1, 3);

    $textboxtextonly = new admin_setting_configtext_with_maxlength('tool_paymentplugin_gsettings', get_string('gsettingstext2', 'tool_paymentplugin'),
        get_string('gsettingstext2desc', 'tool_paymentplugin'), '', PARAM_TEXT, 10, 20);

    $textboxemailonly = new admin_setting_configtext_with_maxlength('tool_paymentplugin_gsettings/text3', get_string('gsettingstext3', 'tool_paymentplugin'),
        get_string('gsettingstext3desc', 'tool_paymentplugin'), '', PARAM_EMAIL, 0, 70);


    // Add settings
    $globalsettings->add($heading);
    $globalsettings->add($disableallcheck);
    $globalsettings->add($checkbox2);
    $globalsettings->add($multiselect);
    $globalsettings->add($entryfield);
    $globalsettings->add($resultbox);
    $globalsettings->add($textboxnumbersonly);
    $globalsettings->add($textboxtextonly);
    $globalsettings->add($textboxemailonly);

    foreach (core_plugin_manager::instance()->get_plugins_of_type('paymentgateway') as $plugin) {
        $plugin->load_settings($ADMIN, 'tool_paymentplugin_folder', $hassiteconfig);
    }

}
