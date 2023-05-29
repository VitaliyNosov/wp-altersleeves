<?php

namespace Mythic_Core\Ajax\Data;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\System\MC_Crons;
use Mythic_Core\Utils\MC_Sendinblue;

/**
 * Class MC_Capture_Newsletter_Signup
 *
 * @package Mythic_Core\Ajax\Data
 */
class MC_Capture_Newsletter_Signup extends MC_Ajax {
    
    public function execute() {
        MC_Crons::single( 'mc_sib_newsletter_signup', [ MC_Sendinblue::parseData( $_POST ) ] );
    }
    
    /**
     * @return string
     */
    public static function get_action_name() : string {
        return 'newsletter-signup';
    }
    
}