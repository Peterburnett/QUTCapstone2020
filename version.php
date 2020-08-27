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
 * This file defines the version of payment plugin
 *
 * File         course_settings.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
    defined('MOODLE_INTERNAL') || die();

    $plugin->version = 2020081100;
    /* $plugin->requires = TODO;
    // $plugin->supported = TODO;
    // $plugin->incompatible = TODO;
    */
    $plugin->component = 'tool_paymentplugin';
    $plugin->maturity = MATURITY_STABLE;
    $plugin->release = 'v0.1-r0';
    /* $plugin->dependencies = [ 'mod_forum' => ANY_VERSION, 'mod_data' => TODO ]; */

