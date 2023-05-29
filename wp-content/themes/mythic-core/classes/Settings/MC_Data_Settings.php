<?php

namespace Mythic_Core\Settings;

use Mythic_Core\Abstracts\MC_Settings_Page;
use Mythic_Core\System\MC_Settings;

/**
 * Class MC_Mythic_Settings
 *
 * @package Mythic_Core\Configuration
 */
class MC_Data_Settings extends MC_Settings_Page {
    
    public static $setting_group = 'mythic_core_data_settings';
    public static $setting_name = 'mythic_core_data_settings';
    public static $menu_slug = 'admin-mythic-core-data-settings';
    public static $title = 'Mythic Core Data Settings';
    
    /**
     * @return array
     */
    public function settingsList() : array {
        return [
            'tracking' => [
                'title'  => __( 'Tracking Settings', MC_TEXT_DOMAIN ),
                'intro'  => __( 'Set up tracking info below', MC_TEXT_DOMAIN ),
                'fields' => [
                    'facebook_pixel'        => __( 'Facebook Pixel', MC_TEXT_DOMAIN ),
                    'facebook_pixel_id'     => __( 'Facebook Pixel ID', MC_TEXT_DOMAIN ),
                    'facebook_access_token' => __( 'Facebook Access Token', MC_TEXT_DOMAIN ),
                    'gtm_head'              => __( 'GTM script after &lt;head&gt;', MC_TEXT_DOMAIN ),
                    'gtag_head'             => __( 'gtag.js script after &lt;head&gt;', MC_TEXT_DOMAIN ),
                    'gtm_body'              => __( 'GTM script after &lt;body&gt;', MC_TEXT_DOMAIN ),
                    'sendinblue_api_key'    => __( 'Sendinblue API Key', MC_TEXT_DOMAIN ),
                    'slack_token'           => __( 'Slack Token', MC_TEXT_DOMAIN ),
                ],
            ],
        ];
    }
    
    public function facebook_pixel_callback() {
        MC_Settings::inputTextArea( 'facebook_pixel', static::$setting_name );
    }
    
    public function facebook_pixel_id_callback() {
        $args = [
            'input_name'  => 'facebook_pixel_id',
            'option_name' => static::$setting_name,
            'placeholder' => 'Enter ID here',
            'type'        => 'text',
        ];
        MC_Settings::input( $args );
    }
    
    public function facebook_access_token_callback() {
        $args = [
            'input_name'  => 'facebook_access_token',
            'option_name' => static::$setting_name,
            'placeholder' => 'Enter access token here',
            'type'        => 'text',
        ];
        MC_Settings::input( $args );
    }
    
    public function gtm_head_callback() {
        MC_Settings::inputTextArea( 'gtm_head', static::$setting_name );
    }
    
    public function gtag_head_callback() {
        MC_Settings::inputTextArea( 'gtag_head', static::$setting_name );
    }
    
    public function gtm_body_callback() {
        MC_Settings::inputTextArea( 'gtm_body', static::$setting_name );
    }
    
    public function sendinblue_api_key_callback() {
        $args = [
            'input_name'  => 'sendinblue_api_key',
            'option_name' => static::$setting_name,
            'type'        => 'password',
        ];
        MC_Settings::input( $args );
    }
    
    public function slack_token_callback() {
        $args = [
            'input_name'  => 'slack_token',
            'option_name' => static::$setting_name,
            'type'        => 'password',
        ];
        MC_Settings::input( $args );
    }
    
}
