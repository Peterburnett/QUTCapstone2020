<?php
require_once(__DIR__ . "/../../../config.php");
require_login();

$PAGE->set_context(CONTEXT_SYSTEM::instance());
$PAGE->set_url(new moodle_url("/admin/tool/paymentplugin/test_payment.php"));
$PAGE->set_pagelayout("base");
$PAGE->set_title(get_string('testpaymentpagetitle', 'tool_paymentplugin'));
$PAGE->set_heading(get_string('testpaymentpagetitle', 'tool_paymentplugin'));

echo $OUTPUT->header();

$mform = new tool_paymentplugin\form\test_payment_form();

if ($mform->is_cancelled()) {
    //cancelled
} else if ($fromform = $mform->get_data()) {
    $accnumber = $fromform->accountnumber;
    $password = $fromform->password;
    // send information to payment gateway api

    // redirect?
    
    
    // proof the user's information was extracted from form (delete once above is implemented)
    echo "account number: " . $accnumber;
    echo "<br>";
    echo "password: " . $password;
} else {
    $mform->display();
}

echo $OUTPUT->footer();
