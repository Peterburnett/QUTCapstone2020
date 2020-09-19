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

 * Creates a settings page for paypal subplugin.
 *
 * File         settings.php
 * Encoding     UTF-8
 *
 * @package     paymentgateway_paypal
 * @author      Quyen Nguyen
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Page Heading.
$settings->add(new admin_setting_heading('paymentgateway_paypal/header', get_string('settings:heading', 'paymentgateway_paypal'),
    get_string('settings:description', 'paymentgateway_paypal')));

// Enable/Disable button.
$settings->add(new admin_setting_configcheckbox('paymentgateway_paypal/enabled',
    get_string('gatewayenable:text', 'tool_paymentplugin', get_string('pluginname', 'paymentgateway_paypal')), '', 0));

// Client ID textbox.
$settings->add(new admin_setting_configtext('paymentgateway_paypal/clientid',
    get_string('settings:clientid', 'paymentgateway_paypal'),
    get_string('settings:clientdesc', 'paymentgateway_paypal'), ''));

// Colour dropdown box: [gold, blue, silver, white, black].
$colourarray = [
    'gold' => get_string('settings:colourgold', 'paymentgateway_paypal'),
    'blue' => get_string('settings:colourblue', 'paymentgateway_paypal'),
    'silver' => get_string('settings:coloursilver', 'paymentgateway_paypal'),
    'white' => get_string('settings:colourwhite', 'paymentgateway_paypal'),
    'black' => get_string('settings:colourblack', 'paymentgateway_paypal')
];
$settings->add(new admin_setting_configselect('paymentgateway_paypal/colour',
    get_string('settings:colour', 'paymentgateway_paypal'),
    get_string('settings:colourdesc', 'paymentgateway_paypal'), 'gold', $colourarray));

// Shape dropdown box: [rectangle, pill].
$shapearray = [
    'rect' => get_string('settings:shaperectangle', 'paymentgateway_paypal'),
    'pill' => get_string('settings:shapepill', 'paymentgateway_paypal')
];
$settings->add(new admin_setting_configselect('paymentgateway_paypal/shape',
    get_string('settings:shape', 'paymentgateway_paypal'),
    get_string('settings:shapedesc', 'paymentgateway_paypal'), 'rect', $shapearray));