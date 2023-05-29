<?php

namespace Mythic_Core\Ajax\Search;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Search_Functions;

/**
 * Class MC_Ajax_Search
 *
 * @package Mythic_Core\Ajax\Search
 */
class MC_Ajax_Search extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response = [ 'status' => 0 ];
        if( empty( $_POST['type'] ) ) {
            $this->error( 'type is required' );
        }
        
        $type = $_POST['type'];
        $page = !empty( $_POST['page'] ) ? $_POST['page'] : 1;
        set_query_var( 'paged', $page );
        $GLOBALS['mc_ajax_search_paged'] = $page;
        
        $search_args = [
            'type'   => $type,
            'search' => !empty( $_POST['search'] ) ? $_POST['search'] : '',
        ];
        
        if( !empty( $_POST['currentPageType'] ) ) {
            if( $_POST['currentPageType'] == 'affiliatesControl' || $_POST['currentPageType'] == 'financeControl' ) {
                $response['data'] = MC_Search_Functions::asDisplaySearchControl( $search_args, false );
            }
        } else {
            switch( $type ) {
                case 'card':
                    $search_args['card_id'] = $_POST['element_id'];
                    $search_args['orderby'] = !empty( $_POST['orderby'] ) ? $_POST['orderby'] : 'title';
                    $search_args['order']   = !empty( $_POST['order'] ) ? $_POST['order'] : 'ASC';
                    break;
                
                case 'set':
                    $search_args['set_id'] = $_POST['element_id'];
                    break;
                
                case 'designs':
                    $search_args['form_data'] = static::prepareDesignsSidebarData();
                    break;
            }
            
            $response['data'] = MC_Search_Functions::asDisplaySearchPageContent( $search_args, false );
        }
        
        $response['status'] = 1;
        
        $this->success( $response );
    }
    
    private static function prepareDesignsSidebarData() {
        $data              = [];
        $data['card_id']   = !empty( $_POST['form_data']['card_id'] ) ? $_POST['form_data']['card_id'] : 0;
        $data['set_id']    = !empty( $_POST['form_data']['set_id'] ) ? $_POST['form_data']['set_id'] : 0;
        $data['artist_id'] = !empty( $_POST['form_data']['artist_id'] ) ? $_POST['form_data']['artist_id'] : 0;
        $data['tag_id']    = !empty( $_POST['form_data']['tag_id'] ) ? $_POST['form_data']['tag_id'] : [];
        
        return $data;
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'asSearch';
    }
    
}
