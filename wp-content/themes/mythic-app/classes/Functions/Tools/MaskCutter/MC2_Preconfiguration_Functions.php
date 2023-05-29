<?php

namespace Mythic\Functions\Tools\MaskCutter;

use Mythic\Abstracts\MC2_DB_Table;

class MC2_Preconfiguration_Functions extends MC2_DB_Table {

    protected static $table_name = 'cutter_preconfigurations';

    /**
     * @return string
     */
    function create_table_query() : string {
        return "CREATE TABLE `table_name` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `mask` int(11) NOT NULL,
                  `preconfiguration` int(11) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `mask` (`mask`),
                  KEY `preconfiguration` (`preconfiguration`)
                ) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;";
    }

    /**
     * @return string
     */
    function create_meta_table_query() : string {
        return "CREATE TABLE `table_name` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `preconfiguration` int(11) unsigned NOT NULL,
                  `element` int(11) unsigned NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `preconfiguration` (`preconfiguration`),
                  KEY `element` (`element`)
                ) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=latin1;";
    }

}