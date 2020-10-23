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
 * A manager class used by payment gateway subplugins.
 *
 * @package     tool_paymentplugin
 * @author      Mitchell Halpin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace tool_paymentplugin;

use dml_exception;
use moodle_exception;

defined ('MOODLE_INTERNAL') || die();

class payment_manager {

    const CURRENCY = array('USD' => 'usd', 'AUD' => 'aud');

    const PAYMENT_FAILED = 0;
    const PAYMENT_COMPLETE = 1;
    const PAYMENT_INCOMPLETE = 2;

    /**
     * Enrols a user in a course.
     *
     * @param int $courseid
     * @param int $userid
     * @throws \moodle_exception
     */
    public static function paymentplugin_enrol(int $courseid, int $userid) {
        global $DB;
        try {
            get_course($courseid);
        } catch (dml_exception $e) {
            throw new \moodle_exception('errorinvalidcourse', 'tool_paymentplugin', '', $courseid);
        }

        if (!\core_user::get_user($userid)) {
            throw new \moodle_exception('errorinvaliduser', 'tool_paymentplugin', '', $userid);
        }

        $enrolinstance = $DB->get_record('enrol', array('enrol' => 'payment', 'courseid' => $courseid));
        if (!$enrolinstance) {
            throw new \moodle_exception('errorinvalidcourseenrol', 'tool_paymentplugin', '', $courseid);
        }

        $enrol = enrol_get_plugin('payment');
        $enrol->enrol_user($enrolinstance, $userid);
    }

    /**
     * Strips underscores from all keys from an array, or all property names in an object.
     *
     * @param array|object $data An array or object to be operated on.
     * @return void
     */
    public static function filter_underscores(&$data) {
        $datatype = gettype($data);
        foreach ($data as $var => $value) {
            $count = 0;
            $newvar = str_replace('_', '', $var, $count);
            // Only replace and unset if the key actually changed as indicated by $count.
            if ($datatype == 'array' && $count) {
                $data[$newvar] = $value;
                unset($data[$var]);
            } else if ($datatype == 'object' && $count) {
                $data->$newvar = $value;
                unset($data->$var);
            }
        }
    }

    /**
     * Actions a transaction given the correct data.
     *
     * @param object_paymentgateway $gateway A payment gateway instance.
     * @param int $paymentstatus Either PAYMENT_FAILED, PAYMENT_COMPLETE or PAYMENT_INCOMPLETE.
     * @param int $userid The moodle user id of the one making the purchase.
     * @param string $currency The currency the transaction was made in.
     * @param float $amount The value of the amount paid.
     * @param int $date The date time of the purchase.
     * @param int $courseid The moodle course id that the transaction was used to purchase.
     * @param array $additionaldata Any valid additional data in this object will be inserted into the
     * payment log tablename provided in the gateway instance.
     * 
     * @return int The status of the paypal transaction as either PAYMENT_COMPLETE, PAYMENT_INCOMPLETE
     * or PAYMENT_FAILED.
     */
    public static function submit_transaction(\tool_paymentplugin\paymentgateway\object_paymentgateway $gateway,
        int $paymentstatus, int $userid, string $currency, float $amount, int $date, int $courseid,
        array $additionaldata = null) : int {
        global $DB;

        // Ensure currency is a valid currency.
        if (!in_array(strtolower($currency), self::CURRENCY)) {
            throw new \moodle_exception('Invalid currency passed.');
        }

        $gatewayname = $gateway->get_name();
        $gatewaytablename = $gateway->get_tablename();

        $id = $DB->insert_record('tool_paymentplugin_purchases', ['gateway' => $gatewayname, 'currency' => $currency,
            'userid' => $userid, 'amount' => $amount, 'paymentdate' => $date, 'courseid' => $courseid, 'success' => $paymentstatus]
            );

        if (!empty($additionaldata)) {
            $additionaldata['purchaseid'] = $id; // NOTE, all subplugin tables will need purchase_id.
            self::filter_underscores($additionaldata);
            $DB->insert_record($gatewaytablename, $additionaldata);
        }

        if ($paymentstatus == self::PAYMENT_COMPLETE) {
            // Enrol the user.
            self::paymentplugin_enrol($courseid, $userid);
            return self::PAYMENT_COMPLETE;
        } else if ($paymentstatus == self::PAYMENT_INCOMPLETE) {
            /* Don't do anything to the current enrolment.
            Notify student and admin that payment is pending
            Notify admin of pending_reason, but only tell student that payment is pending
            and to contact admin for details. */
            return self::PAYMENT_INCOMPLETE;
        } else if ($paymentstatus == self::PAYMENT_FAILED) {
            // Notify student that payment failed (notify admin too or no?).
            return self::PAYMENT_FAILED;
        } else {
            throw new \moodle_exception('Invalid payment status passed.');
        }
    }
}
