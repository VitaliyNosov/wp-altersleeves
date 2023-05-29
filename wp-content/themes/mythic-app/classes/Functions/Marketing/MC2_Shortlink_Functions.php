<?php

namespace Mythic\Functions\Marketing;

use Mythic\Abstracts\MC2_DB_Table;

class MC2_Shortlink_Functions extends MC2_DB_Table {

    protected static $table_name = 'shortlinks';

    /**
     * @param string $slug
     */
    public static function redirectFromSlug( $slug = '' ) {
        if( is_home() || is_front_page() ) return;
        $urls = self::getBySlug( $slug );
        if( empty( $urls ) ) return;
        $url = $urls[0];
        $url = $url->destination;
        wp_redirect( $url );
        exit();
    }

    /**
     * @param string $slug
     *
     * @return array|object|null
     */
    public static function getBySlug( $slug = '' ) {
        if( empty( $slug ) ) return [];
        global $wpdb;
        $table_name = self::TABLE_NAME;
        $query      = 'SELECT * FROM '.$table_name.' WHERE slug = "'.$slug.'";';
        return $wpdb->get_results( $query );
    }

    /**
     * @return string
     */
    function create_table_query() : string {
        return "CREATE TABLE `table_name` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `slug` varchar(100) NOT NULL DEFAULT '',
              `destination` varchar(255) NOT NULL DEFAULT '',
              `url` varchar(255) NOT NULL DEFAULT '',
              PRIMARY KEY (`id`),
              KEY `slug` (`slug`),
              KEY `destination` (`destination`),
              KEY `url` (`url`)
            ) ENGINE=InnoDB AUTO_INCREMENT=538 DEFAULT CHARSET=utf8;";
    }

}