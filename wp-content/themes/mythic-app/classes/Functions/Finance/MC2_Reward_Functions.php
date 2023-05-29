<?php

namespace Mythic\Functions\Finance;

use Mythic\Abstracts\MC2_DB_Table;

/**
 * Class MC2_Reward_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Reward_Functions extends MC2_DB_Table {

    protected static $table_name = 'rewards';

    /**
     * @return string
     */
    public function create_table_query() : string {
        return "CREATE TABLE `table_name` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `admin_id` int(11) NOT NULL,
                  `date` date NOT NULL,
                  `amount` float(20,2) NOT NULL,
                  `notes` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `admin_id` (`admin_id`),
                  KEY `date` (`date`),
                  KEY `amount` (`amount`)
                ) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    }

    /**
     * @param int $user_id
     *
     * @return array|object|null
     */
    public static function get_by_user_id( int $user_id = 0 ) {
        return self::get_results( "user_id = $user_id" );
    }

}
