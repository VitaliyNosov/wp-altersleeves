<?php

namespace Mythic_Core\System;

use Mythic_Core\Display\MC_Render;

/**
 * Class MC_Settings
 *
 * @package Mythic_Core\System
 */
class MC_Settings {
    
    /**
     * @param array $args
     */
    public static function renderPage( $args = [] ) {
        if( empty( $args['setting_list'] ) || empty( $args['setting_group'] ) || empty( $args['menu_slug'] ) ) {
            return;
        }
        $args['title'] = $args['title'] ?? '';
        $args['tab']   = $_GET['tab'] ?? $args['tab'] ?? '';
        MC_Render::adminPage( 'settings', $args );
    }
    
    /**
     * @param string $input_name
     * @param string $setting_name
     * @param string $placeholder
     */
    public static function inputEditor( $input_name = '', $setting_name = '', $placeholder = '' ) {
        $settings = get_option( $setting_name );
        $value    = $settings[ $input_name ] ?? $placeholder;
        
        $args = [
            'wpautop'       => false,
            'media_buttons' => false,
            'textarea_name' => $setting_name.'['.$input_name.']',
        ];
        
        wp_editor( $value, $setting_name, $args );
    }
    
    /**
     * @param string $input_name
     * @param string $setting_name
     * @param string $placeholder
     */
    public static function inputText( $input_name = '', $setting_name = '', $placeholder = '' ) {
        self::input( [
                         'input_name'  => $input_name,
                         'placeholder' => $placeholder,
                         'option_name' => $setting_name,
                         'type'        => 'text',
                     ] );
    }
    
    /**
     * @param string $input_name
     * @param string $setting_name
     * @param string $placeholder
     */
    public static function inputTextArea( $input_name = '', $setting_name = '', $placeholder = '' ) {
        self::input( [
                         'input_name'  => $input_name,
                         'placeholder' => $placeholder,
                         'option_name' => $setting_name,
                         'type'        => 'textarea',
                     ] );
    }
    
    /**
     * @param string $input_name
     * @param string $setting_name
     * @param string $placeholder
     */
    public static function inputUrl( $input_name = '', $setting_name = '', $placeholder = '' ) {
        self::input( [
                         'input_name'  => $input_name,
                         'placeholder' => $placeholder,
                         'option_name' => $setting_name,
                         'type'        => 'url',
                     ] );
    }
    
    /**
     * @param string $input_name
     * @param string $setting_name
     */
    public static function inputFile( $input_name = '', $setting_name = '' ) {
        self::input( [
                         'input_name'  => $input_name,
                         'option_name' => $setting_name,
                         'type'        => 'file',
                     ] );
    }
    
    /**
     * @param array $args
     */
    public static function input( $args = [] ) {
        if( empty( $args['input_name'] ) || empty( $args['option_name'] ) ) return;
        
        $input_name   = $args['input_name'];
        $setting_name = $args['option_name'];
        $settings     = get_option( $setting_name );
        $type         = $args['type'];
        if( $type != 'select' && $type != 'textarea' ) $type = 'input';
        
        $args = [
            'input_name'   => $input_name,
            'setting_name' => $args['option_name'],
            'settings'     => get_option( $setting_name ),
            'placeholder'  => $args['placeholder'] ?? '',
            'type'         => $args['type'] ?? 'text',
            'value'        => $settings[ $input_name ] ?? '',
            'rows'         => $args['rows'] ?? 10,
        ];
        MC_Render::adminField( $type, '', $args );
    }
    
    /**
     * @param string $input_name
     * @param string $setting_name
     */
    public static function selectFromPages( $input_name = '', $setting_name = '' ) {
        $pages = [];
        foreach( MC_WP::pages() as $page_id ) {
            $pages[] = [ 'value' => $page_id, 'label' => get_the_title( $page_id ) ];
        }
        self::select(                                                                                                                         [
                                                                                                                                                  'input_name'  => $input_name,
                                                                                                                                                  'option_name' => $setting_name,
                                                                                                                                                  'empty_value' => true,
                                                                                                                                              ],
                                                                                                                                              $pages );
    }
    
    /**
     * @param        $input_name
     * @param string $setting_name
     */
    public static function selectYesNo( $input_name, $setting_name = '' ) {
        self::select(                                                                                                                               [
                                                                                                                                                        'input_name'  => $input_name,
                                                                                                                                                        'option_name' => $setting_name,
                                                                                                                                                    ],
                                                                                                                                                    [
                                                                                                                                                        [
                                                                                                                                                            'value' => '0', 'label' => __( 'No',
                                                                                                                                                                                           MC_TEXT_DOMAIN )
                                                                                                                                                        ],
                                                                                                                                                        [
                                                                                                                                                            'value' => '1', 'label' => __( 'Yes',
                                                                                                                                                                                           MC_TEXT_DOMAIN )
                                                                                                                                                        ],
                                                                                                                                                    ] );
    }
    
    /**
     * @param array $args
     * @param array $options
     */
    public static function select( $args = [], $options = [] ) {
        if( empty( $args['input_name'] ) || empty( $args['option_name'] ) || empty( $options ) ) return;
        
        if( !empty( $args['empty_value'] ) ) {
            array_unshift( $options, [
                'value' => '',
                'label' => __( '- - Please Select - -', MC_TEXT_DOMAIN ),
            ] );
        }
        
        $setting_name = $args['option_name'];
        
        $args = [
            'input_name'   => $args['input_name'],
            'setting_name' => $setting_name,
            'settings'     => get_option( $setting_name ),
        ];
        MC_Render::adminField( 'select', '', $args );
    }
    
}