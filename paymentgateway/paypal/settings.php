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
//Crete Settings heading
$settings->add(new admin_setting_heading('paymentgateway_paypal/header', get_string('ssettings', 'paymentgateway_paypal'), get_string('ssettingsdesc', 'paymentgateway_paypal')));

// Create Client ID textbox
$settings->add(new admin_setting_configtext('paymentgateway_paypal/textbox', get_string('ssettingsclientid', 'paymentgateway_paypal'), 
    get_string('ssettingsclientdesc', 'paymentgateway_paypal'), ''));

// Create Currency array
$currencyarray = [
    'Option 1' => get_string('ssettingscurrencyoption1', 'paymentgateway_paypal'),
    'Option 2' => get_string('ssettingscurrencyoption2', 'paymentgateway_paypal')
];

// Create Currency dropdown box
$settings->add(new admin_setting_configselect('paymentgateway_paypal/dropdownbox1', get_string('ssettingscurrencybox', 'paymentgateway_paypal'),
    get_string('ssettingscurrencydesc', 'paymentgateway_paypal'), 'Option 1', $currencyarray));

// Create Colour array
$colourarray = [
    'Option 1' => get_string('ssettingscolouroption1', 'paymentgateway_paypal'),
    'Option 2' => get_string('ssettingscolouroption2', 'paymentgateway_paypal'),
    'Option 3' => get_string('ssettingscolouroption3', 'paymentgateway_paypal'),
    'Option 4' => get_string('ssettingscolouroption4', 'paymentgateway_paypal'),
    'Option 5' => get_string('ssettingscolouroption5', 'paymentgateway_paypal')
];

// Create Colour dropdown box: [gold, blue, silver, white, black]
$settings->add(new admin_setting_configselect('paymentgateway_paypal/dropdownbox2', get_string('ssettingscolourbox', 'paymentgateway_paypal'),
    get_string('ssettingscolourdesc', 'paymentgateway_paypal'), 'Option 1', $colourarray));

// Create Shape array
$shapearray = [
    'Option 1' => get_string('ssettingsshapeoption1', 'paymentgateway_paypal'),
    'Option 2' => get_string('ssettingsshapeoption2', 'paymentgateway_paypal')
];

// Create Shape dropdown box: [rectangle, pill]
$settings->add(new admin_setting_configselect('paymentgateway_paypal/dropdownbox3', get_string('ssettingsshapebox', 'paymentgateway_paypal'),
    get_string('ssettingsshapedesc', 'paymentgateway_paypal'), 'Option 1', $shapearray));
