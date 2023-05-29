<?php

namespace Mythic_Core\System;

use Mythic_Core\Functions\MC_Backer_Functions;
use Mythic_Core\Functions\MC_Design_Functions;
use Mythic_Core\Functions\MC_Mtg_Printing_Functions;
use Mythic_Core\Functions\MC_Royalty_Functions;
use WP_Error;
use WP_Post_Type;

class MC_Post_Types {
    
    public function __construct() {
        add_action( 'init', [ $this, 'registerPostTypes' ], 999 );
    }
    
    public function registerPostTypes() {
        $post_types = [
            MC_Mtg_Printing_Functions::getPostTypeSettings(),
            MC_Backer_Functions::getPostTypeSettings(),
            MC_Design_Functions::getPostTypeSettings(),
            MC_Royalty_Functions::getPostTypeSettings(),
        ];
        $post_types = apply_filters( 'mc_post_types', $post_types );
        foreach( $post_types as $post_type ) self::register( $post_type );
    }
    
    /**
     * @param array $settings
     *
     * @return WP_Error|WP_Post_Type
     */
    public static function register( $settings = [] ) {
        $name           = $settings['name'];
        $args           = [];
        $args['labels'] = [
            'name'          => $settings['label'],
            'singular_name' => $settings['label_singular'],
        ];
        foreach( $settings as $key => $setting ) $args[ $key ] = $setting;
        return register_post_type(
            $name,
            $args
        );
    }
    
}



