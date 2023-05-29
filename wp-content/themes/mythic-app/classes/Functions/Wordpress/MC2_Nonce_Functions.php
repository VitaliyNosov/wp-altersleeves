<?php

namespace Mythic\Functions\Wordpress;

class MC2_Nonce_Functions {
    
    /**
     * @param      $action
     * @param bool $echo
     *
     * @return string
     */
    public static function render( $action, bool $echo = true ) : string {
        if( !empty( $GLOBALS['mc_actions'][ $action ] ) ) return '';
        $GLOBALS['mc_actions'][ $action ] = 1;
        $mc_action                        = static::generate_nonce_action( $action );
        
        return wp_nonce_field( static::generate_nonce_secret( $action ), $mc_action, 0, $echo );
    }
    
    /**
     *
     * Verify a wordpress nonce action
     *
     * @return bool|int
     */
    public static function verify( $action ) {
        if( is_admin() ) return true;
        
        if( empty( $_POST['mc_nonce'] ) ) return false;
        
        return wp_verify_nonce( $_POST['mc_nonce'], static::generate_nonce_secret( $action ) );
    }
    
    /**
     *
     * Returns suffixed nonce name to prevent clashes with external software (ie plugins, themes)
     *
     * @param $action
     *
     * @return string
     */
    public static function generate_nonce_action( $action ) : string {
        return $action.'_action_mc';
    }
    
    /**
     *
     * Returns suffixed nonce secret to prevent clashes with external software (ie plugins, themes)
     *
     * @param $action
     *
     * @return string
     */
    public static function generate_nonce_secret( $action ) : string {
        return $action.'_secret_mc';
    }
    
}