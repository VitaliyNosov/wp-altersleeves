<?php

namespace Mythic\Functions\Tools\MaskCutter;

use Mythic\Abstracts\MC2_DB_Table;

/**
 * Class MC2_Canvas_Functions
 *
 * @package Mythic\Functions\MaskCutter
 */
class MC2_Mask_Map_Functions extends MC2_DB_Table {

    protected static $table_name = 'cutter_mask_maps';

    /**
     * @return string
     */
    function create_table_query() : string {
        return "CREATE TABLE `table_name` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `canvas` int(11) DEFAULT NULL,
              `name` varchar(255) NOT NULL DEFAULT '',
              `framecode` varchar(255) NOT NULL DEFAULT '',
              PRIMARY KEY (`id`),
              KEY `canvas` (`canvas`),
              KEY `name` (`name`),
              KEY `framecode` (`framecode`)
            ) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;";
    }

    /**
     * @return string[]
     */
    function meta_tables() : array {
        return [ 'cutter_mask_elements' ];
    }

    /**
     * @return string
     */
    protected function create_meta_table_queries() : array {
        return [ 'cutter_mask_elements' =>
                     "CREATE TABLE `table_name` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `mask_map` int(11) unsigned NOT NULL,
                  `element` int(11) unsigned NOT NULL,
                  `variation` int(11) unsigned DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `mask` (`mask`),
                  KEY `element` (`element`),
                  KEY `variation` (`variation`)
                ) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8;"
            ];
    }

}