<?php

namespace Mythic_Core\Utils;

/**
 * Class MC_Database
 *
 * @package Mythic_Core\Utils
 */
class MC_Database {
    
    /**
     * @param string $table_name
     * @param int    $site_id
     *
     * @return string
     */
    public static function multisiteTable( string $table_name = '', int $site_id = 0 ) : string {
        if( empty( $table_name ) ) return '';
        global $wpdb;
        if( $site_id == 1 ) return $wpdb->prefix.$table_name;
        return $wpdb->prefix.$site_id.'_'.$table_name;
    }
    
}