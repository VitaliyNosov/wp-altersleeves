<?php

namespace Mythic\Functions\Mtg;

use Mythic\Abstracts\MC2_DB_Table;

/**
 * Class MC2_Set_Functions
 *
 * @package Mythic\Functions\Mtg
 */
class MC2_Set_Functions extends MC2_DB_Table {

    protected static $table_name = 'mtg_sets';
    public static $tax = 'mtg_set';
    public static $name = 'M:TG Sets';

    /**
     * @return array
     */
    protected function get_parameters() : array {
        return [
            'key'   => self::$tax,
            'posts' => [ 'product' ],
            'label' => self::$name,
        ];
    }

    /**
     * @return string
     */
    public function create_table_query() : string {
        return "CREATE TABLE `table_name` (
                  `id` bigint(11) NOT NULL,
                  `set` varchar(4) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
                  `name` varchar(255) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
                  `searchable_name` varchar(255) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
                  `released_at` datetime NOT NULL,
                  `icon` varchar(255) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`),
                  KEY `searchable_name` (`searchable_name`),
                  KEY `released_at` (`released_at`),
                  KEY `set` (`set`),
                  KEY `name` (`name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_520_ci;";
    }

}