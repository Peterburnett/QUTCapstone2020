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
            syslog(LOG_ERR, $logerrmsg);

            if (http_response_code() == 200) {
                http_response_code(500);
            }

            exit(0);
        };
    }
}
