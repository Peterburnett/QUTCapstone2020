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

defined ('MOODLE_INTERNAL') || die();

class tool_paymentplugin_testcase extends advanced_testcase {

    public function test_course_cost() {
        global $DB;
        $this->resetAfterTest();

        $tablename = 'tool_paymentplugin_course';
        $coursecosts = array(10.50, 200.62, 50.00, 123, 001.95);
        $courses = array();

        // Generate Courses.
        for ($x = 0; $x < count($coursecosts); $x++) {
            $courses[] = $this->getDataGenerator()->create_course()->id;
        }

        // Test if prices for multiple courses can be set without throwing error.
        for ($x = 0; $x < count($coursecosts); $x++) {
            $record = (object) array('courseid' => $courses[$x], 'cost' => $coursecosts[$x]);
            $DB->insert_record($tablename, $record);
        }

        // Test if prices for all courses are retrieved correctly.
        for ($x = 0; $x < count($coursecosts); $x++) {
            $record = $DB->get_record($tablename, ['courseid' => $courses[$x]]);
            $this->assertEquals($coursecosts[$x], $record->cost);
        }

        // Test for postive prices (all prices should be higher than 0).
        for ($x = 0; $x < count($coursecosts); $x++) {
            $record = $coursecosts[$x];
            $this->assertGreaterThan(0, $record, "There is an invaild price.");
        }
    }

    public function test_detectsubplugins() {
        $this->resetAfterTest();

        // Test disabled by default.
        $this->assertEquals(0, count(\tool_paymentplugin\plugininfo\paymentgateway::get_all_enabled_gateway_objects()),
            "The gateway is not disabled");

        // Test enable configs.
        set_config('enabled', 1, 'paymentgateway_paypal');
        $this->assertEquals(1, count(\tool_paymentplugin\plugininfo\paymentgateway::get_all_enabled_gateway_objects()),
            "Configs are not enabled.");

        // Test disable all config.
        set_config('disableall', 1, 'tool_paymentplugin');
        $this->assertEquals(0, count(\tool_paymentplugin\plugininfo\paymentgateway::get_all_enabled_gateway_objects()),
            "All configs are not disabled");

        set_config('disableall', 0, 'tool_paymentplugin');
        $this->assertEquals(1, count(\tool_paymentplugin\plugininfo\paymentgateway::get_all_enabled_gateway_objects()),
            "All configs are not disabled");
    }
}
