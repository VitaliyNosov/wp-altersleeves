<?php

namespace Mythic_Template\Settings;

//use Mythic_Core\System\MT_Settings;
use Mythic_Template\Abstracts\MT_Settings_Page;
use Mythic_Template\System\MT_Settings;

/**
 * Class MT_Settings_Pages
 *
 * @package Mythic_Template\Configuration
 */
class MT_Settings_Pages extends MT_Settings_Page
{
    public static $settings_pages = [
        [
            'page_title' => 'Mythic Template settings',
            'menu_title' => 'Mythic Template settings',
            'menu_slug' => 'mythic-template-settings',
            'capability' => 'edit_posts'
        ]
    ];

    /**
     * @return string
     */
    public static function mtChangeAcfSavePath()
    {
        return MT_DIR_ACF_JSON;
    }

    /**
     * @param $paths
     * @return array
     */
    public static function mtChangeAcfLoadPath($paths)
    {
        unset($paths[0]);
        $paths[] = MT_DIR_ACF_JSON;

        return $paths;
    }
}
