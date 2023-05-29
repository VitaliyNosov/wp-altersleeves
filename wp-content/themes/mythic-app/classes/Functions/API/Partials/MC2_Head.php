<?php

namespace Mythic\Functions\API\Partials;

use Mythic\Abstracts\MC2_API_Abstract;
use Mythic\Functions\API\MC2_API_Functions;
use Mythic\Functions\Website\MC2_Logo_Functions;
use Mythic\Helpers\MC2;

class MC2_Head extends MC2_API_Abstract {
    
    const CACHE_NAME = 'head';
    
    // @Todo Sergey you will need to update these as the API sees fit
    public $list_of_api_routes = [
        'get_global_data' => [
            'global',
            'GET',
            'get_global_data'
        ]
    ];
    
    /**
     * @param bool $cache
     *
     * @return array
     */
    public function get_data( $cache = true ) : array {
        if( $cache ) {
            $cached_data = MC2_API_Functions::get_cached_json_data( self::CACHE_NAME );
            if( MC2::array_keys_exists( [ 'favicon' ], $cached_data ) ) return $cached_data;
        }
        $data = [
            'favicon' => MC2_Logo_Functions::get_favicon_logo(),
        ];
        wp_send_json( $data );
        MC2_API_Functions::update_cached_json_file( self::CACHE_NAME, $data );
        return $this->prepare_response( $data );
    }
    
}