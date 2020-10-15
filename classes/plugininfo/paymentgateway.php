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
 * Defines the subplugin type 'paymentgateway'
 *
 * @package     tool_paymentplugin
 * @author      Mitchell Halpin - Based heavily off code done by 'Catalyst AU'
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace tool_paymentplugin\plugininfo;

defined('MOODLE_INTERNAL') || die();

class paymentgateway extends \core\plugininfo\base  {

    /**
     * gets all payment gateways.
     *
     * @return array of gateway objects.
     */
    public static function get_all_gateway_objects() : array {
        $return = array();

        foreach (\core_plugin_manager::instance()->get_plugins_of_type('paymentgateway') as $gateway) {
            $classname = '\\paymentgateway_'.$gateway->name.'\\paymentgateway';
            if (class_exists($classname)) {
                $return[] = new $classname($gateway->name);
            }
        }
        return self::sort_gateways_by_order($return);
    }

    /**
     * Gets all enabled payment gateway objects.
     *
     * @return array of gateway objects
     */
    public static function get_all_enabled_gateway_objects() : array {
        $gateways = self::get_all_gateway_objects();
        $returnarr = array();
        foreach ($gateways as $gateway) {
            if ($gateway->is_enabled()) {
                $returnarr[] = $gateway;
            }
        }
        return $returnarr;
    }

    /**
     * Get specific gateway object
     *
     * @param string name of payment gateway
     *
     * @return paymentgateway or null
     */
    public static function get_gateway_object(string $name) {
        foreach (\core_plugin_manager::instance()->get_plugins_of_type('paymentgateway') as $gateway) {
            if ($gateway->name == $name) {
                $gatewayclass = "\\paymentgateway_".$gateway->name.'\\paymentgateway';
                if (class_exists($gatewayclass)) {
                    return new $gatewayclass($name);
                }
            }
        }
    }

    /**
     * Sorts payment gateways by configured order.
     *
     * @param array of gateway objects
     *
     * @return array of gateway objects
     * @throws \dml_exception
     */
    public static function sort_gateways_by_order(array $unsorted) : array {
        $sorted = array();
        $orderarray = explode(',', get_config('tool_paymentplugin', 'paymentgateway_order'));

        foreach ($orderarray as $order => $gatewayname) {
            foreach ($unsorted as $key => $gateway) {
                if ($gateway->get_name() == $gatewayname) {
                    $sorted[] = $gateway;
                    unset($unsorted[$key]);
                }
            }
        }

        $sorted = array_merge($sorted, $unsorted);
        return $sorted;
    }

    /**
     * Loads all payment gateway settings.
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig whether the current user has moodle/site:config capability
     *
     * @return void or null
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig or !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $settings = new \admin_settingpage('paymentgateway_'.$this->name, $this->displayname,
            'moodle/site:config', $this->is_enabled() === false);

        if ($adminroot->fulltree) {
            include($this->full_path('settings.php'));
        }

        $adminroot->add($parentnodename, $settings);
    }
}
