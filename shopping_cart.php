<?php

require_once(__DIR__.'/../../../config.php');
require_once('form/shopping_cart_form.php');

use tool_paymentplugin\shopping\shopping_session;

// Setup Page
$title = get_string('shoppingcart:title', 'tool_paymentplugin');
$PAGE->set_url('/admin/tool/paymentplugin/shopping_cart.php');

$PAGE->set_heading($title);
$PAGE->navbar->add($title, new moodle_url('/admin/tool/paymentplugin/shopping_cart.php'));

// Display Page
echo $OUTPUT->header();

$cartform = new shopping_cart_form(new moodle_url('/admin/tool/paymentplugin/shopping_cart.php'));
$cartform->display();
/*

$cart = shopping_session::getcart();

$total = 0;
$tablename = 'tool_paymentplugin_course';
foreach($cart as $item) {
    if (!is_null($item)) {
        $record = $DB->get_record($tablename, ['courseid' => $item]);
        $cost = $record['cost'];
        if (!is_null($cost))  {
            echo $item.' = $'.$cost;
            $total += $cost;
        } else {
            echo 'Course not found!';
        }
        echo '--';
    }
}
echo 'Total $'.$total;
*/
echo $OUTPUT->footer();