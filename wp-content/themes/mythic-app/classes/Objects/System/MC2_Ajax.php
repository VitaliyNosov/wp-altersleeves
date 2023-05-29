<?php

namespace Mythic\Objects\System;

use Mythic\Abstracts\MC2_Abstract;
use Mythic\Functions\Wordpress\MC2_Nonce_Functions;
use Mythic\Helpers\MC2_Functions;

/**
 * The Ajax blueprint. Build all ajax actions off this.
 */
class MC2_Ajax extends MC2_Abstract {

    public $args;
    public $action = '';
    public $nonce = '';
    public $function = null;


    public function __construct($params) {
        if( empty($params) ) return;
        $this->set_action($action = $params[0] ?? '');
        $this->set_function($params[1] ?? '');
        $this->set_nonce( $params[2] ?? $action );

        add_action( 'wp_ajax_'.$action, [ $this, 'init' ] );
        if( isset($public) && !$public ) return;
        add_action( 'wp_ajax_nopriv_'.$action, [ $this, 'init' ] );
    }

    /**
     * Executes the AJAX request
     */
    public function init() {
        $required = $this->get_required_values();
        foreach( $required as $required_single ) {
            if( !isset( $_POST[ $required_single ] ) ) $this->error( 'Required fields error' );
        }

        if( !empty( MC2_Nonce_Functions::verify( static::generate_nonce_action() ) ) ) {
            if( empty( $function = $this->get_function() ) ) $this->error( 'No function provided' );
            $this->success( MC2_Functions::run($function, $_POST) );
        }

        $this->error( 'Verify error' );
    }

    /**
     * @param null $function
     */
    public function set_function( $function ) {
        $this->function = $function;
    }

    /**
     * @return null
     */
    public function get_function() {
        return $this->function;
    }

    /**
     * @return bool|int
     */
    public static function verify( $action ) {
        if( is_admin() ) return true;

        if( empty( $_POST['mc_nonce'] ) ) return false;

        return wp_verify_nonce( $_POST['mc_nonce'], MC2_Nonce_Functions::generate_nonce_secret( $action ) );
    }

    /**
     * @return array
     */
    public function get_required_values() : array {
        return $this->args['required'] ?? [];
    }

    /**
     * Parses the arguments
     *
     * @param array $args
     *
     * @return array
     */
    protected function parse_args( array $args = [] ) : array {
        if( !is_iterable( $args ) ) $args = [];
        $defaults = $this->default_args();
        if( empty( $defaults ) ) return $args;

        return array_replace_recursive( $defaults, $args );
    }

    /**
     * Lists the default arguments
     *
     * @return array
     */
    protected function default_args() : array {
        return [];
    }

    /**
     * @param bool $echo
     */
    public function render_nonce( bool $echo = true ) {
        $action = $this->generate_nonce_action();
        MC2_Nonce_Functions::render( $action, $echo );
    }

    /**
     * @return string
     */
    protected function generate_nonce_action() : string {
        $action = $this->nonce;
        return !empty( $action ) ? $action : $this->action;
    }

    /**
     * @param string $action
     */
    protected function set_action( string $action = '' ) {
        if( strpos( $action, 'mc-' ) === false ) $action = "mc-$action";
        $this->action = $action;
    }

    /**
     * @return string
     */
    protected function get_action() : string {
        return $this->action;
    }

    /**
     * @param string $nonce
     */
    protected function set_nonce( string $nonce = '' ) {
        if( strpos( $nonce, 'mc-' ) === false ) $nonce = "mc-$nonce";
        $this->nonce = $nonce;
    }

    /**
     * @return string
     */
    protected function get_nonce() : string {
        return $this->nonce;
    }

    /**
     * Sends an error response
     *
     * @param     $message
     * @param int $code
     */
    public function error( $message, int $code = 400 ) {
        $this->response( [ 'error' => $message ], $code );
    }

    /**
     * Sends a JSON response
     *
     * @param array $data
     * @param int   $code
     */
    protected function response( array $data, int $code ) {
        header( 'Content-type: application/json' );
        echo json_encode( $data );
        wp_die( '', $code );
    }

    /**
     * Sends a success response
     *
     * @param     $data
     * @param int $code
     */
    public function success( $data, int $code = 200 ) {
        $this->response( $data, $code );
    }

}
