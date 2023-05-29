<?php

namespace Mythic_Template\Abstracts;

use Mythic_Template\Functions\MT_Ajax_Functions;

/**
 * Class MT_Ajax
 *
 * @package Mythic_Template\Abstracts
 */
abstract class MT_Ajax {

    /**
     * MT_Ajax constructor.
     */
    public function __construct() {
        add_action( 'wp_ajax_'.$this->get_action_name(), [ $this, 'execute' ] );
        if( !$this->isPublic() ) return;
        add_action( 'wp_ajax_nopriv_'.$this->get_action_name(), [ $this, 'execute' ] );
    }

    /**
     * @return string
     */
    protected abstract static function get_action_name() : string;

    /**
     * @return bool
     */
    protected function isPublic() : bool {
        return true;
    }

    /**
     * Executes the AJAX request
     */
    public function init() {
        $required = $this->required_values();
        if( !empty( $required ) ) {
            foreach( $required as $required_single ) {
                if( !isset( $_POST[ $required_single ] ) ) $this->error( 'Required fields error' );
            }
        }

        if( !empty( MT_Ajax_Functions::verifyWpNonce( static::generate_nonce_action() ) ) ) $this->execute();

        $this->error( 'Verify error' );
    }

    abstract protected function execute();

    /**
     * @return array
     */
    public function required_values() : array {
        return [];
    }

    /**
     * Returns a JSON response with an error message and exits php execution
     *
     * @param string $error
     */
    public function return_error( string $error ) {
        $this->return_response( [
                                   'error'   => $error,
                                   'success' => false,
                               ] );
    }

    /**
     * @param $response
     */
    public function return_response( $response ) {
        header( 'Content-type: application/json' );
        echo json_encode( $response );
        wp_die();
    }

    /**
     * Parses the arguments
     *
     * @param array $args
     *
     * @return array
     */
    protected function parse_args( $args = [] ) : array {
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
     * Sends an error response
     *
     * @param     $message
     * @param int $code
     */
    protected function error( $message, $code = 400 ) {
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
    protected function success( $data, $code = 200 ) {
        $this->response( $data, $code );
    }

    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return '';
    }

    /**
     * @param bool $echo
     */
    public static function render_nonce( $echo = true ) {
        $action = static::generate_nonce_action();
        MT_Ajax_Functions::render_nonce( $action, $echo );
    }

    /**
     * @return string
     */
    protected static function generate_nonce_action() {
        $action = static::get_nonce_name();
        //$action = !empty( $action ) ? $action : static::get_action_name();

        return $action;
    }

}
