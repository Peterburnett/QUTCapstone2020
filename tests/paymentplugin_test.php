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
 * Test cases for database
 *
 * File         paymentplugin_test.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

class tool_paymentplugin_testcase extends advanced_testcase {

    // Course cost currently only supports whole numbers.
    // Add tests for floats once decimal values and currency is sorted.
    public function test_course_cost() {
        global $DB;
        $this->resetAfterTest();

        $tablename = 'tool_paymentplugin_course';
        $coursecosts = array(10, 200, 50, 123, 001);

        // Test if prices for multiple courses can be set without throwing error
        for ($x = 0; $x < count($coursecosts); $x++) {
            $record = (object) array('courseid' => $x, 'cost' => $coursecosts[$x]);
            $DB->insert_record($tablename, $record);
        }

        // Test if prices for all courses are retrieved correctly
        for ($x = 0; $x < count($coursecosts); $x++) {
            $record = $DB->get_record('tool_paymentplugin_course', ['courseid' => $x]);
            $this->assertEquals($coursecosts[$x], $record->cost);
        }
    }

    public function test_subplugin_detection()  {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->assertEquals(count(\tool_paymentplugin\plugininfo\paymentgateway::get_all_gateway_objects()), 2);
        $this->assertEquals(count(\tool_paymentplugin\plugininfo\paymentgateway::get_all_enabled_gateway_objects()), 0);

        set_config('enabled', 1, 'paymentgateway_paypal');
        set_config('enabled', 1, 'paymentgateway_credit');

        $this->assertEquals(count(\tool_paymentplugin\plugininfo\paymentgateway::get_all_enabled_gateway_objects()), 2);

        set_config('disablePurchases', 1, 'tool_paymentplugin_gsettings');

        $this->assertEquals(count(\tool_paymentplugin\plugininfo\paymentgateway::get_all_enabled_gateway_objects()), 0);

        set_config('disablePurchases', 0, 'tool_paymentplugin_gsettings');

        $this->assertEquals(count(\tool_paymentplugin\plugininfo\paymentgateway::get_all_enabled_gateway_objects()), 2);
    }
}
