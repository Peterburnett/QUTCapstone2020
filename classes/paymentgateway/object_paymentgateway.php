<?php

namespace tool_paymentplugin\classes\paymentgateway;

defined ('MOODLE_INTERNAL') || die();

abstract class object_paymentgateway {
    public $name;

    public function __construct($name)    {
        $this->name = $name.'test';
    }
}