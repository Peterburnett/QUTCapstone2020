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

use paymentgateway_paypal\ipn;
use stdClass;

class paymentgateway_paypal_ipn_testcase extends \advanced_testcase {

    /**
     * Simulates being sent an IPN from PayPal.
     * 
     * @param string $ipn_name File name of test IPN in test_ipn directory
     * @return array $post Data from test IPN file in the same format as $_POST
     */
    private function generate_simulated_ipn($ipn_name) {
        $postraw = file_get_contents(__DIR__."\\fixtures\\$ipn_name.txt");
        $postraw = explode('&', $postraw);

        $post = array();
        foreach ($postraw as $param) {
            $paramarray = explode('=', $param);
            $paramarray[0] = urldecode($paramarray[0]);
            $paramarray[1] = urldecode($paramarray[1]);
            $post[$paramarray[0]] = $paramarray[1];
        }
        
        return $post;
    }

    /**
     * Generates default values of expected data based on the data in ipn_normal.txt.
     * Fixtures should be based on ipn_normal.txt as much as possible with only the minimum necessary
     * fields being changed or added for a test.
     * 
     * @return object $ex
     */
    private function generate_expected_data() {
        $ex = new stdClass();
        $ex->txn_type = 'cart';
        $ex->business = 'test@business.example.com';
        $ex->charset = 'UTF-8';
        $ex->parent_txn_id = null;
        $ex->receiver_email = 'test@business.example.com';
        $ex->receiver_id = 'T9CHS2ZUN6BL6';
        $ex->resend = null;
        $ex->residence_country = 'AU';
        $ex->test_ipn = null;
        $ex->txn_id = '1YR097324Y527661V';
        $ex->first_name = 'John';
        $ex->last_name = 'Doe';
        $ex->payer_id = 'ZFDYPPYELC4KS';
        $ex->item_name1 = 'Test course_1+2=3';
        $ex->mc_currency = 'USD';
        $ex->mc_gross = '50.00';
        $ex->payment_date = '16:09:34 Sep 14, 2020 PDT';
        $ex->courseid = 2;
        $ex->userid = 3;
        $ex->payment_status = 'Completed';
        $ex->pending_reason = null;
        return $ex;
    }

    // Test processing of normal IPN.
    public function test_processing_normal() {
        $this->resetAfterTest();

        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);

        // Expected result.
/*         $ex = new stdClass();
        $ex->txn_type = 'cart';
        $ex->business = 'test@business.example.com';
        $ex->charset = 'UTF-8';
        $ex->parent_txn_id = null;
        $ex->receiver_email = 'test@business.example.com';
        $ex->receiver_id = 'T9CHS2ZUN6BL6';
        $ex->resend = null;
        $ex->residence_country = 'AU';
        $ex->test_ipn = null;
        $ex->txn_id = '1YR097324Y527661V';
        $ex->first_name = 'John';
        $ex->last_name = 'Doe';
        $ex->payer_id = 'ZFDYPPYELC4KS';
        $ex->item_name1 = 'Test course_1+2=3';
        $ex->mc_currency = 'USD';
        $ex->mc_gross = '50.00';
        $ex->payment_date = '16:09:34 Sep 14, 2020 PDT';
        $ex->courseid = 2;
        $ex->userid = 3;
        $ex->payment_status = 'Completed';
        $ex->pending_reason = null; */
        $ex = $this->generate_expected_data();
        $this->assertEquals($ex, $data);

