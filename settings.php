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

use tool_paymentplugin\plugininfo\paymentgateway;

defined('MOODLE_INTERNAL') || die();

$category = optional_param('category', '', PARAM_STRINGID);

if ($hassiteconfig) {

    // Create settings pages
    $globalsettings = new admin_settingpage('tool_paymentplugin_gsettings', get_string('gsettings', 'tool_paymentplugin'));

    // Create a category in the admin tree
    $paymentplugincat = new admin_category('tool_paymentplugin_folder', get_string('pluginname', 'tool_paymentplugin'), false);
    $paymentplugincat->add('tool_paymentplugin_folder', $globalsettings);

    // Add the category to the tree
    $ADMIN->add('tools', $paymentplugincat);
  
    // Sub Plugin Enabled/Disabled Settings
    // Create Configs
    $gateways = paymentgateway::get_all_gateway_objects();

    $globalsettings->add(new admin_setting_heading('tool_paymentplugin_subsettings/heading', get_string('tool_paymentplugin_subsettings/heading', 'tool_paymentplugin'),
        count($gateways).get_string('tool_paymentplugin_subsettings/headingdesc', 'tool_paymentplugin').' '.
        count(paymentgateway::get_all_enabled_gateway_objects()).' '.get_string('tool_paymentplugin_subsettings/headingdesc2', 'tool_paymentplugin')));

    $globalsettings->add(new admin_setting_configcheckbox('tool_paymentplugin_gsettings/disablePurchases', get_string('gsettingsdisableallpurchase', 'tool_paymentplugin'),
        '', 0));
    if ($category == '') {
        foreach ($gateways as $gateway) {
            $globalsettings->add(new admin_setting_configcheckbox('paymentgateway_'.$gateway->name.'/enabled', get_string('settingsdisablepurchase', 'tool_paymentplugin').' '.
            $gateway->get_readable_name(), '', 0));
        }
    }

    // Sub Plugin Settings
    foreach (core_plugin_manager::instance()->get_plugins_of_type('paymentgateway') as $plugin) {
        $plugin->load_settings($ADMIN, 'tool_paymentplugin_folder', $hassiteconfig);
    }
}
