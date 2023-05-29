<?php

namespace Mythic_Core\System;

use WP_Error;
use WP_Taxonomy;

/**
 * Class MC_Taxonomies
 *
 * @package Mythic_Core\System
 */
class MC_Taxonomies {
    
    public function __construct() {
        add_action( 'init', [ $this, 'registerTaxonomies' ], 999 );
    }
    
    /**
     * @param array $taxonomy
     *
     * @return WP_Error|WP_Taxonomy
     */
    public static function register( $taxonomy = [] ) {
        $hierarchical  = $taxonomy['hierarchical'] ?? true;
        $metaBox       = $taxonomy['meta_box_cb'] ?? null;
        $public        = $taxonomy['public'] ?? true;
        $rewrite       = $taxonomy['rewrite'] ?? false;
        $showQuickEdit = $taxonomy['show_in_quick_edit'] ?? true;
        $showUi        = $taxonomy['show_ui'] ?? true;
        
        return register_taxonomy( $taxonomy['key'], $taxonomy['posts'], [
            'show_ui'            => $showUi,
            'show_in_quick_edit' => $showQuickEdit,
            'meta_box_cb'        => $metaBox,
            'label'              => $taxonomy['label'],
            'public'             => $public,
            'rewrite'            => $rewrite,
            'hierarchical'       => $hierarchical,
        ] );
    }
    
    public function registerTaxonomies() {
        $taxes = [
            [
                'key'   => 'product_group',
                'posts' => [ 'product' ],
                'label' => 'Product Groups',
            ],
            [
                'key'   => 'frame_code',
                'posts' => [ 'product', 'printing' ],
                'label' => 'Frame Codes',
            ],
            [
                'key'   => 'mtg_frame',
                'posts' => [ 'printing', 'product' ],
                'label' => 'M:TG Frames',
            ],
            [
                'key'   => 'mtg_set',
                'posts' => [ 'printing', 'product' ],
                'label' => 'M:TG Sets',
            ],
            [
                'key'                => 'mtg_card',
                'posts'              => [ 'printing', 'product' ],
                'label'              => 'M:TG Cards',
                'show_ui'            => false,
                'show_in_quick_edit' => false,
                'meta_box_cb'        => false,
            ],
            [
                'key'   => 'design_group',
                'posts' => [ 'product' ],
                'label' => 'Design Groups',
            ],
            [
                'key'   => 'alter_status',
                'posts' => [ 'product' ],
                'label' => 'Alter Status',
            ],
            [
                'key'   => 'alter_type',
                'posts' => [ 'design', 'product' ],
                'label' => 'Crop types',
            ],
            [
                'key'   => 'set_type',
                'posts' => [ 'product' ],
                'label' => 'Sleeves in Collection',
            ],
            [
                'key'   => 'design_type',
                'posts' => [ 'design', 'product' ],
                'label' => 'Design Types',
            ]
        ];
        $taxes = apply_filters( 'mc_taxonomies', $taxes );
        foreach( $taxes as $taxonomy ) self::register( $taxonomy );
    
    }
    
}