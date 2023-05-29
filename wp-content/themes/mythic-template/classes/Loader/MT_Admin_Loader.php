<?php

namespace Mythic_Template\Loader;


use Mythic_Template\Settings\MT_Settings_Pages;

/**
 * Class MT_Admin_Loader
 *
 * @package Mythic_Template\Loader
 */
class MT_Admin_Loader {

    /**
     * MT_AdminLoader constructor.
     */
    public function __construct() {
        $this->initClasses();
    }

    public function initClasses() {
        new MT_Settings_Pages();
    }

}
