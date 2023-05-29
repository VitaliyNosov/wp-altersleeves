<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Objects\MC_User;

class MC_Backer_Functions {
    
    const NAME          = 'backer';
    const READABLE_NAME = 'Backer';
    
    /**
     * @return array
     */
    public static function getPostTypeSettings() : array {
        return [
            'name'                => self::NAME,
            'label'               => self::READABLE_NAME.'s',
            'label_singular'      => self::READABLE_NAME,
            'supports'            => [ 'title' ],
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 10,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'menu_icon'           => 'dashicons-universal-access',
            'can_export'          => false,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capability_type'     => 'post',
        ];
    }
    
    /**
     * @return bool
     */
    public static function roleBacker() {
        if( !is_user_logged_in() ) return false;
        if( MC_User_Functions::isAdmin() ) return true;
        $userEmail = wp_get_current_user()->user_email;
        if( get_page_by_title( $userEmail, ARRAY_A, 'backer' ) != null ) return true;
        if( MC_User::meta( '_download_credits' ) > 0 ) return true;
        
        return false;
    }
    
    /**
     * @return int
     */
    public static function remainingSingleCredits() : int {
        if( !is_user_logged_in() ) return 0;
        $remainingCredits = MC_User::meta( '_download_credits', wp_get_current_user()->ID );
        if( $remainingCredits > 0 ) return $remainingCredits;
        
        return 0;
    }
    
}