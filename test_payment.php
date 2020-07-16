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
 * Adds a page that users can insert payment info into.
 *
 * File         test_payment.php
 * Encoding     UTF-8
 *
 * @package     tool_paymentplugin
 *
 * @copyright   MAHQ
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../../config.php");
require_login();

// what data needs to be passed through to this page via url? array of course id's to be purchased?
$purchases = optional_param_array('purchaseid', null, PARAM_INT);

$PAGE->set_context(CONTEXT_SYSTEM::instance()); // correct context?
$PAGE->set_url(new moodle_url("/admin/tool/paymentplugin/test_payment.php"));
$PAGE->set_pagelayout("base");
$PAGE->set_title(get_string('testpaymentpagetitle', 'tool_paymentplugin'));
$PAGE->set_heading(get_string('testpaymentpagetitle', 'tool_paymentplugin'));

echo $OUTPUT->header();

$mform = new tool_paymentplugin\form\test_payment_form();

if ($mform->is_cancelled()) {
    null;
    // Redirect to previous page
} else if ($fromform = $mform->get_data()) {
    $accnumber = $fromform->accountnumber;
    $password = $fromform->password;
    // send information to payment gateway api

    // redirect?

    // just proving that the user's information was extracted from form (delete once above or unit tests are implemented)
    echo "account number: " . $accnumber;
    echo "<br>";
    echo "password: " . $password;

} else {
    $mform->display();
}

echo $OUTPUT->footer();
