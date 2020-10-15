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
 * Language EN file for payment plugin.
 *
 * @package     tool_paymentplugin
 * @author      Mitchell Halpin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// General.
$string['pluginname'] = 'Payment Plugin';
$string['adminsettingsheading'] = 'Payment plugin settings';
$string['paymentgateway'] = '{$a} Payment Gateway';

// Admin Settings.
$string['gatewaydisableall:text'] = 'Disable all payment gateways';
$string['gatewaylist:heading'] = 'Enable\Disable payment gateways';
$string['gatewaylist:desc'] = '{$a->installed} installed payment gateways. {$a->enabled} currently enabled.';
$string['gatewayenable:text'] = 'Enable {$a}';

$string['settings:currency'] = 'Currency';
$string['settings:currencydesc'] = 'currency under development';
$string['settings:currencyUSD'] = 'USD';
$string['settings:currencyAUD'] = 'AUD';

// Course Settings.
$string['coursesettings:title'] = 'Course enrolment';
$string['coursesettings_management:title'] = 'Payment settings';

// Subplugin Types.
$string['subplugintype_paymentgateway'] = 'Payment gateway';
$string['subplugintype_paymentgateway_plural'] = 'Payment gateways';

// Purchase page.
$string['purchasepagetitle'] = 'Purchase course';
$string['purchasepagecourse'] = 'You are buying the course: "{$a->name}" for ${$a->cost}.';

// Errors.
$string['errorcoursecost'] = 'Please insert a valid number.';
$string['errornothingenabled'] = 'No payment gateways have been enabled! Please contact the site administrator for details.';
$string['errorinvalidcourse'] = 'No course of ID {$a} exists.';
$string['errorinvaliduser'] = 'No user of ID {$a} exists.';
$string['errorinvalidcourseenrol'] = 'The payment enrolment method is not available in course of ID {$a}.';
