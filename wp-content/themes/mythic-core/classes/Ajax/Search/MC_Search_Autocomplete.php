<?php

namespace Mythic_Core\Ajax\Search;

use MC_Mtg_Card_Functions;
use MC_Search_Functions;
use Mythic_Core\Abstracts\MC_AJAX;

/**
 * Class MC_Search_Autocomplete
 *
 * @package Mythic_Core\Ajax\Search
 */
class MC_Search_Autocomplete extends MC_AJAX {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response = [];
        
        $search_term                  = $_REQUEST['search_term'] ?? '';
        $response['search_term']      = $search_term = trim( $search_term );
        $response['cards']            = MC_Mtg_Card_Functions::queryForAutocomplete( $search_term );
        $response['artists']          = MC_Search_Functions::userIndexedSearch( $search_term, 'alterist', 5 );
        $response['content_creators'] = MC_Search_Functions::userIndexedSearch( $search_term, 'content_creator', 5 );
        
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'search-autocomplete';
    }
    
}
