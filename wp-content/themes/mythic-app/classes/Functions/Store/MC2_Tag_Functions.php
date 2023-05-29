<?php

namespace Mythic\Functions\Store;

use Mythic\Abstracts\MC2_DB_Table;

class MC2_Tag_Functions extends MC2_DB_Table {

    protected static $table_name = 'tags';

    public function create_table_query() : string {
        return "CREATE TABLE `table_name` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
              `searchable_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
              PRIMARY KEY (`id`),
              KEY `searchable_name` (`searchable_name`),
              KEY `name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }

}