<?php

namespace Mythic\Objects\System;

use Mythic\Abstracts\MC2_Abstract;

class MC2_Taxonomy extends MC2_Abstract {

    protected $args = [];
    protected $tax = '';
    protected $post_types = [];

    public function __construct( $params ) {
        if( !isset( $params[1] ) ) return;
        $this->set_tax( $params[0] );
        $this->set_post_types( $params[1] );
        $args = $params[2] ?? [];
        $this->set_args( $args );
        $this->process_args();
        add_action( 'init', [ $this, 'register' ] );
    }

    /**
     * @return array
     */
    public function get_args() : array {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function set_args( array $args ) {
        $this->args = $args;
    }

    /**
     * @return string
     */
    public function get_tax() : string {
        return $this->tax;
    }

    /**
     * @param mixed|string $tax
     */
    public function set_tax( $tax ) {
        $this->tax = $tax;
    }

    /**
     * @return array
     */
    public function get_post_types() : array {
        return $this->post_types;
    }

    /**
     * @param mixed $post_types
     */
    public function set_post_types( $post_types ) {
        $this->post_types = $post_types;
    }

    /**
     * @return \WP_Error|\WP_Taxonomy
     */
    public function register() {
        return register_taxonomy( $this->get_tax(), $this->get_post_types(), $this->get_args() );
    }

    public function process_args() {
        $args = $this->get_args();

        $defaults = [
            'show_ui'               => true,
            'show_ui_in_quick_edit' => true,
            'meta_box_cb'           => null,
            'label'                 => ucfirst( str_replace( '_', ' ', $this->get_tax() ) ),
            'public'                => true,
            'rewrite'               => false,
            'hierarchical'          => true,
        ];
        foreach( $defaults as $key => $default ) {
            if( array_key_exists( $key, $args ) ) continue;
            $args[ $key ] = $default;
        }
        $this->set_args( $args );
    }

}