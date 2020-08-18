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
// Crete Settings heading
$settings->add(new admin_setting_heading('Subplugin_settings', get_string('ssettings', 'paymentgateway_paypal'), get_string('ssettingsdesc', 'paymentgateway_paypal')));

// Create Client ID textbox
$settings->add(new admin_setting_configtext('Subplugin_settings/textbox', get_string('ssettingsclientid', 'paymentgateway_paypal'), 
    get_string('ssettingsclientdesc', 'paymentgateway_paypal'), ''));

// Create Currency array
$currencyarray = [
    'Option 1' => get_string('ssettingscurrencyoption1', 'paymentgateway_paypal'),
    'Option 2' => get_string('ssettingscurrencyoption2', 'paymentgateway_paypal'),
]
// Create Currency dropdown box
$settings->add(new admin_setting_configselect('Subplugin_settings/dropdownbox1', get_string('ssettingscurrencybox', 'paymentgateway_paypal'),
    get_string('ssettingscurrencydesc', 'paymentgateway_paypal'), [], $currencyarray));

// Create Colour dropdown box: [gold, blue, silver, white, black]

// Create Shape dropdown box: [rectangle, pill]
