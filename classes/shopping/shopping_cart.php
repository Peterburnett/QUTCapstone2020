<?php

namespace tool_paymentplugin\shopping;

defined ('MOODLE_INTERNAL') || die();

class shopping_cart {

    private $cart = array();

    public function addtocart(int $courseid) {
        if (in_array($courseid, $this->cart)) {
            $this->cart[] = $courseid;
            echo  $courseid;
            return true;
        }
        return false;
    }

    public function removefromcart($courseid)   {
        $key = array_search($courseid, $this->cart);
        if ($key >= 0) {
            $this->cart[$key] = NULL;
            return true;
        }
        return false;
    }

    public function getcart()   {
        return $this->cart;
    }
}