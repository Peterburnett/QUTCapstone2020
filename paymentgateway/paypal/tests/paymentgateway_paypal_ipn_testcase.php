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
 * Tests for paypal paymentgateway IPN class.
 *
 * @package    paymentgateway_paypal
 * @copyright  2020 MAHQ
 * @author     Haruki Nakagawa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paymentgateway_paypal\tests;
defined('MOODLE_INTERNAL') || die();

class paymentgateway_paypal_ipn_testcase extends \advanced_testcase {

    /**
     * Simulates being sent an IPN from PayPal.
     * 
     * @param string $ipn_name File name of test IPN in test_ipn directory
     * @return array $post Data from test IPN file in the same format as $_POST
     */
    private function ipn_sim($ipn_name) {
        $postraw = file_get_contents('/fixtures/$ipn_name.txt');
        $postraw = explode('&', $postraw);

        $post = array();
        foreach ($postraw as $param) {
            $paramarray = explode('=', $param);
            $post[$paramarray[0]] = $paramarray[1];
        }
        
        return $post;
    }

    // Test processing of normal IPN.
    public function test_processing_normal() {
        $post = $this->ipn_sim('ipn_normal');

        $generator = $this->getDataGenerator();

        $courserecord = new \stdClass();
        $courserecord->fullname = 'Test course_1+2=3';
        $courserecord->idnumber = 10;
        $course = $generator->create_course($courserecord);
         

        $userrecord = new \stdClass();
        $userrecord->idnumber = 25;
        $user = $generator->create_user($userrecord);

        $this->setUser($user);
    }

    // Test processing of IPN without custom value.

    // Test processing of IPN with incorrect custom value format.

    // Test processing of IPN with post request that is not an IPN.

    // Test normal successful IPN.

    // Test IPN with incorrect course cost.

    // Test IPN with incorrect currency.

    // Test IPN with invalid userid.

    // Test IPN with invalid courseid.

    // Test IPN with incorrect cost, currency, and invalid userid, courseid.

    // Test IPN with duplicate transaction ID (txn_id).

    // Test IPN with payment status not "Completed".

}
