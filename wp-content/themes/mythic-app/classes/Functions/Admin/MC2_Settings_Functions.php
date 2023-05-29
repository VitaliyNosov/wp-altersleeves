<?php

namespace Mythic\Functions\Admin;

use Mythic\Abstracts\MC2_Class;
use Mythic_Template\Settings\MT_Settings_Pages;

class MC2_Settings_Functions extends MC2_Class {
    
    const ACF_DIR = ABSPATH.'/files/mt_acf_json/';
    
    public function filters() {
        add_filter( 'acf/settings/save_json', [ $this, 'change_acf_save_path' ] );
        add_filter( 'acf/settings/load_json', [ $this, 'change_acf_load_path' ] );
    }
    
    /**
     * @return string
     */
    public function change_acf_save_path() {
        return self::ACF_DIR;
    }
    
    /**
     * @param $paths
     *
     * @return array
     */
    public function change_acf_load_path( $paths ) {
        unset( $paths[0] );
        $paths[] = self::ACF_DIR;
        
        return $paths;
    }
    
}