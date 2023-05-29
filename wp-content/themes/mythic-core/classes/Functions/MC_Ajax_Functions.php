<?php

namespace Mythic_Core\Functions;

/**
 * Class MC_Ajax_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Ajax_Functions {
    
    /**
     * @param bool $echo
     */
    public static function render_nonce( $action, $echo = true ) {
        if( !empty( $GLOBALS['mc_actions'][ $action ] ) ) return;
        $GLOBALS['mc_actions'][ $action ] = 1;
        $mc_action                        = static::generate_nonce_action( $action );
        
        wp_nonce_field( static::generateSecretForNonce( $action ), $mc_action, 0, $echo );
    }
    
    /**
     * @return bool|int
     */
    public static function verifyWpNonce( $action ) {
        if( is_admin() ) return true;
        
        if( empty( $_POST['mc_nonce'] ) ) return false;
        
        return wp_verify_nonce( $_POST['mc_nonce'], static::generateSecretForNonce( $action ) );
    }
    
    /**
     * @return string
     */
    public static function generate_nonce_action( $action ) {
        return $action.'_action_mc';
    }
    
    /**
     * @param string $action
     *
     * @return string
     */
    public static function generateSecretForNonce( $action ) {
        return $action.'_secret_mc';
    }
    
}