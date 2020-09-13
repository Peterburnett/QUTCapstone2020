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
 * Lang EN file for paymentgateway_paypal.
 *
 * File         paymentgateway_paypal.php
 * Encoding     UTF-8
 *
 * @package     paymentgateway_paypal
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

$string['pluginname'] = 'PayPal Payment Gateway';
$string['pluginnamebasic'] = 'PayPal';

$string['ssettings'] = 'Paypal Payment Subplugin Settings';
$string['ssettingsdesc'] = 'Settings for Paypal Payment Subplugin';

$string['ssettingsclientid'] = 'Client ID';
$string['ssettingsclientdesc'] = 'The client ID given to you by PayPal.
If invalid, the PayPal purchase button will not appear.';

$string['ssettingscurrencybox'] = 'Currency';
$string['ssettingscurrencydesc'] = 'currency under development';
$string['ssettingscurrencyUSD'] = 'USD';
$string['ssettingscurrencyAUD'] = 'AUD';

$string['ssettingscolourbox'] = 'Colour';
$string['ssettingscolourdesc'] = 'Choose the colour of the PayPal button.';
$string['ssettingscolourgold'] = 'Gold';
$string['ssettingscolourblue'] = 'Blue';
$string['ssettingscoloursilver'] = 'Silver';
$string['ssettingscolourwhite'] = 'White';
$string['ssettingscolourblack'] = 'Black';

$string['ssettingsshapebox'] = 'Shapes';
$string['ssettingsshapedesc'] = 'Choose the shape of the PayPal button.';
$string['ssettingsshaperectangle'] = 'Rectangle';
$string['ssettingsshapepill'] = 'Pill';

$string['erroripn'] = 'An error occurred while processing IPN';
$string['erroripncost'] = 'The transacted amount did not match with the course cost!';
$string['erroripncurrency'] = 'The transacted currency did not match with the currency set in paypal plugin settings!';
$string['erroripncourseid'] = 'The courseid does not exist!';
$string['erroripnuserid'] = 'The userid does not exist!';
$string['erroripninvalid'] = 'IPN failed to be verified by PayPal.';
