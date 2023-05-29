<?php

namespace Mythic_Core\Ajax\Search;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Users\MC_Wp_User;

/**
 * Class MC_Ajax_Search_Autocomplete_Users
 *
 * @package Mythic_Core\Ajax\Search
 */
class MC_Ajax_Search_Autocomplete_Users extends MC_Ajax {
    
    /**
     * @return array
     */
    public function required_values() : array {
        return [ 'search_term' ];
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response    = [ 'status' => 0 ];
        $search_term = trim( $_REQUEST['search_term'] ?? '' );
        $role        = [];
        if( !empty( $_POST['role_search'] ) ) {
            switch( $_POST['role_search'] ) {
                case 'affiliate':
                    $role = [ 'content_creator' ];
                    break;
                case 'alterist':
                    $role = [ 'alterist' ];
                    break;
            }
        }
        $response['users'] = MC_Wp_User::mcSearchUsers( $search_term, 20, 0, $role );
        
        $response['status'] = 1;
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'searchAutocompleteUsers';
    }
    
}
