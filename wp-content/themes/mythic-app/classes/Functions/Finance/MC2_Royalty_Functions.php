<?php

namespace Mythic\Functions\Finance;

use Mythic\Abstracts\MC2_DB_Table;
use Mythic\Functions\Store\MC2_Order_Functions;
use Mythic\Functions\Wordpress\MC2_WP;
use Mythic\Helpers\MC2_Vars;
use Mythic\Objects\Finance\MC2_Royalty;
use ReflectionClass;
use WC_Order_Query;

/**
 * Class MC2_Royalty_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Royalty_Functions extends MC2_DB_Table {
    
    protected static $table_name = 'royalties';
    
    /**
     * @return string
     */
    public function create_table_query() : string {
        return "CREATE TABLE `table_name` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL DEFAULT '',
              `width` int(5) unsigned NOT NULL,
              `height` int(5) unsigned NOT NULL,
              PRIMARY KEY (`id`),
              KEY `name` (`name`),
              KEY `width` (`width`),
              KEY `height` (`height`)
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;";
    }

}