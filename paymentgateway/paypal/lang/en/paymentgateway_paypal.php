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
 * @package     paymentgateway_paypal
 * @author      Haruki Nakagawa
 * @author      Quyen Nguyen
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

$string['pluginname'] = 'PayPal';

$string['settings:heading'] = 'PayPal payment subplugin settings';
$string['settings:description'] = 'Settings for PayPal payment subplugin';

$string['settings:clientidsandbox'] = 'Sandbox client ID';
$string['settings:clientdescsandbox'] = 'The client business ID given to you by PayPal for a sandbox application.
    If invalid, the PayPal purchase button will not appear.';
$string['settings:clientidproduction'] = 'Production client ID';
$string['settings:clientdescproduction'] = 'The client business ID given to you by PayPal for a live appication.
    If invalid, the PayPal purchase button will not appear.';

$string['settings:colour'] = 'Colour';
$string['settings:colourdesc'] = 'Choose the colour of the PayPal button.';
$string['settings:colourgold'] = 'Gold';
$string['settings:colourblue'] = 'Blue';
$string['settings:coloursilver'] = 'Silver';
$string['settings:colourwhite'] = 'White';
$string['settings:colourblack'] = 'Black';

$string['settings:shape'] = 'Shape';
$string['settings:shapedesc'] = 'Choose the shape of the PayPal button.';
$string['settings:shaperectangle'] = 'Rectangle';
$string['settings:shapepill'] = 'Pill';

$string['error:clientid'] = 'PayPal client ID has not been set! Please contact the site administrator for details.';
$string['erroripn'] = 'An error occurred while processing IPN';
$string['erroripncost'] = 'The transacted amount did not match with the course cost!';
$string['erroripncurrency'] = 'The transacted currency did not match with the currency set in paypal plugin settings!';
$string['erroripncourseid'] = 'The courseid does not exist!';
$string['erroripnuserid'] = 'The userid does not exist!';
$string['erroripninvalid'] = 'IPN failed to be verified by PayPal.';
$string['error:invalidpayment'] = 'Invalid payment.';
$string['error:pendingpayment'] = 'Payment pending.';
$string['error:paypal'] = 'PAYPAL ERROR: {$a}';
$string['error:invalidcustom'] = 'Invalid value of the request param: custom';
$string['error:purchasescript'] = 'Sorry, you can not use the script that way.';
$string['error:failedtransaction'] = 'Transaction failed.';

$string['messageprovider:payment_paypal_error'] = 'Notification of payment error through PayPal.';
