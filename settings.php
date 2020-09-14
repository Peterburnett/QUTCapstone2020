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
 * Creates admin settings page for plugin.
 *
 * @package     tool_paymentplugin
 * @author      Mitchell Halpin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_paymentplugin\plugininfo\paymentgateway;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $category = optional_param('category', '', PARAM_STRINGID);

    // Page setup.
    $globalsettings = new admin_settingpage('tool_paymentplugin_settings', 
        get_string('adminsettingsheading', 'tool_paymentplugin'));
    $paymentplugincat = new admin_category('tool_paymentplugin_folder', 
        get_string('pluginname', 'tool_paymentplugin'), false);
    $paymentplugincat->add('tool_paymentplugin_folder', $globalsettings);
    $ADMIN->add('tools', $paymentplugincat);

    // Page Settings.
    $gateways = paymentgateway::get_all_gateway_objects();

    $globalsettings->add(new admin_setting_heading('tool_paymentplugin_settings/heading',
        get_string('gatewaylist:heading', 'tool_paymentplugin'),
        get_string('gatewaylist:desc', 'tool_paymentplugin',
        ['installed' => count($gateways), 'enabled' => count(paymentgateway::get_all_enabled_gateway_objects())])));

    $globalsettings->add(new admin_setting_configcheckbox('tool_paymentplugin_settings/disableall',
        get_string('gatewaydisableall:text', 'tool_paymentplugin'), '', 0));
    if ($category == '') {
        foreach ($gateways as $gateway) {
            $globalsettings->add(new admin_setting_configcheckbox('paymentgateway_'.$gateway->name.'/enabled',
                get_string('gatewayenable:text', 'tool_paymentplugin', $gateway->get_display_name_appended()), '', 0));
        }
    }

    // Fetch Plugin Settings.
    foreach (core_plugin_manager::instance()->get_plugins_of_type('paymentgateway') as $plugin) {
        $plugin->load_settings($ADMIN, 'tool_paymentplugin_folder', $hassiteconfig);
    }
}
