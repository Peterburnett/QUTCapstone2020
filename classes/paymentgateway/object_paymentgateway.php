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
    private $name;

    // Name of table used to log paymentgateway specific transaction details.
    private $tablename;

    private $config;

    /**
     * @param string name of gateway
     */
    public function __construct($name) {
        $this->name = $name;
        // Default table name.
        $this->tablename = 'paymentgateway_' . $this->name;

        $this->config = get_config('paymentgateway_' . $this->name);
    }

    public function get_name() {
        return $this->name;
    }

    /**
     * Gets the display name with 'Payment Gateway' appened on the end.
     *
     * @return string name of gateway + 'Payment Gateway'
     */
    public function get_display_name_appended() {
        return get_string('paymentgateway', 'tool_paymentplugin', $this->get_display_name());
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
     * Gets the transaction details table name of the gateway.
     *
     * @return string name of table
     */
    public function get_tablename() {
        return $this->tablename;
    }

    /**
     * Sets the transaction details table name of the gateway.
     * Should only need to be used if the default table name cannot be used
     * for logging transaction details.
     *
     * @param string $tablename name of table
     */
    public function set_tablename(string $tablename) {
        $this->tablename = $tablename;
    }

    /**
     * Checks if the plugin is enabled in the admin settings for this plugin.
     *
     * @return boolean TRUE if enabled, FALSE otherwise.
     */
    public function is_enabled() {
        $config = $this->config;
        // Explicitly convert to bool instead of using int to avoid type conversion errors.
        if (!empty($config->enabled) && $config->enabled) {
            $pluginenabled = true;
        } else {
            $pluginenabled = false;
        }

        $enabled = $pluginenabled && !get_config('tool_paymentplugin', 'disableall');
        if ($enabled) {
            return true;
        }
        return false;
    }

    /**
     * When a payment gateway validates a purchase, it calls this function.
     * A log of the transaction will be made, and the user enrolled.
     *
     * @param object $data Data of transaction.
     */
    abstract public function submit_purchase_data($data);

    /**
     * Gets the payment gateway button in a html acceptable form.
     *
     * @param int $courseid
     *
     * @return string html that makes up the payment gateway button.
     */
    abstract public function payment_button(int $courseid): string;
}
