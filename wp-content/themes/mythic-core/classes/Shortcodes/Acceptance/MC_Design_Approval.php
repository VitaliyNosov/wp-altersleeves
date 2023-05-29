<?php

namespace Mythic_Core\Shortcodes\Acceptance;

/**
 * Class MC_Design_Approval
 *
 * @package Mythic_Core\Shortcodes\Acceptance
 */
class MC_Design_Approval {
    
    public const SHORT_DESIGN_APPROVAL = 'mc_design_approval';
    
    public function __construct() {
        add_shortcode( self::SHORT_DESIGN_APPROVAL, [ $this, 'generate' ] );
        add_shortcode( strtoupper( self::SHORT_DESIGN_APPROVAL ), [ $this, 'generate' ] );
    }
    
    /**
     * @param array $args
     *
     * @return string
     */
    public function generate( $args = [] ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/acceptance/approval-new.php' );
        return ob_get_clean();
    }
    
}
