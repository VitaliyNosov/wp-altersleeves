<?php

namespace Mythic_Gaming\System;


use MC_Url;

/**
 * Class MG_Filters
 *
 * @package Mythic_Gaming\System
 */
class MG_Filters {

    /**
     * MG_Filters constructor.
     */
    public function __construct() {
        /** Wordpress Filters */
        add_filter( 'body_class', [ self::class, 'bodyClassMythicFrames' ] );
        add_filter( 'the_title', [ self::class, 'contentTitle' ], 10, 2 );

        /** Mythic Core Filters */
        add_filter( 'mc_disclaimer_filter', [ self::class, 'disclaimer' ] );
        add_filter( 'mc_header_sections', [ self::class, 'headerSections' ] );
        add_filter( 'mc_head_title_filter', [ self::class, 'headTitle' ] );
        add_filter( 'mc_layout_filter', [ self::class, 'layout' ] );

        /** Woo */
       add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
    }

    /**
     * @param $classes
     *
     * @return mixed
     */
    public static function bodyClassMythicFrames( $classes ) {
        $classes[] = MG_Content::isMythicFrames() ? 'mythic-frames' : 'mythic-gaming';

        return $classes;
    }

    public static function contentTitle( $title, $id ) {
        if( MC_Url::isLoginPage() ) return false;

        return $title;
    }

    /**
     * @param $disclaimer
     *
     * @return string
     */
    public static function disclaimer( $disclaimer ) : string {
        return do_shortcode( 'Magic: The Gathering, its logo, the planeswalker symbol, the [mtg_mana_symbols] symbols, the pentagon of colors, and all characters’ names and distinctive likenesses are property of Wizards of the Coast LLC in the USA and other countries. All Rights Reserved.' );
    }

    /**
     * @param $title
     *
     * @return string|void
     */
    public static function headTitle( $title ) : string {
        if( MG_Content::isMythicFrames() ) return 'Mythic Frames - Kickstarter coming soon';

        return $title;
    }

    /**
     * @return string[]
     */
    public static function headerSections() : array {
        return [
            'logo',
            'nav',
        ];
    }

    /**
     * @param string $layout
     *s
     *
     * @return string
     */
    public static function layout( $layout = 'default' ) : string {
        if( MC_Url::isCampaign() ) return 'campaign';
        if( MG_Content::isMythicFrames() ) return 'mythic-frames';
        if( MC_Url::isLoginPage() ) return 'login';
        if( MC_Url::isDashboard() ) return 'dashboard';

        return $layout;
    }

}