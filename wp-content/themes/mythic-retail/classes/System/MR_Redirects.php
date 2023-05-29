<?php

namespace Mythic_Retail\System;

use Mythic_Core\System\MC_Redirects;
use Mythic_Core\Utils\MC_Url;

/**
 * Class MR_Redirects
 *
 * @package Mythic_Core\System
 */
class MR_Redirects {

    /**
     * MR_Redirects constructor.
     */
    public function __construct() {
        add_action( 'template_redirect', [ $this, 'redirects' ] );
    }

    public function redirects() {
        if( is_404() ) MC_Redirects::home();
        if( is_user_logged_in() && is_front_page() ) MC_Redirects::redirect('/dashboard/main' );
        if( !is_user_logged_in() && strpos(\MC_Server::primaryPath(), 'product' ) !== false ) MC_Redirects::home();
    }

}