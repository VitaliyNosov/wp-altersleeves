<?php

namespace Mythic_Template\Abstracts;

use Mythic_Template\System\MT_Settings;

/**
 * Class MT_Settings_Page
 *
 * @package Mythic_Template\Abstracts
 */
abstract class MT_Settings_Page
{
    public static $settings_pages = [];

    /**
     * Start up
     */
    public function __construct()
    {
        if (!empty(static::$settings_pages) && function_exists('acf_add_options_page')) {
            foreach (static::$settings_pages as $settings_page) {
                acf_add_options_page($settings_page);
            }
        }
    }

}
