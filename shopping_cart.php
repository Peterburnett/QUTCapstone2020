<?php

require_once(__DIR__.'/../../../config.php');
require_once('classes/form/shopping_cart_form.php');
require_login();

// Setup Page
$title = get_string('shoppingcart:title', 'tool_paymentplugin');
$PAGE->set_url('/admin/tool/paymentplugin/shopping_cart.php');

$PAGE->set_heading($title);
$PAGE->navbar->add($title, new moodle_url('/admin/tool/paymentplugin/shopping_cart.php'));

// Display Page
echo $OUTPUT->header();

$cartform = new shopping_cart_form(new moodle_url('/admin/tool/paymentplugin/shopping_cart.php'));
$cartform->display();

echo $OUTPUT->footer();