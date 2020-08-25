<?php

namespace tool_paymentplugin\shopping;

defined ('MOODLE_INTERNAL') || die();

class shopping_session {

    public static function addtocart(int $courseid) {
        global $SESSION;
        
        if (isset($SESSION->paymentplugin_shoppingcart)) {
            $cart = unserialize($SESSION->paymentplugin_shoppingcart);
            $cart->addtocart($courseid);
            $SESSION->paymentplugin_shoppingcart = serialize($cart);
        } else {
            $cart = new shopping_cart();
            $cart->addtocart($courseid);
            $SESSION->paymentplugin_shoppingcart = serialize($cart);
        }
    }

    public static function removefromcart(int $courseid) {
        global $SESSION;
        
        if (isset($SESSION->paymentplugin_shoppingcart)) {
            $cart = unserialize($SESSION->paymentplugin_shoppingcart);
            $cart->removefromcart($courseid);
            $SESSION->paymentplugin_shoppingcart = serialize($cart);
        }
    }

    public static function getcart() {
        global $SESSION;
        
        if (isset($SESSION->paymentplugin_shoppingcart)) {
            $cart = unserialize($SESSION->paymentplugin_shoppingcart);
            return $cart->getcart();
        }
    }

}