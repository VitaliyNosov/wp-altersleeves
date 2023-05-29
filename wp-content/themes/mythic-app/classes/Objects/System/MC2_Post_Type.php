<?php

namespace Mythic\Objects\System;

use Mythic\Abstracts\MC2_Abstract;
use Mythic\Helpers\MC2_Vars;

class MC2_Post_Type extends MC2_Abstract {

    public static $label_singular = '';
    public static $label_plural = '';

    /**
     * PostTypeAbstract constructor
     */
    public function __construct( ...$params ) {
        $this->register();
    }

    /**
     * Gets the custom post type slug
     *
     * @return string
     */
    public function get_slug() {
        return MC2_Vars::sanitize_with_underscores( $this->get_label() );
    }

    /**
     * Gets the custom post type label
     *
     * @return string
     */
    protected function get_label_singular() : string {
        return static::$label_singular;
    }

    /**
     * @return string
     */
    protected function get_label() : string {
        return static::$label_plural;
    }

    /**
     * Gets the custom post type arguments
     *
     * @return array
     */
    protected function get_args() : array {
        return [];
    }

    /**
     * Registers the custom post type
     */
    protected function register() {
        // set labels
        $label        = $this->get_label();
        $label_single = $this->get_label_singular();
        $post_type    = static::get_slug();

        // defaults
        $lc_label        = mb_strtolower( $label );
        $lc_label_single = mb_strtolower( $label_single );

        // make complete $args array
        $args = [
            // labels
            'label'               => _x( $lc_label, 'Post Type Label', MC2_TEXT_DOMAIN ),
            'description'         => _x( $label, 'Post Type Description', MC2_TEXT_DOMAIN ),
            'labels'              => [
                'name'               => _x( $label, 'Post Type General Name', MC2_TEXT_DOMAIN ),
                'singular_name'      => _x( $label_single, 'Post Type Singular Name', MC2_TEXT_DOMAIN ),
                'menu_name'          => _x( $label, 'Post Type label', MC2_TEXT_DOMAIN ),
                'parent_item_colon'  => _x( 'Parent Item:', 'Post Type label', MC2_TEXT_DOMAIN ),
                'all_items'          => _x( 'All '.$lc_label, 'Post Type label', MC2_TEXT_DOMAIN ),
                'view_item'          => _x( 'View '.$lc_label_single, 'Post Type label', MC2_TEXT_DOMAIN ),
                'add_new_item'       => _x( 'New '.$lc_label_single, 'Post Type label', MC2_TEXT_DOMAIN ),
                'add_new'            => _x( 'Add '.$lc_label_single, 'Post Type label', MC2_TEXT_DOMAIN ),
                'edit_item'          => _x( 'Edit '.$lc_label_single, 'Post Type label', MC2_TEXT_DOMAIN ),
                'update_item'        => _x( 'Update '.$lc_label_single, 'Post Type label', MC2_TEXT_DOMAIN ),
                'search_items'       => _x( 'Search '.$lc_label_single, 'Post Type label', MC2_TEXT_DOMAIN ),
                'not_found'          => _x( 'No '.$lc_label.' found', 'Post Type label', MC2_TEXT_DOMAIN ),
                'not_found_in_trash' => _x( 'No '.$lc_label.' found in the trash', 'Post Type label', MC2_TEXT_DOMAIN ),
            ],
            // rewrite
            'rewrite'             => [
                'slug'       => $post_type,
                'with_front' => true,
                'pages'      => true,
                'feeds'      => false,
            ],
            // options
            'supports'            => [ 'title', 'editor', 'thumbnail', 'author' ],
            'taxonomies'          => [],
            'hierarchical'        => true,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => '10',
            'menu_icon'           => '',
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        ];

        // combine arrays; custom args overwrite the default ones
        $customize = $this->get_args();
        if( !is_array( $customize ) ) $customize = [];
        $args = array_replace_recursive( $args, $customize );

        // register already!
        register_post_type( $post_type, $args );
    }

}