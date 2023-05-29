<?php

namespace Mythic\Functions\API;

use Mythic\Objects\System\MC2_API;

class MC2_Global_Data extends MC2_API {
    
    public $list_of_api_routes = [
        'get_global_data' => [
            'global',
            'GET',
            'get_global_data'
        ]
    ];
    
    public function get_global_data() {
        $data = [
            'pageTitle' => 'test data from API'
        ];

        $this->return_response( $data );
    }
    
}