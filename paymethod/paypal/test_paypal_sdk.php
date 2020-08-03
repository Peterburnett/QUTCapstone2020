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
 * A temporary test page for testing the sending of a user to paypal for a course purchase.
 * This page is for test purposes, and will later be replaced by a form that appears
 * on the course enrolment page.  This uses the more up-to-date SDK integration of the smart buttons.
 * https://developer.paypal.com/docs/checkout/integrate/#
 *
 * File         test_paypal_sdk.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_login();

$id = required_param('id', PARAM_INT);

$PAGE->set_context(CONTEXT_COURSE::instance($id));
$PAGE->set_url(new moodle_url('/admin/tool/paymentplugin/paymethod/paypal/test_paypal_sdk.php', array('id'=>$id)));
$PAGE->set_title("test paypal payment");
$PAGE->set_heading("paypal payment");
// Using raw strings instead of get_string because this file will not be used.

echo $OUTPUT->header();
?>
<script
    src="https://www.paypal.com/sdk/js?client-id=Ac77CRgg9lq_gvxT2dmf9DryDowLdBCwMafuVLDgdLHfHyYgF5kgSlG-uWziX9RgJ8yhB5ZYCWIbEsQl"> // Required. Replace SB_CLIENT_ID with your sandbox client ID.
</script>

<div id="paypal-button-container"></div>

<script>
  paypal.Buttons({
    createOrder: function(data, actions) {
      // This function sets up the details of the transaction, including the amount and line item details.
      return actions.order.create({
        purchase_units: [{
          amount: {
            value: '0.01'
          }
        }]
      });
    },
    onApprove: function(data, actions) {
      // This function captures the funds from the transaction.
      return actions.order.capture().then(function(details) {
        // This function shows a transaction success message to your buyer.
        alert('Transaction completed by ' + details.payer.name.given_name);
      });
    },
    style: {
        color:  'blue'
    }
  }).render('#paypal-button-container');
  //This function displays Smart Payment Buttons on your web page.
</script>

<?php
echo $OUTPUT->footer();
