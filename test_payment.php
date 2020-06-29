<?php
require_once(__DIR__ . "/../../../config.php");
require_login();

$PAGE->set_context(CONTEXT_SYSTEM::instance());
$PAGE->set_url(new moodle_url("/admin/tool/paymentplugin/test_payment_page.php"));
$PAGE->set_pagelayout("base");
$PAGE->set_title(get_string('testpaymentpagetitle', 'tool_paymentplugin'));
$PAGE->set_heading(get_string('testpaymentpagetitle', 'tool_paymentplugin'));

echo $OUTPUT->header();

require_once(__DIR__ . "/classes/test_payment_form.php");
$mform = new testpayment_form();
$mform->display();

echo $OUTPUT->footer();
