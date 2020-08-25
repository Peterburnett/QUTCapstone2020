<?php

namespace tool_paymentplugin\shopping;

defined ('MOODLE_INTERNAL') || die();

class shopping_session {
    public static function addtocart(int $courseid) {
        global $SESSION;
        
        if (!is_null($SESSION->paymentplugin_shoppingcart)) {
            $cart = unserialize($SESSION->paymentplugin_shoppingcart);
            $cart->addtocart($courseid);
            $SESSION->paymentplugin_shoppingcart = serialize($cart);
        } else {
            $cart = new shopping_cart();
            $cart->addtocart($courseid);
            $SESSION->paymentplugin_shoppingcart = serialize($cart);
        }
    }
}