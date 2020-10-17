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
 * Class for a specific payment gateway object.
 *
 * @package     paymentgateway_paypal
 * @author      Haruki Nakagawa
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace paymentgateway_paypal;

use \tool_paymentplugin\payment_manager;

defined ('MOODLE_INTERNAL') || die();

class paymentgateway extends \tool_paymentplugin\paymentgateway\object_paymentgateway {

    const STATUS_COMPLETE = 'Completed';
    const STATUS_PROCESSED = 'Processed';
    const STATUS_PENDING = 'Pending';

    /**
     * Alerts site admin of potential problems.
     *
     * @param string   $subject email subject
     * @param stdClass $data    PayPal IPN data
     */
    private function message_paypal_error_to_admin(string $subject, array $data) {
        global $PAGE;
        $PAGE->set_context(\context_system::instance());

        $admin = get_admin();
        $site = get_site();

        $message = "$site->fullname:  ".get_string('error:failedtransaction', 'paymentgateway_paypal')."\n\n$subject\n\n";

        foreach ($data as $key => $value) {
            $message .= "$key => $value\n";
        }

        $eventdata = new \core\message\message();
        $eventdata->courseid          = empty($data->courseid) ? SITEID : $data->courseid;
        $eventdata->modulename        = 'moodle';
        $eventdata->component         = 'paymentgateway_paypal';
        $eventdata->name              = 'payment_paypal_error';
        $eventdata->userfrom          = $admin;
        $eventdata->userto            = $admin;
        $eventdata->subject           = get_string('error:paypal', 'paymentgateway_paypal', $subject);
        $eventdata->fullmessage       = $message;
        $eventdata->fullmessageformat = FORMAT_MOODLE;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);
    }

    public function submit_purchase_data($data) {
        global $DB;
        $status = $data->payment_status;

        $paymentstatus = payment_manager::PAYMENT_FAILED;
        if ($status == self::STATUS_COMPLETE || $status == self::STATUS_PROCESSED) {
            $paymentstatus = payment_manager::PAYMENT_COMPLETE;
        } else if ($status == self::STATUS_PENDING) {
            $paymentstatus = payment_manager::PAYMENT_INCOMPLETE;
        }

        // If errorinfo exists, there was an error. Do not enrol the user.
        if (isset($data->errorinfo)) {
            $paymentstatus = payment_manager::PAYMENT_FAILED;
        }

        // Check for duplicate txn ids.
        $record = $DB->get_record('paymentgateway_paypal', ['txnid' => $data->txn_id]);
        if ($record != false) {
            $paymentstatus = payment_manager::PAYMENT_FAILED;
        }

        $data = (array) $data;

        $res = payment_manager::submit_transaction($this, $paymentstatus, $data['userid'],
            $data['mc_currency'], $data['mc_gross'], $data['payment_date'], $data['courseid'], $data);

        if ($res == payment_manager::PAYMENT_FAILED) { // ERROR.
            $this->message_paypal_error_to_admin(get_string('error:invalidpayment', 'paymentgateway_paypal'), $data);
            return payment_manager::PAYMENT_FAILED;
        } else if ($res == payment_manager::PAYMENT_INCOMPLETE) { // PENDING.
            $this->message_paypal_error_to_admin(get_string('error:pendingpayment', 'paymentgateway_paypal'), $data);
            return payment_manager::PAYMENT_INCOMPLETE;
        }
        return payment_manager::PAYMENT_COMPLETE;
    }

    public function payment_button(int $courseid): string {
        global $CFG, $USER, $DB, $PAGE;

        $data = new \stdClass();

        // Gather config data.
        $sandboxid       = get_config('paymentgateway_paypal', 'clientidsandbox');
        $productionid    = get_config('paymentgateway_paypal', 'clientidproduction');
        // If testing, set '$CFG->usepaypalsandbox = 1;' in config.php.
        $clientid        = empty($CFG->usepaypalsandbox) ? $productionid : $sandboxid;
        $data->buttonsize      = 'small';
        $data->buttoncolour    = get_config('paymentgateway_paypal', 'colour');
        $data->buttonshape     = get_config('paymentgateway_paypal', 'shape');
        $course          = $DB->get_record('course', array('id' => $courseid));
        $context         = \context_course::instance($course->id);
        $data->coursefullname  = format_string($course->fullname, true, array('context' => $context));
        $data->userfirstname   = $USER->firstname;
        $data->userlastname    = $USER->lastname;
        $data->useremail       = $USER->email;
        $record          = $DB->get_record('tool_paymentplugin_course', ['courseid' => $course->id]);
        $cost            = $record->cost;
        $data->amount          = number_format((float)$cost, 2, '.', '');
        $data->currency        = get_config('tool_paymentplugin', 'currency');
        // Custom parameter that holds user ID and course ID for the IPN page to read.
        $data->custom          = $USER->id . '-' . $course->id;

        $html = \html_writer::script(null, "https://www.paypal.com/sdk/js?client-id=$clientid&currency=$data->currency");
        $html .= \html_writer::div(null, null, array('id' => 'paypal-button-container'));

        // Gateway JavaScript.
        $PAGE->requires->js_call_amd('paymentgateway_paypal/paypal', 'button', array($data));

        if ($clientid) {
            return $html;
        } else {
            throw new \moodle_exception(get_string('error:clientid', 'paymentgateway_paypal'));
        }
    }
}