        // Similar IPN but with no null values
        $post = $this->generate_simulated_ipn('ipn_normal2');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);

        // Expected result.
        $ex = $this->generate_expected_data();
        $ex->parent_txn_id = '1QR097329Y527661V';
        $ex->resend = 'true';
        $ex->test_ipn = '1';
        $ex->txn_id = '1QR097329Y527661V';
        $ex->payment_status = 'Pending';
        $ex->pending_reason = 'echeck';
        $this->assertEquals($ex, $data);
    }

    // Test processing of IPN without custom value.
    public function not_a_test_yet() {
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

    // Test full purchase
    public function _test_valid_purchase() {
        $this->resetAfterTest();
        global $DB;

        $files = ['ipn_normal', 'ipn_normal2', 'ipn_failed'];
        for ($t = 0; $t < count($files); $t++) {
            $course = $this->getDataGenerator()->create_course();
            $user = $this->getDataGenerator()->create_user();
            $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));
    
            $post = $this->generate_simulated_ipn($files[$t]);
            $ipn = new ipn();
            $data = $ipn->process_ipn($post);
            $data->courseid = $course->id;
            $data->userid = $user->id;
            $result_of_test = $ipn->submit_data('VERIFIED', $data);
            // Get Purchase Proof
            $record2 = $DB->get_record_sql('SELECT * FROM {tool_paymentplugin_purchases} as ppp JOIN {paymentgateway_paypal} as 
                pgp ON pgp.purchase_id = ppp.id AND ppp.courseid = ?', [$data->courseid]);
            // Get Enrolment Proof
            $record3 = $DB->get_record_sql('SELECT * FROM {user_enrolments} as ue JOIN {enrol} as 
                e ON e.id = ue.enrolid AND ue.userid = ?', ['userid' => $user->id]);

            if ($t == 0) {
                $this->assertEquals(1, $result_of_test);
                $this->assertEquals(1, $record2->success);
                $this->assertEquals('John', $record2->first_name);
                $this->assertEquals('50.00', $record2->amount);
                $this->assertEquals($course->id, $record3->courseid);
            } else if ($t == 1) {
                $this->assertEquals(2, $result_of_test);
                $this->assertEquals(2, $record2->success);
                $this->assertEquals('John', $record2->first_name);
                $this->assertEquals('50.00', $record2->amount);
                $this->assertEquals(null, $record3);
            } else if ($t == 2) {
                $this->assertEquals(0, $result_of_test);
                $this->assertEquals(0, $record2->success);
                $this->assertEquals('Jake', $record2->first_name);
                $this->assertEquals('250.00', $record2->amount);
                $this->assertEquals(null, $record3);
            }
        }
    }



    // Test processing of IPN with incorrect custom value format.

    // Test processing of IPN with post request that is not an IPN.

    // Test normal successful IPN. (already done in test_valid_purchase)
    public function test_successful_ipn() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(array('idnumber' => 999));
        $DB->insert_record('tool_paymentplugin_course', array('courseid' => $course->id, 'cost' => 50));
        $user = $this->getDataGenerator()->create_user();
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        $data->userid = $user->id;
        $ipn->submit_data('VERIFIED', $data);

        // Get Purchase details.
        $details = $DB->get_record_sql('SELECT * FROM {tool_paymentplugin_purchases} as ppp JOIN {paymentgateway_paypal} as 
            pgp ON pgp.purchase_id = ppp.id AND ppp.courseid = ?', [$data->courseid]);
        // Get Enrolment details.
        $enrolment = $DB->get_record_sql('SELECT * FROM {user_enrolments} as ue JOIN {enrol} as 
            e ON e.id = ue.enrolid AND ue.userid = ?', ['userid' => $user->id]);

        // Check transaction details were recorded correctly.
        $this->assertEquals(1, $details->success);
        $this->assertEquals('John', $details->first_name);
        $this->assertEquals('50.00', $details->amount);
        $this->assertEquals($course->id, $enrolment->courseid);
    }

    // Test IPN with incorrect course cost.

    // Test IPN with incorrect currency.

    // Test IPN with invalid userid.

    // Test IPN with invalid courseid.

    // Test IPN with incorrect cost, currency, and invalid userid, courseid.

    // Test IPN with duplicate transaction ID (txn_id).
    public function test_duplicate_txn_id() {
        $this->resetAfterTest();
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        // txn_id 1, User 1
        $user = $this->getDataGenerator()->create_user();
        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        $data->userid = $user->id;
        $trxn_id = $data->txn_id;
        $ipn->submit_data('VERIFIED', $data);

        // txn_id 1, User 2
        $user = $this->getDataGenerator()->create_user();
        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        $data->userid = $user->id;
        $trxn2_id = $data->txn_id;
        $result_of_test = $ipn->submit_data('VERIFIED', $data);

        $this->assertEquals($trxn_id, $trxn2_id);
        $this->assertEquals(0, $result_of_test);
    }

    // Test IPN with payment status not "Completed".

}
