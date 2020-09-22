<?php

namespace tool_paymentplugin;

class payment_manager {

    /**
     * Enrols a user in a course.
     *
     * @param string $courseid
     * @param string $userid
     * @throws \moodle_exception
     */
    public static function paymentplugin_enrol($courseid, $userid) {
        global $DB;
        if (!$DB->record_exists('course', array('id' => $courseid))) {
            throw new \moodle_exception('errorinvalidcourse', 'tool_paymentplugin', '', $courseid);
        }

        if (!$DB->record_exists('user', array('id' => $userid))) {
            throw new \moodle_exception('errorinvaliduser', 'tool_paymentplugin', '', $userid);
        }

        if (!$DB->record_exists('enrol', array('enrol' => 'payment', 'courseid' => $courseid))) {
            throw new \moodle_exception('errorinvalidcourseenrol', 'tool_paymentplugin', '', $courseid);
        }

        $enrol = enrol_get_plugin('payment');
        $enrolinstance = $DB->get_record('enrol', array('enrol' => 'payment', 'courseid' => $courseid));
        $enrol->enrol_user($enrolinstance, $userid);
    }

    /**
     * @param int $paymentstatus 0 for FAILED, 1 for COMPLETE, 2 for INCOMPLETE
     * @param string $gateway_table_name The name of the subplugin table where necessary non default data will be sent too.
     * @param string $gatewayname Payment gateway object name.
     * @param int $userid The moodle id of the user making the purchase.
     * @param string $currency The currency the transaction was made in.
     * @param double $amount the value of the amount paid.
     * @param string $date The date time of the purchase.
     * @param int The moodle course id that the transaction was used to purchase.
     * @param \stdclass Any valid additional data in this object will be inserted into the specified table $gateway_table_name.
     */
    public static function submit_transaction($paymentstatus, $gateway_table_name, $gatewayname, $userid, $currency, $amount, $date, $courseid, $additionaldata = null) {
        global $DB;

        $id = $DB->insert_record('tool_paymentplugin_purchases', ['payment_type' => $gatewayname, 'currency' => $currency, 'userid' => $userid, 
            'amount' => $amount, 'date' => $date, 'courseid' => $courseid, 'success' => $paymentstatus]);

        if (!is_null($additionaldata)) {
            $additionaldata->purchase_id = $id; // NOTE, all subplugin tables will need purchase_id.
            $DB->insert_record($gateway_table_name, $additionaldata);
        }

        if ($paymentstatus == 1) {
            // Enrol the user.
            payment_manager::paymentplugin_enrol($courseid, $userid);
        } else if ($paymentstatus == 2) {
            // don't do anything to the current enrolment
            // Notify student and admin that payment is pending
            // Notify admin of pending_reason, but only tell student that payment is pending
            // and to contact admin for details
        } else {
            // Notify student that payment failed (notify admin too or no?)
        }
    }
}
