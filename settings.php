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

if ($hassiteconfig) {

    // Create settings pages
    $globalsettings = new admin_settingpage('tool_paymentplugin_gsettings', get_string('gsettings', 'tool_paymentplugin'));
    $ADMIN->add('tools', $globalsettings);

    // Create settings
    $disableallcheck = new admin_setting_configcheckbox('tool_paymentplugin_gsettings/disablePurchases', get_string('gsettingsdisablepurchase', 'tool_paymentplugin'),
        get_string('gsettingsdisablepurchasedesc', 'tool_paymentplugin'), 0);
    
    $installedgateways = array();
    $gateways = paymentgateway::get_gateway_objects();
    $installedgateways[] = sizeof($gateways);
    foreach ($gateways as $gateway)    {
        $installedgateways[] = $gateway->name;
    }

    $multiselect = new admin_setting_configmultiselect('tool_paymentplugin_gsettings/multi1', get_string('gsettingsmulti1', 'tool_paymentplugin'),
        '', [], $installedgateways);

    $entryfield = new admin_setting_configtext('tool_paymentplugin_test_entryfield', 'Entry Field:', 'The box below will hold this value', '');
    if ($entryfield->get_setting() == '') {
        $entryfield->write_setting("For Example...");
    }

    $globalsettings->add($disableallcheck);
    $globalsettings->add($multiselect);
}