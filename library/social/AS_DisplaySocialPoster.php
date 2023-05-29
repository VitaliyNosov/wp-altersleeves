<?php

namespace Alter_Sleeves\Shortcodes\Social;

/**
 * Class ShowPassPicker
 *
 * @package Alter_Sleeves\Shortcodes
 */
class DisplaySocialPoster {

    public const SHORT_SOCIAL_POSTER = 'social_poster';

    public function __construct() {
        add_shortcode( self::SHORT_SOCIAL_POSTER, [ $this, 'generate' ] );
        add_shortcode( strtoupper( self::SHORT_SOCIAL_POSTER ), [ $this, 'generate' ] );
    }

    /**
     * @param array $args
     *
     * @return string
     */
    public function generate( $args = [] ) {

        wp_register_script( 'cas-social-poster-js', AS_URI_JS.'/social-poster.js', [ 'jquery' ] );
        wp_enqueue_script( 'cas-social-poster-js' );

        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/social/layouts/social-poster.php' );
        return ob_get_clean();
    }

}
