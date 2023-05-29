<?php

namespace Mythic\Functions\Tools\MaskCutter;

use Mythic\Abstracts\MC2_DB_Table;

/**
 * Class MC2_Canvas_Functions
 *
 * @package Mythic\Functions\MaskCutter
 */
class MC2_Variation_Functions extends MC2_DB_Table {

    protected static $table_name = 'cutter_variations';

    /**
     * @return string
     */
    public function create_table_query() : string {
        return "CREATE TABLE `table_name` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `element` int(11) unsigned NOT NULL,
              `name` varchar(255) NOT NULL DEFAULT '',
              `file` longtext NOT NULL,
              PRIMARY KEY (`id`),
              KEY `element` (`element`),
              KEY `name` (`name`)
            ) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8;";
    }

}