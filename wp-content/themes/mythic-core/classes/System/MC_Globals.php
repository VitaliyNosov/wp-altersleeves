<?php

namespace Mythic_Core\System;

use MC_Woo_Cart_Functions;
use Mythic_Core\Objects\MC_User;
use Mythic_Core\Users\MC_Affiliates;

/**
 * Class MC_Globals
 *
 * @package Mythic_Core\System
 */
class MC_Globals {
    
    /**
     * MC_Globals constructor.
     */
    public function __construct() {
        $this->images();
    }
    
    public function images() {
        global $mc_icon_logo;
        $icon_logo = MC_URI_ICON_LOGO;
    }
    
    /** User globals */
    public function users() {
        global $mc_cart_empty,
               $mc_user,
               $mc_user_admin,
               $mc_user_affiliate,
               $mc_user_creator,
               $mc_user_moderator,
               $mc_user_id,
               $mc_woo_filter;
        $mc_cart_empty     = MC_Woo_Cart_Functions::empty();
        $mc_user           = wp_get_current_user();
        $mc_user_id        = $mc_user->ID;
        $mc_user_admin     = MC_User::isAdmin();
        $mc_user_affiliate = MC_Affiliates::is();
        $mc_woo_filter     = !MC_WOO_ACTIVE;
    }
    
}