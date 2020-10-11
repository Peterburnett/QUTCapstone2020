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
     * @param string $ipnname File name of test IPN in test_ipn directory
     * @return array $post Data from test IPN file in the same format as $_POST
     */
    private function generate_simulated_ipn($ipnname) {
        $postraw = file_get_contents(__DIR__."/fixtures/$ipnname.txt");
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
     * Generates expected extracted data based on the data in ipn_normal.txt.
     * Fixtures should be based on ipn_normal.txt as much as possible with only the minimum necessary
     * fields being changed or added for a test, so that tests can use this function to easily generate
     * an object of expected values.
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

    /**
     * Generates expected data for a complete record of transaction data based on ipn_normal.txt
     * with no errors.
     * Fields courseid and userid are manually set by each test as we cannot tell a generator to
     * create a course/user with a specific id.
     * Fields id, purchase_id and payment_date should simply not be tested as we do not have any expected values for them.
     *
     * @param stdClass $course A course record.
     * @param stdClass $user A user record.
     *
     * @return object $ex
     */
    private function generate_expected_table_data(stdClass $course, stdClass $user) {
        $ex = $this->generate_expected_data();

        $ex->payment_type = 'paypal';
        $ex->currency = $ex->mc_currency;
        $ex->amount = $ex->mc_gross;
        $ex->success = '1';
        $ex->verified = '1';
        $ex->errorinfo = null;
        $ex->courseid = $course->id;
        $ex->userid = $user->id;
        unset($ex->mc_currency);
        unset($ex->mc_gross);
        unset($ex->payment_date);

        return $ex;
    }

    // Test processing of normal IPN.
    public function test_processing_normal() {
        $this->resetAfterTest();

        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);

        $ex = $this->generate_expected_data();
        $this->assertEquals($ex, $data);

        // Similar IPN but with no null values.
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
    public function test_processing_no_custom() {
        $this->resetAfterTest();

        $post = $this->generate_simulated_ipn('ipn_no_custom');
        $ipn = new ipn();
        $this->expectException('moodle_exception');
        $ipn->process_ipn($post);
    }

    // Test processing of IPN with incorrect custom value format.
    public function test_processing_bad_custom() {
        $this->resetAfterTest();

        $post = $this->generate_simulated_ipn('ipn_bad_custom');
        $ipn = new ipn();
        $this->expectException('moodle_exception');
        $ipn->process_ipn($post);
    }

    // Test normal successful IPN.
    public function test_successful_ipn() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $DB->insert_record('tool_paymentplugin_course', array('courseid' => $course->id, 'cost' => 50));
        $user = $this->getDataGenerator()->create_user();
        // Add enrol_payment as an enrolment method to course.
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        set_config('currency', 'USD', 'tool_paymentplugin');

        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        $data->userid = $user->id;
        $ipn->submit_data('VERIFIED', $data);

        // Get Purchase details.
        $details = $DB->get_record_sql('SELECT * FROM {tool_paymentplugin_purchases} JOIN {paymentgateway_paypal}
        ON {paymentgateway_paypal}.purchase_id = {tool_paymentplugin_purchases}.id
        AND {tool_paymentplugin_purchases}.courseid = ?', [$data->courseid]);
        // Get Enrolment details.
        $enrolment = $DB->get_record_sql('SELECT * FROM {user_enrolments} JOIN {enrol}
            ON {enrol}.id = {user_enrolments}.enrolid AND {user_enrolments}.userid = ?', ['userid' => $user->id]);

        // Unset fields we cannot test for.
        unset($details->id);
        unset($details->purchase_id);
        unset($details->payment_date);

        // Check transaction details were recorded correctly.
        $ex = $this->generate_expected_table_data($course, $user);
        $this->assertEquals($ex, $details);

        // Check enrolment happened correctly.
        $this->assertEquals($user->id, $enrolment->userid);
        $this->assertEquals($course->id, $enrolment->courseid);
        $this->assertEquals('payment', $enrolment->enrol);
    }

    // Test IPN with incorrect course currency.
    public function test_incorrect_currency() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $DB->insert_record('tool_paymentplugin_course', array('courseid' => $course->id, 'cost' => 50));
        $user = $this->getDataGenerator()->create_user();
        // Add enrol_payment as an enrolment method to course.
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        // Set settings to AUD while payment occurs in USD.
        set_config('currency', 'AUD', 'tool_paymentplugin');

        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        $data->userid = $user->id;
        $ipn->submit_data('VERIFIED', $data);

        // Get Purchase details.
        $details = $DB->get_record_sql('SELECT * FROM {tool_paymentplugin_purchases} JOIN {paymentgateway_paypal}
        ON {paymentgateway_paypal}.purchase_id = {tool_paymentplugin_purchases}.id
        AND {tool_paymentplugin_purchases}.courseid = ?', [$data->courseid]);
        // Get Enrolment details.
        $enrolment = $DB->get_record_sql('SELECT * FROM {user_enrolments} JOIN {enrol}
            ON {enrol}.id = {user_enrolments}.enrolid AND {user_enrolments}.userid = ?', ['userid' => $user->id]);

        // Unset fields we cannot test for.
        unset($details->id);
        unset($details->purchase_id);
        unset($details->payment_date);

        // Check transaction details were recorded correctly.
        $ex = $this->generate_expected_table_data($course, $user);
        $ex->success = '0';
        $ex->errorinfo = get_string('erroripncurrency', 'paymentgateway_paypal');
        $this->assertEquals($ex, $details);

        // Check enrolment failed.
        $this->assertEquals(false, $enrolment);
    }

    // Test IPN with incorrect cost.
    public function test_incorrect_cost() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        // Set course cost to 100 while the paid amount is only 50.
        $DB->insert_record('tool_paymentplugin_course', array('courseid' => $course->id, 'cost' => 100));
        $user = $this->getDataGenerator()->create_user();
        // Add enrol_payment as an enrolment method to course.
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        set_config('currency', 'USD', 'tool_paymentplugin');

        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        $data->userid = $user->id;
        $ipn->submit_data('VERIFIED', $data);

        // Get Purchase details.
        $details = $DB->get_record_sql('SELECT * FROM {tool_paymentplugin_purchases} JOIN {paymentgateway_paypal}
        ON {paymentgateway_paypal}.purchase_id = {tool_paymentplugin_purchases}.id
        AND {tool_paymentplugin_purchases}.courseid = ?', [$data->courseid]);
        // Get Enrolment details.
        $enrolment = $DB->get_record_sql('SELECT * FROM {user_enrolments} JOIN {enrol}
            ON {enrol}.id = {user_enrolments}.enrolid AND {user_enrolments}.userid = ?', ['userid' => $user->id]);

        // Unset fields we cannot test for.
        unset($details->id);
        unset($details->purchase_id);
        unset($details->payment_date);

        // Check transaction details were recorded correctly.
        $ex = $this->generate_expected_table_data($course, $user);
        $ex->success = '0';
        $ex->errorinfo = get_string('erroripncost', 'paymentgateway_paypal');
        $this->assertEquals($ex, $details);

        // Check enrolment failed.
        $this->assertEquals(false, $enrolment);
    }

    // Test IPN with invalid userid.
    public function test_incorrect_userid() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $DB->insert_record('tool_paymentplugin_course', array('courseid' => $course->id, 'cost' => 50));
        $user = $this->getDataGenerator()->create_user();
        // Add enrol_payment as an enrolment method to course.
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        set_config('currency', 'USD', 'tool_paymentplugin');

        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        // Increase the value of the user id, making it an invalid one.
        $incorrectid = $user->id + 1;
        $data->userid = $incorrectid;
        $ipn->submit_data('VERIFIED', $data);

        // Get Purchase details.
        $details = $DB->get_record_sql('SELECT * FROM {tool_paymentplugin_purchases} JOIN {paymentgateway_paypal}
        ON {paymentgateway_paypal}.purchase_id = {tool_paymentplugin_purchases}.id
        AND {tool_paymentplugin_purchases}.courseid = ?', [$data->courseid]);
        // Get Enrolment details.
        $enrolment = $DB->get_record_sql('SELECT * FROM {user_enrolments} JOIN {enrol}
            ON {enrol}.id = {user_enrolments}.enrolid AND {user_enrolments}.userid = ?', ['userid' => $user->id]);

        // Unset fields we cannot test for.
        unset($details->id);
        unset($details->purchase_id);
        unset($details->payment_date);

        // Check transaction details were recorded correctly.
        $ex = $this->generate_expected_table_data($course, $user);
        $ex->userid = $incorrectid;
        $ex->success = '0';
        $ex->errorinfo = get_string('erroripnuserid', 'paymentgateway_paypal');
        $this->assertEquals($ex, $details);

        // Check enrolment failed.
        $this->assertEquals(false, $enrolment);
    }

    // Test IPN with invalid courseid.
    public function test_incorrect_courseid() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $DB->insert_record('tool_paymentplugin_course', array('courseid' => $course->id, 'cost' => 50));
        $user = $this->getDataGenerator()->create_user();
        // Add enrol_payment as an enrolment method to course.
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        set_config('currency', 'USD', 'tool_paymentplugin');

        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        // Increase course id by 1, making it invalid.
        $incorrectid = $course->id + 1;
        $data->courseid = $incorrectid;
        $data->userid = $user->id;
        $ipn->submit_data('VERIFIED', $data);

        // Get Purchase details.
        $details = $DB->get_record_sql('SELECT * FROM {tool_paymentplugin_purchases} JOIN {paymentgateway_paypal}
        ON {paymentgateway_paypal}.purchase_id = {tool_paymentplugin_purchases}.id
        AND {tool_paymentplugin_purchases}.courseid = ?', [$data->courseid]);
        // Get Enrolment details.
        $enrolment = $DB->get_record_sql('SELECT * FROM {user_enrolments} JOIN {enrol}
            ON {enrol}.id = {user_enrolments}.enrolid AND {user_enrolments}.userid = ?', ['userid' => $user->id]);

        // Unset fields we cannot test for.
        unset($details->id);
        unset($details->purchase_id);
        unset($details->payment_date);

        // Check transaction details were recorded correctly.
        $ex = $this->generate_expected_table_data($course, $user);
        $ex->courseid = $incorrectid;
        $ex->success = '0';
        $ex->errorinfo = get_string('erroripncourseid', 'paymentgateway_paypal');
        $this->assertEquals($ex, $details);

        // Check enrolment failed.
        $this->assertEquals(false, $enrolment);
    }

    // Test IPN with multiple errors (incorrect course cost, currency and userid).
    public function test_incorrect_multiple() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        // Make course price different from transacted price.
        $DB->insert_record('tool_paymentplugin_course', array('courseid' => $course->id, 'cost' => 100));
        $user = $this->getDataGenerator()->create_user();
        // Add enrol_payment as an enrolment method to course.
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        // Make plugin settings currency different from transacted currency.
        set_config('currency', 'AUD', 'tool_paymentplugin');

        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        // Increase user id by 1, making it invalid.
        $incorrectid = $user->id + 1;
        $data->courseid = $course->id;
        $data->userid = $incorrectid;
        $ipn->submit_data('VERIFIED', $data);

        // Get Purchase details.
        $details = $DB->get_record_sql('SELECT * FROM {tool_paymentplugin_purchases} JOIN {paymentgateway_paypal}
        ON {paymentgateway_paypal}.purchase_id = {tool_paymentplugin_purchases}.id
        AND {tool_paymentplugin_purchases}.courseid = ?', [$data->courseid]);
        // Get Enrolment details.
        $enrolment = $DB->get_record_sql('SELECT * FROM {user_enrolments} JOIN {enrol}
            ON {enrol}.id = {user_enrolments}.enrolid AND {user_enrolments}.userid = ?', ['userid' => $user->id]);

        // Unset fields we cannot test for.
        unset($details->id);
        unset($details->purchase_id);
        unset($details->payment_date);

        // Check transaction details were recorded correctly.
        $ex = $this->generate_expected_table_data($course, $user);
        $ex->userid = (string)$incorrectid;
        $ex->success = '0';
        $ex->errorinfo = get_string('erroripncurrency', 'paymentgateway_paypal') . " " .
                         get_string('erroripncost', 'paymentgateway_paypal') . " " .
                         get_string('erroripnuserid', 'paymentgateway_paypal');
        $this->assertEquals($ex, $details);

        // Check enrolment failed.
        $this->assertEquals(false, $enrolment);
    }

    // Test IPN with duplicate transaction ID (txn_id).
    public function test_duplicate_txn_id() {
        $this->resetAfterTest();
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        // Original.
        $user = $this->getDataGenerator()->create_user();
        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        $data->userid = $user->id;
        $trxnid = $data->txn_id;
        $ipn->submit_data('VERIFIED', $data);

        // Same txn id.
        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        $data->userid = $user->id;
        $trxn2id = $data->txn_id;
        $testresult = $ipn->submit_data('VERIFIED', $data);

        $this->assertEquals($trxnid, $trxn2id);
        $this->assertEquals(0, $testresult);
    }

    // Test pending IPN.
    public function test_pending_ipn() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $DB->insert_record('tool_paymentplugin_course', array('courseid' => $course->id, 'cost' => 50));
        $user = $this->getDataGenerator()->create_user();
        // Add enrol_payment as an enrolment method to course.
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        set_config('currency', 'USD', 'tool_paymentplugin');

        $post = $this->generate_simulated_ipn('ipn_normal2');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        $data->userid = $user->id;
        $ipn->submit_data('VERIFIED', $data);

        // Get Purchase details.
        $details = $DB->get_record_sql('SELECT * FROM {tool_paymentplugin_purchases} JOIN {paymentgateway_paypal}
        ON {paymentgateway_paypal}.purchase_id = {tool_paymentplugin_purchases}.id
        AND {tool_paymentplugin_purchases}.courseid = ?', [$data->courseid]);
        // Get Enrolment details.
        $enrolment = $DB->get_record_sql('SELECT * FROM {user_enrolments} JOIN {enrol}
            ON {enrol}.id = {user_enrolments}.enrolid AND {user_enrolments}.userid = ?', ['userid' => $user->id]);

        // Unset fields we cannot test for.
        unset($details->id);
        unset($details->purchase_id);
        unset($details->payment_date);

        // Check transaction details were recorded correctly.
        $ex = $this->generate_expected_table_data($course, $user);
        $ex->parent_txn_id = '1QR097329Y527661V';
        $ex->resend = 'true';
        $ex->test_ipn = '1';
        $ex->txn_id = '1QR097329Y527661V';
        $ex->payment_status = 'Pending';
        $ex->pending_reason = 'echeck';
        $ex->success = '2';
        $this->assertEquals($ex, $details);

        // Check enrolment did not happen.
        $this->assertEquals(false, $enrolment);
    }

    // Test failed IPN.
    public function test_failed_ipn() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $DB->insert_record('tool_paymentplugin_course', array('courseid' => $course->id, 'cost' => 50));
        $user = $this->getDataGenerator()->create_user();
        // Add enrol_payment as an enrolment method to course.
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        set_config('currency', 'USD', 'tool_paymentplugin');

        $post = $this->generate_simulated_ipn('ipn_failed');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        $data->userid = $user->id;
        $ipn->submit_data('VERIFIED', $data);

        // Get Purchase details.
        $details = $DB->get_record_sql('SELECT * FROM {tool_paymentplugin_purchases} JOIN {paymentgateway_paypal}
        ON {paymentgateway_paypal}.purchase_id = {tool_paymentplugin_purchases}.id
        AND {tool_paymentplugin_purchases}.courseid = ?', [$data->courseid]);
        // Get Enrolment details.
        $enrolment = $DB->get_record_sql('SELECT * FROM {user_enrolments} JOIN {enrol}
            ON {enrol}.id = {user_enrolments}.enrolid AND {user_enrolments}.userid = ?', ['userid' => $user->id]);

        // Unset fields we cannot test for.
        unset($details->id);
        unset($details->purchase_id);
        unset($details->payment_date);

        // Check transaction details were recorded correctly.
        $ex = $this->generate_expected_table_data($course, $user);
        $ex->payment_status = 'Failed';
        $ex->success = '0';
        $this->assertEquals($ex, $details);

        // Check enrolment did not happen.
        $this->assertEquals(false, $enrolment);
    }

    // Test IPN that is not from PayPal (PayPal did not return VERIFIED).
    public function test_unverified_ipn() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $DB->insert_record('tool_paymentplugin_course', array('courseid' => $course->id, 'cost' => 50));
        $user = $this->getDataGenerator()->create_user();
        // Add enrol_payment as an enrolment method to course.
        $DB->insert_record('enrol', array('enrol' => 'payment', 'courseid' => $course->id));

        set_config('currency', 'USD', 'tool_paymentplugin');

        $post = $this->generate_simulated_ipn('ipn_normal');
        $ipn = new ipn();
        $data = $ipn->process_ipn($post);
        $data->courseid = $course->id;
        $data->userid = $user->id;

        $this->expectException('moodle_exception');

        try {
            $ipn->submit_data('INVALID', $data);
        } finally {
            // Get Purchase details.
            $details = $DB->get_record_sql('SELECT * FROM {tool_paymentplugin_purchases} JOIN {paymentgateway_paypal}
            ON {paymentgateway_paypal}.purchase_id = {tool_paymentplugin_purchases}.id
            AND {tool_paymentplugin_purchases}.courseid = ?', [$data->courseid]);
            // Get Enrolment details.
            $enrolment = $DB->get_record_sql('SELECT * FROM {user_enrolments} JOIN {enrol}
                ON {enrol}.id = {user_enrolments}.enrolid AND {user_enrolments}.userid = ?', ['userid' => $user->id]);

            // Check transaction details were not recorded.
            $this->assertEquals(false, $details);

            // Check enrolment did not occur.
            $this->assertEquals(false, $enrolment);
        }
    }
}
