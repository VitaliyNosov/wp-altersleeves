<?php

namespace Mythic\Functions\Store\Pricing;

use Mythic\Abstracts\MC2_DB_Table;

class MC2_Price_Functions extends MC2_DB_Table {

    protected static $table_name = 'prices';

    /**
     * @return string
     */
    function create_table_query() : string {
        $table_name = $this->get_table_name();
        return "CREATE TABLE `$table_name` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `name` int(11) DEFAULT NULL,
              `usd` float DEFAULT NULL,
              `eur` float DEFAULT NULL,
              `discount_id` int(11) unsigned DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `name` (`name`),
              KEY `price_usd` (`usd`),
              KEY `price_eur` (`eur`),
              KEY `discount_id` (`discount_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }

}