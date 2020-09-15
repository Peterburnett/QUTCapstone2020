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
 * PayPal payment gateway utility class.
 *
 * @package    paymentgateway_paypal
 * @copyright  2016 Cameron Ball <cameron@cameron1729.xyz>
 * @author     2020 Haruki Nakagawa
 * @copyright  2020 MAHQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paymentgateway_paypal;

defined('MOODLE_INTERNAL') || die();

/**
 * PayPal payment gateway utility class.
 *
 * @package   paymentgateway_paypal
 * @copyright 2016 Cameron Ball <cameron@cameron1729.xyz>
 * @author    2020 Haruki Nakagawa
 * @copyright 2020 MAHQ
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class util {

    /**
     * Alerts site admin of potential problems.
     * !!!Could not get email to work on a server running on XAMPP!!!
     * !!!Could not be tested, and therefore not used right now!!!
     *
     * @param string   $subject email subject
     * @param stdClass $data    PayPal IPN data
     */
    public static function message_paypal_error_to_admin($subject, $data) {
        $admin = get_admin();
        $site = get_site();

        $message = "$site->fullname:  Transaction failed.\n\n$subject\n\n";

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
        $eventdata->subject           = "PAYPAL ERROR: ".$subject;
        $eventdata->fullmessage       = $message;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);
    }

    /**
     * Silent exception handler.
     *
     * @return callable exception handler
     */
    public static function get_exception_handler() {
        return function($ex) {
            $info = get_exception_info($ex);

            $logerrmsg = "paymentgateway_paypal IPN exception handler: ".$info->message;
            if (debugging('', DEBUG_NORMAL)) {
                $logerrmsg .= ' Debug: '.$info->debuginfo."\n".format_backtrace($info->backtrace, true);
            }
            error_log($logerrmsg);

            if (http_response_code() == 200) {
                http_response_code(500);
            }

            exit(0);
        };
    }
}
