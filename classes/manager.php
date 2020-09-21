<?php

namespace tool_paymentplugin;

class manager {

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

    public static function submit_transaction($gatewayname, $userid, $amount, $date, $courseid, $additionaldata = null) {
        global $DB;

        $id = $DB->insert_record('tool_paymentplugin_purchases', ['payment_type' => $gatewayname, 'userid' => $userid, 'amount' => $amount, 'date' => $date, 'courseid' => $courseid]);

        if (!is_null($additionaldata)) {
            $additionaldata->id = $id;
            $DB->insert_record('paymentgateway_'.$gatewayname, $additionaldata);
        }

        manager::paymentplugin_enrol($courseid, $userid);
    }
}
