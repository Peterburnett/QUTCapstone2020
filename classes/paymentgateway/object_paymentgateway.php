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
 * @package     tool_paymentplugin
 * @author      Mitchell Halpin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace tool_paymentplugin\paymentgateway;

defined ('MOODLE_INTERNAL') || die();

abstract class object_paymentgateway {

    // Name of payment gateway.
    public $name;

    /**
     * @param string name of gateway
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Gets the display name with 'Payment Gateway' appened on the end.
     * 
     * @return string name of gateway + 'Payment Gateway'
     */
    public function get_display_name_appended() {
        return get_string('pluginname', 'paymentgateway_'.$this->name).' '.get_string('paymentgateway', 'tool_paymentplugin');
    }

    /**
     * Gets the display name of the gateway.
     * 
     * @return string name of gateway
     */
    public function get_display_name() {
        return get_string('pluginname', 'paymentgateway_'.$this->name);
    }

    /**
     * Checks if the plugin is enabled in the admin settings for this plugin.
     * 
     * @return boolean TRUE if enabled, FALSE otherwise.
     */
    public function is_enabled() {
        $enabled = get_config('paymentgateway_'.$this->name, 'enabled') &&
            !get_config('tool_paymentplugin_settings', 'disableall');
        if ($enabled == 1) {
            return true;
        }
        return false;
    }

    /**
     * Gets the pyment gateway button in a html acceptable form.
     * 
     * @param int course id
     * 
     * @return html of the payment gateway button.
     */
    public function payment_button($courseid) {
    }
}
