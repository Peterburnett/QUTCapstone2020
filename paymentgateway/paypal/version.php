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
 * This file defines the version of the paypal payment method plugin.
 *
 * @package     paymentgateway_paypal
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2020101501;
$plugin->component = 'paymentgateway_paypal';

$plugin->dependencies = array(
    'tool_paymentplugin' => 2020101200
);
