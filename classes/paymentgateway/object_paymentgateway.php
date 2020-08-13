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
 * Abstract class for all payment gateway objects. 
 *
 * File         object_paymentgateway.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin\classes\paymentgateway
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace tool_paymentplugin\paymentgateway;

defined ('MOODLE_INTERNAL') || die();

abstract class object_paymentgateway {
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function get_display_name() {
        return get_string('pluginname', 'paymentgateway_'.$this->name);
    }

    public function is_enabled() {
        $enabled = get_config('paymentgateway_'.$this->name, 'enabled');
        if ($enabled == 1) {
            return true;
        }
        return false;
    }
}