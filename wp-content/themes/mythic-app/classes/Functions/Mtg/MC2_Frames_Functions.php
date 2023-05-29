<?php

namespace Mythic\Functions\Mtg;

use Mythic\Abstracts\MC2_DB_Table;
use Mythic\Objects\System\MC2_Taxonomy;

class MC2_Frames_Functions extends MC2_DB_Table {

    protected static $table_name = 'mtg_frames';
    public static $tax = 'mtg_frame';
    public static $singular_name = 'M:TG Frame';
    public static $name = 'M:TG Frames';

    public function actions() {
       MC2_Taxonomy::new(static::$tax,[ 'product' ], $this->get_taxonoMC2_args() );
    }

    /**
     * @return array
     */
    protected function get_taxonoMC2_args() : array {
        return [
            'labels' => [
                'name'          => static::$name,
                'singular_name' => static::$singular_name,
            ],
        ];
    }

    /**
     * @return string
     */
    public function create_table_query() : string {
        return "CREATE TABLE `table_name` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `framecode` varchar(240) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
              PRIMARY KEY (`id`),
              KEY `framecode` (`framecode`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }

}