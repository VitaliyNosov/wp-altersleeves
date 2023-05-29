<?php

namespace Mythic_Core\Shortcodes\User;

use MC_User_Functions;
use Mythic_Core\Abstracts\MC_Shortcode;
use Mythic_Core\System\MC_Statuses;
use Mythic_Core\System\MC_WP;

/**
 * Class MC_Alter_Submit
 *
 * @package Mythic_Core\Shortcodes\Creator
 */
class MC_Alter_Submit extends MC_Shortcode {
    
    const SHORTCODE = 'mc_alter_submit';
    
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
        $idUser    = wp_get_current_user()->ID;
        $alterArgs = [
            'post_type'      => 'product',
            'post_status'    => MC_Statuses::keys(),
            'posts_per_page' => 1,
            'author'         => $idUser,
            'tax_query'      => [
                'RELATION' => 'AND',
                [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'simple',
                ],
                [
                    'taxonomy' => 'product_group',
                    'field'    => 'slug',
                    'terms'    => 'alter',
                ],
            ],
            'fields'         => 'ids',
        ];
        $alters    = get_posts( $alterArgs );
        $file      = '/creator/components/newcomer-notice.php';
        if( !empty( MC_WP::meta( 'permission_submit_alter', $idUser ) || !empty( $alters ) ) || MC_User_Functions::isAdmin() || MC_User_Functions::isContentCreator() ) {
            $file = '/creator/management/forms/alter/content.php';
        }
        ob_start();
        include DIR_THEME_TEMPLATE_PARTS.$file;
        return ob_get_clean();
    }
    
}