<?php

namespace Mythic_Retail\System;

use Mythic_Core\Utils\MC_Url;

/**
 * Class MR_Filters
 *
 * @package Mythic_Retail\System
 */
class MR_Filters {

    /**
     * MR_Filters constructor.
     */
    public function __construct() {
        /** Mythic Core Filters */
        add_filter( 'mc_header_sections', [ self::class, 'headerSections' ] );
        add_filter( 'mc_layout_filter', [ self::class, 'layout' ] );

        /** Woo */
        add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
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
        if( MC_Url::isLoginPage() ) return 'login';
        if( MC_Url::isDashboard() ) return 'dashboard';
        return $layout;
    }

}