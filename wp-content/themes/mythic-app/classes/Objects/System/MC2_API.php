<?php

namespace Mythic\Objects\System;

use Mythic\Abstracts\MC2_Abstract;

/**
 * The API blueprint. Build all API functionality off this.
 */
class MC2_API extends MC2_Abstract {
    
    public $api_namespace = 'ma-api/v1';
    public $api_key = 'JYR1YJNXJF223UG6XNGGX4KM';
    public $list_of_api_routes = [];
    
    /**
     * MC2_API constructor.
     */
    public function __construct() {
        if( empty( $this->list_of_api_routes ) ) return;
        
        $this->register_rest_api();
    }
    
    /**
     * Add routes registration action
     */
    public function register_rest_api() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }
    
    /**
     * Register API routes
     */
    public function register_routes() {
        foreach( $this->list_of_api_routes as $route ) {
            if( empty( $route[0] ) || empty( $route[1] ) || empty( $route[2] ) ) continue;

            register_rest_route( $this->api_namespace, $route[0], [
                [
                    'methods'             => $route[1],
                    'callback'            => [ $this, $route[2] ],
                    'permission_callback' => [ $this, !empty( $route[3] ) ? $route[3] : 'permission_callback' ],
                ]
            ] );
        }
    }
    
    /**
     * @param $request
     *
     * @return bool
     */
    public function permission_callback( $request ) {
        // @TODO: decide and configure API key using
        $request_headers = $request->get_headers();
        
        return true;
    }
    
    /**
     * @param $request
     *
     * @return mixed
     */
    public function get_body_params( $request ) {
        return json_decode( $request->get_body() );
    }
    
    /**
     * @param $data
     *
     * @return false|mixed|string|void
     */
    public function return_response( $data ) {
        return wp_send_json( $data );
    }
    
}