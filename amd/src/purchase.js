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
 * AJAX request for the purchase.php page.
 *
 * @package   tool_paymentplugin
 * @module    tool_paymentplugin/purchase
 * @author    Haruki Nakagawa
 * @copyright 2020 MAHQ
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/ajax'], function(Ajax) {
    return {
        purchasecheck: function(courseid, userid, redirecturl) {
            var request =  {
                methodname: 'tool_paymentplugin_check_enrolled',
                args: {
                    courseid: courseid,
                    userid: userid
                }
            };
            Ajax.call([request])[0].done(
                function(data) {
                    if (data == true) {
                        document.location = redirecturl;
                    }
                }
            ).fail();
            setInterval(this.purchasecheck, 10000, courseid, userid, redirecturl);
        }
    };
});
