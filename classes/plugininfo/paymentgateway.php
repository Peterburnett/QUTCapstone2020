<?php

    namespace tool_paymentplugin\plugininfo;

    defined('MOODLE_INTERNAL') || die();

    // @See https://docs.moodle.org/dev/Subplugins#Settings_pages
    class paymentgateway extends \core\plugininfo\base  {

        //@credit https://github.com/catalyst/moodle-tool_mfa/blob/master/classes/plugininfo/factor.php
        public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {

            if (!$this->is_installed_and_upgraded()) {
                return;
            }
    
            if (!$hassiteconfig or !file_exists($this->full_path('settings.php'))) {
                return;
            }
    
            $section = $this->get_settings_section_name();
    
            $settings = new \admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);
    
            if ($adminroot->fulltree) {
                include($this->full_path('settings.php'));
            }
    
            $adminroot->add($parentnodename, $settings);
        }
        
    }