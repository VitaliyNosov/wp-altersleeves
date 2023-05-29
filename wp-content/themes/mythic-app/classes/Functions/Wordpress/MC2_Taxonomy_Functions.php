<?php

namespace Mythic\Functions\Wordpress;

use WP_Error;
use WP_Taxonomy;

/**
 * Class MC2_TaxonoMC2_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Taxonomy_Functions {

    /**
     * @param array $taxonomy
     *
     * @return WP_Error|WP_Taxonomy
     */
    public static function register( array $taxonomy = [] ) {
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
            'hierarchical'       => $hierarchical
        ] );
    }

}