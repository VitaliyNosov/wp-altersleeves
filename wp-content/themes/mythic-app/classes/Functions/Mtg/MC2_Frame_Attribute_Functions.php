<?php

namespace Mythic\Functions\Mtg;

use Mythic\Abstracts\MC2_DB_Table;

class MC2_Frame_Attribute_Functions extends MC2_DB_Table {

    protected static $table_name = 'mtg_frame_attrs';
    public static $tax = 'mc_mtg_frame_attr';
    public static $name = 'M:TG Frame Attributes';

    /**
     * @return string
     */
    public function create_table_query() : string {
        $table_name = $this->get_table_name();
        return "CREATE TABLE `$table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`),
                  KEY `type` (`type`),
                  KEY `name` (`name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }

    /**
     * @return array
     */
    protected function create_meta_table_queries() : array {
        return [
            'mtg_frame_attr_relationships' => "CREATE TABLE `table_name` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
              `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
              PRIMARY KEY (`id`),
              KEY `type` (`type`),
              KEY `name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
        ];
    }

    /**
     * @return string[]
     */
    function meta_tables() : array {
        return [ 'mtg_frame_attr_relationships' ];
    }

}