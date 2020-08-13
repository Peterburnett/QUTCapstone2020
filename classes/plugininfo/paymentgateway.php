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
 * File         paymentgateway.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin\plugininfo
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace tool_paymentplugin\plugininfo;

defined('MOODLE_INTERNAL') || die();

// @See https://docs.moodle.org/dev/Subplugins#Settings_pages
class paymentgateway extends \core\plugininfo\base  {

    /**
     * Finds all payment gateways.
     * @author Catalyst AU
     *
     * @return array of gateway objects.
     */
    public static function get_all_gateway_objects() {
        $return = array();

        foreach (\core_plugin_manager::instance()->get_plugins_of_type('paymentgateway') as $gateway) {
            $classname = '\\paymentgateway_'.$gateway->name.'\\paymentgateway';
            if (class_exists($classname)) {
                $return[] = new $classname($gateway->name);
            }
        }
        return self::sort_gateways_by_order($return);
    }


    public static function get_all_enabled_gateway_objects() {
        $gateways = self::get_all_gateway_objects();
        $returnarr = array();
        foreach ($gateways as $gateway) {
            if ($gateway->is_enabled()) {
                $returnarr[] = $gateway;
            }
        }
        return $returnarr;
    }


    public static function get_gateway_object($name)   {
        foreach (\core_plugin_manager::instance()->get_plugins_of_type('paymentgateway') as $gateway) {
            if ($gateway->name == $name)    {
                $gateway_class = "\\paymentgateway_".$gateway->name.'\\paymentgateway';
                if (class_exists($gateway_class)) {
                    return new $gateway_class($name);
                }
            }
        }
    }


    /**
     * Sorts payment gateways by configured order.
     * @author Catalyst AU
     *
     * @param array of gateway objects
     *
     * @return array of gateway objects
     * @throws \dml_exception
     */
    public static function sort_gateways_by_order($unsorted) {
        $sorted = array();
        $orderarray = explode(',', get_config('tool_paymentplugin', 'paymentgateway_order'));

        foreach ($orderarray as $order => $gatewayname) {
            foreach ($unsorted as $key => $gateway) {
                if ($gateway->name == $gatewayname) {
                    $sorted[] = $gateway;
                    unset($unsorted[$key]);
                }
            }
        }

        $sorted = array_merge($sorted, $unsorted);
        return $sorted;
    }


    /**
     * Loads factor settings to the settings tree
     *
     * This function usually includes settings.php file in plugins folder.
     * Alternatively it can create a link to some settings page (instance of admin_externalpage)
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig whether the current user has moodle/site:config capability
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig or !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = new \admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);

        if ($adminroot->fulltree) {
            include($this->full_path('settings.php'));
        }

        $adminroot->add($parentnodename, $settings);
    }

}