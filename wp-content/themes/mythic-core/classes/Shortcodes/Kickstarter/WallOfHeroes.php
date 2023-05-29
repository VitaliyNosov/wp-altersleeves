<?php

namespace Mythic_Core\Shortcodes\Kickstarter;

use MC_Backer_Functions;
use MC_User;
use MC_WP;

class WallOfHeroes {
    
    public const SHORT_WALL_OF_HEROES = 'wall_of_heroes';
    
    /**
     * WallOfHeroes constructor.
     */
    public function __construct() {
        add_shortcode( self::SHORT_WALL_OF_HEROES, [ $this, 'generate' ] );
        add_shortcode( strtoupper( self::SHORT_WALL_OF_HEROES ), [ $this, 'generate' ] );
    }
    
    /**
     * @param array $args
     *
     * @return string
     */
    public function generate() {
        if( $this::backerValidity() ) {
            wp_register_script( 'cas-wall-of-heroes-js', AS_URI_JS.'/wall-of-heroes.js' );
            $backerDetails = [ 'user_id' => wp_get_current_user()->ID ];
            wp_localize_script( 'cas-wall-of-heroes-js', 'backerDetails', $backerDetails );
            wp_enqueue_script( 'cas-wall-of-heroes-js' );
        }
        
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/kickstarter/wall-of-heroes/layout.php' );
        return ob_get_clean();
    }
    
    /**
     * @return bool
     */
    public static function backerValidity() {
        if( !is_user_logged_in() ) return false;
        if( MC_User::isAdmin() ) return true;
        if( !MC_Backer_Functions::roleBacker() ) return false;
        global $current_user;
        $completedWall = MC_WP::meta( 'cas_wall_of_heroes', $current_user->ID, 'user' );
        if( $completedWall == 1 ) return false;
        return true;
    }
    
}
