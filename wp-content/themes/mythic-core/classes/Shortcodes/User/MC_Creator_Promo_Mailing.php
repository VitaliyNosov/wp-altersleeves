<?php

namespace Mythic_Core\Shortcodes\User;

use Mythic_Core\Abstracts\MC_Shortcode;
use Mythic_Core\Display\MC_Template_Parts;
use Mythic_Core\Functions\MC_User_Functions;

/**
 * Class MC_Creator_Promo_Mailing
 *
 * @package Mythic_Core\Shortcodes\User
 */
class MC_Creator_Promo_Mailing extends MC_Shortcode {

    public const SHORTCODE = 'mc_creator_promo_mailing';

    /**
     * Returns the shortcode slug
     *
     * @return string
     */
    public function getShortcode() : string {
        return strtolower( self::SHORTCODE );
    }

    /**
     * Generates the shortcode output
     *
     * @param array  $args
     * @param string $content
     *
     * @return string
     */
    public function generate( $args = [], $content = '' ) : string {
	    if( !MC_User_Functions::isContentCreator() ) return '';

      return MC_Template_Parts::get( 'creator/promo-mailing', 'promo-mailing-panel' );
    }
}
