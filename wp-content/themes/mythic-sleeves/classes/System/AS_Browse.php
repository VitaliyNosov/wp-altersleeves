<?php

namespace Alter_Sleeves\System;

use Mythic_Core\Objects\MC_Mtg_Card;

class AS_Browse {
    
    /**
     * @return array
     */
    public static function params() : array {
        $params = [
            'browse_type'  => $_POST['browse_type'] ?? $_GET['browse_type'] ?? '',
            'search_term'  => $_POST['search_term'] ?? $_GET['search_term'] ?? '',
            'card_id'      => $_POST['card_id'] ?? $_GET['card_id'] ?? 0,
            'card_search'  => $_POST['card_search'] ?? $_GET['card_search'] ?? '',
            'set_id'       => $_POST['set_id'] ?? $_GET['set_id'] ?? 0,
            'printing_id'  => $_POST['printing_id'] ?? $_GET['printing_id'] ?? 0,
            'framecode_id' => $_POST['framecode_id'] ?? $_GET['framecode_id'] ?? 0,
            'artist_id'    => $_POST['artist_id'] ?? $_GET['artist_id'] ?? 0,
            //'sort' => $_POST['sort'] ?? $_GET['sort'] ?? '',
            //'sortby' => $_POST['sortby'] ?? $_GET['sortby'] ?? '',
            'target_page'  => $_POST['target_page'] ?? $_GET['target_page'] ?? 1,
        ];
        
        $params['card']      = !empty( $params['card_id'] ) ? new MC_Mtg_Card( $params['card_id'] ) : null;
        $params['card_name'] = !empty( $params['card_id'] ) ? $params['card']->name : $params['search_term'];
        
        return $params;
    }
    
}