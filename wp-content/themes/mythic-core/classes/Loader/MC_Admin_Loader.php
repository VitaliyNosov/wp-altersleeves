<?php

namespace Mythic_Core\Loader;

use MC_Comment_Functions;
use Mythic_Core\Settings\MC_Data_Settings;
use Mythic_Core\Settings\MC_Run_Commands;
use Mythic_Core\Settings\MC_Site_Settings;
use Mythic_Core\System\MC_Statuses;

/**
 * Class MC_Admin_Loader
 *
 * @package Mythic_Core\Loader
 */
class MC_Admin_Loader {
    
    /**
     * MC_AdminLoader constructor.
     */
    public function __construct() {
        add_action( 'admin_footer-edit.php', [ MC_Statuses::class, 'quickEdit' ] );
        add_action( 'admin_footer-post.php', [ MC_Statuses::class, 'edit' ] );
        add_action( 'admin_footer-post-new.php', [ MC_Statuses::class, 'edit' ] );
        add_action( 'admin_init', [ MC_Comment_Functions::class, 'removePostTypeSupport' ] );
        add_action( 'admin_menu', [ MC_Comment_Functions::class, 'removeEditPage' ] );
        $this->initClasses();
    }
    
    public function initClasses() {
        new MC_Site_Settings();
        new MC_Data_Settings();
        new MC_Run_Commands();
    }
    
}