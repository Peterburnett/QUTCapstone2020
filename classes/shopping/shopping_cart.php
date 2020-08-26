<?php

namespace tool_paymentplugin\shopping;

defined ('MOODLE_INTERNAL') || die();

class shopping_cart {

    private $cart = array();

    public function addtocart(int $courseid) {
        if (!in_array($courseid, $this->cart)) {
            $this->cart[] = $courseid;
        }
    }

    public function removefromcart($courseid)   {
        $newcart = array();
        foreach($this->cart as $course) {
            if (!is_null($course)) {
                if ($course != $courseid) {
                    $newcart[] = $course;
                }
            }
        }
        $this->cart = $newcart;
    }

    public function getcart()   {
        return $this->cart;
    }
}