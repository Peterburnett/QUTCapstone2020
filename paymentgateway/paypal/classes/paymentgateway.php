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

use moodle_exception;
use context_system;

defined ('MOODLE_INTERNAL') || die();

class paymentgateway extends \tool_paymentplugin\paymentgateway\object_paymentgateway {

    /**
     * Alerts site admin of potential problems.
     *
     * @param string   $subject email subject
     * @param stdClass $data    PayPal IPN data
     */
    private function message_paypal_error_to_admin($subject, $data) {
        global $PAGE;
        $PAGE->set_context(context_system::instance());

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

    public function submit_purchase_data($data) {
        global $DB;
        $status = $data->payment_status;

        $paymentstatus = 0;
        if ($status == "Completed" || $status == "Processed") {
            $paymentstatus = 1;
        } else if ($status == "Pending") {
            $paymentstatus = 2;
        }

        // If errorinfo exists, there was an error. Do not enrol the user.
        if (isset($data->errorinfo)) {
            $paymentstatus = 0;
        }

        // Check for duplicate txn ids
        if ($DB->record_exists('paymentgateway_paypal', ['txn_id' => $data->txn_id])) {
            $paymentstatus = 0;
        }

        $res = \tool_paymentplugin\payment_manager::submit_transaction($paymentstatus, 'paymentgateway_paypal', $this->name, $data->userid,
            $data->mc_currency, $data->mc_gross, $data->payment_date, $data->courseid, $data);

        if ($res == 0) { // ERROR
            $this->message_paypal_error_to_admin("Invalid Payment.", $data);
            return 0;
        } else if ($res == 2) { // PENDING
            $this->message_paypal_error_to_admin("Payment Pending.", $data);
            return 2;
        }
        return 1; // SUCCESS
    }

    public function payment_button($courseid) {
        global $CFG, $USER, $DB;

        // Gather config data.
        $sandboxid       = get_config('paymentgateway_paypal', 'clientidsandbox');
        $productionid    = get_config('paymentgateway_paypal', 'clientidproduction');
        // If testing, set '$CFG->usepaypalsandbox = 1;' in config.php.
        $clientid        = empty($CFG->usepaypalsandbox) ? $productionid : $sandboxid;
        $buttonsize      = 'small';
        $buttoncolour    = get_config('paymentgateway_paypal', 'colour');
        $buttonshape     = get_config('paymentgateway_paypal', 'shape');
        $course          = $DB->get_record('course', array('id' => $courseid));
        $context         = \context_course::instance($course->id);
        $coursefullname  = format_string($course->fullname, true, array('context' => $context));
        $userfirstname   = $USER->firstname;
        $userlastname    = $USER->lastname;
        $useremail       = $USER->email;
        $record          = $DB->get_record('tool_paymentplugin_course', ['courseid' => $course->id]);
        $cost            = $record->cost;
        $amount          = number_format((float)$cost, 2, '.', '');
        $currency        = get_config('tool_paymentplugin', 'currency');
        // Custom parameter that holds user ID and course ID for the IPN page to read.
        $custom          = $USER->id . '-' . $course->id;

        // Gateway HTML.
        $html = <<<HTML
<script src= "https://www.paypal.com/sdk/js?client-id=$clientid&currency=$currency"></script>
<div id="paypal-button-container"></div>
<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
        // This function sets up the details of the transaction, including the amount and line item details.
        return actions.order.create({
            intent: 'CAPTURE',
            payer: {
                name: {
                    given_name: '$userfirstname',
                    surname:    '$userlastname'
                },
                email_address: '$useremail'
            },
            purchase_units: [{
                amount: {
                    currency_code: '$currency',
                    value: '$amount',
                    breakdown: {
                        item_total: {
                            currency_code: '$currency',
                            value: '$amount'
                        }
                    }
                },
                items: [{
                    name: '$coursefullname',
                    unit_amount: {
                        currency_code: '$currency',
                        value: '$amount'
                    },
                    sku: '$courseid',
                    quantity: '1',
                    category: 'DIGITAL_GOODS'
                }],
                custom_id: '$custom'
            }],
            order_application_context: {
                shipping_preference: 'NO_SHIPPING'
            }
        });
        },
        onApprove: function(data, actions) {
            // This function captures the funds from the transaction.
            return actions.order.capture().then(function(details) {
                // This function shows a transaction success message to your buyer.
                alert('Transaction completed by ' + details.payer.name.given_name);
                // Redirect to purchased course page goes here!!!
                // Use similar process as enrol_paypal with return.php.
            });
        },
        onCancel: function(data) {
        // Redirect to course purchase page when cancelled?
        },
        style: {
            color: '$buttoncolour',
            size:  '$buttonsize',
            shape: '$buttonshape'
        }
    }).render('#paypal-button-container');
    //This function displays Smart Payment Buttons on your web page.
</script>
HTML;

        if ($clientid) {
            return $html;
        } else {
            throw new moodle_exception(get_string('error:clientid', 'paymentgateway_paypal'));
        }
    }
}
