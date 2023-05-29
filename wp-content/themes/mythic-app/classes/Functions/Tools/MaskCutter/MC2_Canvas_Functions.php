<?php

namespace Mythic\Functions\Tools\MaskCutter;

use Mythic\Abstracts\MC2_DB_Table;

/**
 * Class MC2_Canvas_Functions
 *
 * @package Mythic\Functions\MaskCutter
 */
class MC2_Canvas_Functions extends MC2_DB_Table {

    protected static $table_name = 'cutter_canvases';

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