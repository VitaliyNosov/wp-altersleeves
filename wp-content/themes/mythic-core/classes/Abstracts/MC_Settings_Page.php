<?php

namespace Mythic_Core\Abstracts;

use Mythic_Core\System\MC_Settings;

/**
 * Class MC_Settings_Page
 *
 * @package Mythic_Core\Abstracts
 */
abstract class MC_Settings_Page {
    
    public static $setting_group = 'mythic_core_site_settings';
    public static $setting_name = 'mythic_core_site_settings';
    public static $menu_slug = 'admin-mythic-core-site-settings';
    public static $title = 'Mythic Core Site Settings';
    public static $default_tab = '';
    public $setting_list = [];
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $settings;
    
    /**
     * Start up
     */
    public function __construct() {
        if( empty( $this->settings ) ) {
            $this->settings = get_option( static::$setting_group );
        }
        
        add_action( 'admin_menu', [ $this, 'add_plugin_page' ] );
        add_action( 'admin_init', [ $this, 'page_init' ] );
        
        if( empty( $this->setting_list ) ) {
            $this->setting_list = $this->settingsList();
        }
    }
    
    /**
     * @return array
     */
    abstract function settingsList() : array;
    
    public static function init() {
        $class = get_called_class();
        new $class();
    }
    
    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public static function value( $key = '', $default = null ) {
        if( empty( $key ) ) return null;
        $settings = get_option( static::$setting_name, $default );
        
        return $settings[ $key ] ?? $default;
    }
    
    /**
     * Add options page
     */
    public function add_plugin_page() {
        // This page will be under "Settings"
        add_options_page( static::$title, static::$title, 'manage_options', static::$menu_slug, [
            $this,
            'create_admin_page',
        ] );
    }
    
    /**
     * Options page callback
     */
    public function create_admin_page() {
        $this->settings = get_option( static::$setting_group );
        $args           = [
            'setting_list'  => $this->setting_list,
            'setting_group' => static::$setting_group,
            'menu_slug'     => static::$menu_slug,
            'title'         => static::$title,
            'tab'           => $_GET['tab'] ?? static::$default_tab,
        ];
        MC_Settings::renderPage( $args );
    }
    
    /**
     * Register and add settings
     */
    public function page_init() {
        register_setting( static::$setting_group, // Option group
                          static::$setting_name,  // Option name
                          [ $this, 'sanitize' ] // Sanitize
        );
        
        foreach( $this->setting_list as $section_id => $data ) {
            add_settings_section( $section_id,                   // ID
                                  $data['title'],                // Title
                                  [ $this, 'print_intro_info' ], // Callback
                                  static::$menu_slug );
            
            foreach( $data['fields'] as $id => $title ) {
                add_settings_field( $id,                        // ID
                                    $title,                     // Title
                                    [ $this, $id.'_callback' ], // Callback
                                    static::$menu_slug,         // Page
                                    $section_id // Section
                );
            }
        }
    }
    
    /**
     * @param $section
     */
    public function print_intro_info( $section ) {
        if( !isset( $section['id'] ) ) {
            return;
        }
        
        $section_id = $section['id'];
        if( !isset( $this->setting_list[ $section_id ] ) ) {
            return;
        }
        if( !isset( $this->setting_list[ $section_id ]['intro'] ) ) {
            return;
        }
        
        echo $this->setting_list[ $section_id ]['intro'];
    }
    
    /**
     * @param $input
     *
     * @return array
     */
    public function sanitize( $input ) : array {
        $new_input = [];
        foreach( $this->settingsList() as $data ) {
            if( empty( $data['fields'] ) ) continue;
            foreach( $data['fields'] as $id => $title ) {
                $value            = isset( $input[ $id ] ) ? $input[ $id ] : '';
                $new_input[ $id ] = $value;
            }
        }
        return $new_input;
    }
    
}