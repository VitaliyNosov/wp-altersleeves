<?php

namespace Mythic_Core\Settings;

use Mythic_Core\Abstracts\MC_Settings_Page;
use Mythic_Core\System\MC_Settings;

/**
 * Class MC_Site_Settings
 *
 * @package Mythic_Core\Configuration
 */
class MC_Site_Settings extends MC_Settings_Page {
    
    public static $setting_group = 'mythic_core_site_settings';
    public static $setting_name = 'mythic_core_site_settings';
    public static $menu_slug = 'admin-mythic-core-site-settings';
    public static $title = 'Mythic Core Site Settings';
    public $setting_list = [];
    
    /**
     * @return array
     */
    public function settingsList() : array {
        return [
            'site' => [
                'title'  => 'Site Settings',
                'intro'  => 'Settings for operating the website',
                'fields' => [
                    'mf_video_embed' => __( 'Mythic Frames Video', MC_TEXT_DOMAIN ),
                    'live_url'       => __( 'Mythic Frames Video', MC_TEXT_DOMAIN )
                    /*
                    'enable_cart_page' => __('Enable Cart Page', MC_TEXT_DOMAIN),
                    'cookiebar'        => __('Cookie Bar Text', MC_TEXT_DOMAIN),
                    'copyright'        => __('Copyright Text', MC_TEXT_DOMAIN),
                    'disclaimer'       => __('Disclaimer Text', MC_TEXT_DOMAIN),
                    */
                ],
            ],
            /*
            'payment'    => [
                'title'  => __('Payment Settings', MC_TEXT_DOMAIN),
                'intro'  => __('Custom payment settings for Sleeve Alters', MC_TEXT_DOMAIN),
                'fields' => [
                    'stripe_pk' => __('Stripe Public Key', MC_TEXT_DOMAIN),
                    'stripe_sk' => __('Stripe Secret Key', MC_TEXT_DOMAIN),
                ],
            ],
            'newsletter' => [
                'title'  => 'Newsletter Signup Form',
                'intro'  => 'Options for the newsletter signup form',
                'fields' => [
                    'newsletter_title'             => __('Newsletter Title', MC_TEXT_DOMAIN),
                    'newsletter_subtitle'          => __('Newsletter Title', MC_TEXT_DOMAIN),
                    'newsletter_text'              => __('Newsletter Text (before form)', MC_TEXT_DOMAIN),
                    'newsletter_email_text'        => __('Email Placeholder', MC_TEXT_DOMAIN),
                    'newsletter_button_text'       => __('Button Text', MC_TEXT_DOMAIN),
                    'newsletter_disclaimer_text'   => __('Email Placeholder', MC_TEXT_DOMAIN),
                    'newsletter_name_field'        => __('Name Field available', MC_TEXT_DOMAIN),
                    'newsletter_confirmation_text' => __('Email Placeholder', MC_TEXT_DOMAIN),
                    'newsletter_sendinblue_list' => __('Sendinblue List ID', MC_TEXT_DOMAIN),
                ],
            ],
            */
        ];
    }
    
    public function newsletter_title_callback() {
        MC_Settings::input( [
                                'input_name'  => 'newsletter_title',
                                'option_name' => self::$setting_name,
                                'placeholder' => 'Sign up to the newsletter',
                                'type'        => 'text',
                            ] );
    }
    
    public function newsletter_subtitle_callback() {
        MC_Settings::input( [
                                'input_name'  => 'newsletter_subtitle',
                                'option_name' => self::$setting_name,
                                'placeholder' => 'and stay up to date',
                                'type'        => 'text',
                            ] );
    }
    
    public function newsletter_text_callback() {
        MC_Settings::input( [
                                'input_name'  => 'newsletter_text',
                                'option_name' => self::$setting_name,
                                'type'        => 'text',
                            ] );
    }
    
    public function newsletter_email_text_callback() {
        MC_Settings::input( [
                                'input_name'  => 'newsletter_email_text',
                                'option_name' => self::$setting_name,
                                'type'        => 'text',
                            ] );
    }
    
    public function newsletter_button_text_callback() {
        MC_Settings::input( [
                                'input_name'  => 'newsletter_button_text',
                                'option_name' => self::$setting_name,
                                'type'        => 'text',
                            ] );
    }
    
    public function newsletter_disclaimer_text_callback() {
        MC_Settings::inputEditor( 'newsletter_email_text', self::$setting_name, NEWSLETTER_DISCLAIMER );
    }
    
    public function newsletter_name_field_callback() {
        MC_Settings::selectYesNo( 'newsletter_name_field', self::$setting_name );
    }
    
    public function newsletter_confirmation_text_callback() {
        MC_Settings::inputEditor( 'newsletter_confirmation_text', self::$setting_name, NEWSLETTER_DISCLAIMER );
    }
    
    public function newsletter_sendinblue_list_callback() {
        MC_Settings::input( [
                                'input_name'  => 'newsletter_sendinblue_list',
                                'option_name' => self::$setting_name,
                                'type'        => 'number',
                                'placeholder' => 26,
                            ] );
    }
    
    public function live_url_callback() {
        MC_Settings::input( [
                                'input_name'  => 'live_url',
                                'option_name' => self::$setting_name,
                                'placeholder' => 'Enter URL here (no http/https)',
                                'type'        => 'text',
                            ] );
    }
    
    public function mf_video_embed_callback() {
        MC_Settings::input( [
                                'input_name'  => 'mf_video_embed',
                                'option_name' => self::$setting_name,
                                'placeholder' => 'Place embed code here',
                                'type'        => 'textarea',
                            ] );
    }
    
    public function enable_cart_page_callback() {
        MC_Settings::selectYesNo( 'enable_cart_page', self::$setting_name );
    }
    
    public function cookiebar_callback() {
        MC_Settings::inputEditor( 'cookiebar', self::$setting_name, 'Enter your preferred cookiebar text here' );
    }
    
    public function copyright_callback() {
        MC_Settings::input( [
                                'input_name'  => 'copyright',
                                'option_name' => self::$setting_name,
                                'placeholder' => 'Mythic Gaming - All Rights Reserved',
                                'type'        => 'text',
                            ] );
    }
    
    public function disclaimer_callback() {
        MC_Settings::inputEditor( 'disclaimer', self::$setting_name, 'Enter here any legal disclaimers for the footer' );
    }
    
    public function stripe_pk_callback() {
        MC_Settings::input( [
                                'input_name'  => 'stripe_pk',
                                'option_name' => self::$setting_name,
                                'placeholder' => 'Enter Stripe Public Key here',
                                'type'        => 'text',
                            ] );
    }
    
    public function stripe_sk_callback() {
        MC_Settings::input( [
                                'input_name'  => 'stripe_sk',
                                'option_name' => self::$setting_name,
                                'placeholder' => 'Enter Stripe Secret Key here',
                                'type'        => 'text',
                            ] );
    }
    
}
