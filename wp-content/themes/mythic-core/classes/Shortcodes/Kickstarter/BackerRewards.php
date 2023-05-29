<?php

namespace Mythic_Core\Shortcodes\Kickstarter;

/**
 * Class BackerRewards
 *
 * @package Mythic_Core\Shortcodes\Kickstarter
 */
class BackerRewards {
    
    public const SHORT_BACKER_REWARDS = 'backer_rewards';
    
    /**
     * BackerRewards constructor.
     */
    public function __construct() {
        add_shortcode( self::SHORT_BACKER_REWARDS, [ $this, 'generate' ] );
        add_shortcode( strtoupper( self::SHORT_BACKER_REWARDS ), [ $this, 'generate' ] );
    }
    
    /**
     * @param array $args
     *
     * @return string
     */
    public function generate( $args = [] ) {
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/kickstarter/dashboard/rewards.php' );
        return ob_get_clean();
    }
    
}
