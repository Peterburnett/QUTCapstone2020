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
 * Module to display a paypal button that a student can click to make a purchase.
 *
 * @package    paymentgateway_paypal
 * @module     paymentgateway_paypal\paypal
 * @copyright  MAHQ
 * @author     Haruki Nakagawa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {
    return {
        button: function(buttondata) {
            paypal.Buttons({
                createOrder: function(data, actions) {
                // This function sets up the details of the transaction, including the amount and line item details.
                return actions.order.create({
                    intent: 'CAPTURE',
                    payer: {
                        name: {
                            given_name: buttondata.userfirstname,
                            surname:    buttondata.userlastname
                        },
                        email_address: buttondata.useremail
                    },
                    purchase_units: [{
                        amount: {
                            currency_code: buttondata.currency,
                            value: buttondata.amount,
                            breakdown: {
                                item_total: {
                                    currency_code: buttondata.currency,
                                    value: buttondata.amount
                                }
                            }
                        },
                        items: [{
                            name: buttondata.coursefullname,
                            unit_amount: {
                                currency_code: buttondata.currency,
                                value: buttondata.amount
                            },
                            sku: buttondata.courseid,
                            quantity: '1',
                            category: 'DIGITAL_GOODS'
                        }],
                        custom_id: buttondata.custom
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
                    color: buttondata.buttoncolour,
                    size:  buttondata.buttonsize,
                    shape: buttondata.buttonshape
                }
            }).render('#paypal-button-container');
            //This function displays Smart Payment Buttons on your web page.
        }
    };
});
