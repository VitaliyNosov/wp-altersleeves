<?php

namespace Mythic_Template\System;

use Mythic_Template\Settings\MT_Settings_Pages;

/**
 * Class MT_Actions
 *
 * @package Mythic_Template\System
 */
class MT_Actions {

    /**
     * MT_Actions constructor.
     */
    public function __construct()
    {
        add_filter('acf/settings/save_json', [MT_Settings_Pages::class, 'mtChangeAcfSavePath']);
        add_filter('acf/settings/load_json', [MT_Settings_Pages::class, 'mtChangeAcfLoadPath']);
    }
}
