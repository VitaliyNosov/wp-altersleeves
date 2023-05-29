<?php

namespace Mythic\Functions\Tools\MaskCutter;

use Mythic\Abstracts\MC2_DB_Table;

/**
 * Class MC2_Canvas_Functions
 *
 * @package Mythic\Functions\MaskCutter
 */
class MC2_Element_Functions extends MC2_DB_Table {

    protected static $table_name = 'cutter_elements';

    /**
     * @return string
     */
    public function create_table_query() : string {
        return "CREATE TABLE `as_cutter_elements` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL DEFAULT '',
              `description` longtext,
              `locked` int(1) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `name` (`name`),
              KEY `locked` (`locked`)
            ) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;";
    }

}