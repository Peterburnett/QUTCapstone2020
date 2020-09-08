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

 * Settings page for paypal subplugin.

 * Lang EN file for tool_paymentplugin.

 *
 * File         settings.php
 * Encoding     UTF-8
 *
 * @package     paymentgateway_paypal
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

 */


defined('MOODLE_INTERNAL') || die();
// Create Settings heading.
$settings->add(new admin_setting_heading('paymentgateway_paypal/header', get_string('ssettings', 'paymentgateway_paypal'),
    get_string('ssettingsdesc', 'paymentgateway_paypal')));

// Create Enable/Disable button.
$settings->add(new admin_setting_configcheckbox('paymentgateway_paypal/enabled',
    get_string('settingsdisablepurchase', 'tool_paymentplugin'),
    get_string('settingsdisablepurchasedesc', 'tool_paymentplugin'), 0));

// Create Client ID textbox.
$settings->add(new admin_setting_configtext('paymentgateway_paypal/clientid',
    get_string('ssettingsclientid', 'paymentgateway_paypal'),
    get_string('ssettingsclientdesc', 'paymentgateway_paypal'), ''));

// Create Currency array.
$currencyarray = [
    'USD' => get_string('ssettingscurrencyUSD', 'paymentgateway_paypal'),
    'AUD' => get_string('ssettingscurrencyAUD', 'paymentgateway_paypal')
];

// Create Currency dropdown box.
$settings->add(new admin_setting_configselect('paymentgateway_paypal/currency',
    get_string('ssettingscurrencybox', 'paymentgateway_paypal'),
    get_string('ssettingscurrencydesc', 'paymentgateway_paypal'), 'USD', $currencyarray));

// Create Colour array.
$colourarray = [
    'gold' => get_string('ssettingscolourgold', 'paymentgateway_paypal'),
    'blue' => get_string('ssettingscolourblue', 'paymentgateway_paypal'),
    'silver' => get_string('ssettingscoloursilver', 'paymentgateway_paypal'),
    'white' => get_string('ssettingscolourwhite', 'paymentgateway_paypal'),
    'black' => get_string('ssettingscolourblack', 'paymentgateway_paypal')
];

// Create Colour dropdown box: [gold, blue, silver, white, black].
$settings->add(new admin_setting_configselect('paymentgateway_paypal/colour',
    get_string('ssettingscolourbox', 'paymentgateway_paypal'),
    get_string('ssettingscolourdesc', 'paymentgateway_paypal'), 'gold', $colourarray));

// Create Shape array.
$shapearray = [
    'rect' => get_string('ssettingsshaperectangle', 'paymentgateway_paypal'),
    'pill' => get_string('ssettingsshapepill', 'paymentgateway_paypal')
];

// Create Shape dropdown box: [rectangle, pill].
$settings->add(new admin_setting_configselect('paymentgateway_paypal/shape',
    get_string('ssettingsshapebox', 'paymentgateway_paypal'),
    get_string('ssettingsshapedesc', 'paymentgateway_paypal'), 'rect', $shapearray));
