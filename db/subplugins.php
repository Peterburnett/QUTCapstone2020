<?php

    defined('MOODLE_INTERNAL') || die();

    $subplugins = (array) json_decode(file_get_contents($CFG->dirroot."/admin/tool/mfa/db/subplugins.json"))->plugintypes;