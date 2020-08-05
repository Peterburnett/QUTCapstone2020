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
 * on the course enrolment page.  This uses the checkout.js integration which is no longer updated.
 * https://developer.paypal.com/docs/archive/checkout/integrate/#
 *
 * File         test_paypal.php
 * Encoding     UTF-8
 *
 * @package     paymentgateway_paypal
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_login();

$id = required_param('id', PARAM_INT);

$PAGE->set_context(CONTEXT_COURSE::instance($id));
$PAGE->set_url(new moodle_url('/admin/tool/paymentplugin/paymentmemethod/paypal/test_paypal.php', array('id'=>$id)));
$PAGE->set_title("test paypal payment");
$PAGE->set_heading("paypal payment");
// Using raw strings instead of get_string because this file will not be used.

// Values are currently hardcoded until settings page is done
$environment = empty($CFG->usepaypalsandbox) ? 'production' : 'sandbox';
$sandboxid = 'Ac77CRgg9lq_gvxT2dmf9DryDowLdBCwMafuVLDgdLHfHyYgF5kgSlG-uWziX9RgJ8yhB5ZYCWIbEsQl';
$productionid = 'placeholdertext';
$locale  = 'en_US';
$buttonsize = 'small';
$buttoncolour = 'gold';
$buttonshape = 'pill';

$amount = '0.01';
$currency = 'USD';

echo $OUTPUT->header();
?>

<div id="paypal-button"></div>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<script>
  paypal.Button.render({
    // Configure environment
    env: $environment,
    client: {
      sandbox: $sandboxid,
      production: $productionid
    },
    // Customize button (optional)
    locale: $locale,
    style: {
      size: $buttonsize,
      color: $buttoncolour,
      shape: $buttonshape,
    },

    // Enable Pay Now checkout flow (optional)
    commit: true,

    // Set up a payment
    payment: function(data, actions) {
      return actions.payment.create({
        transactions: [{
          amount: {
            total: $amount,
            currency: $currency
          }
        }]
      });
    },
    // Execute the payment
    onAuthorize: function(data, actions) {
      return actions.payment.execute().then(function() {
        // Show a confirmation message to the buyer
        window.alert('Thank you for your purchase!');
      });
    }
  }, '#paypal-button');

</script>

<?php
echo $OUTPUT->footer();
