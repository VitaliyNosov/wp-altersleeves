<?php

namespace Mythic\Functions\Store\Pricing;

use Mythic\Abstracts\MC2_DB_Table;

class MC2_Discount_Functions extends MC2_DB_Table {

    protected static $table_name = 'discounts';

    /**
     * @return string
     */
    public function create_table_query() : string {
        return "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `name` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `value` int(11) DEFAULT NULL,
                  `type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `name` (`name`),
                  KEY `value` (`value`),
                  KEY `type` (`type`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }


}