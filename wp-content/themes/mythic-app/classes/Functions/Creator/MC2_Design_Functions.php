<?php

namespace Mythic\Functions\Creator;

use Mythic\Abstracts\MC2_DB_Table;

class MC2_Design_Functions extends MC2_DB_Table {

    protected static $table_name = 'designs';

    /**
     * @return string
     */
    public function create_table_query() : string {
        return "CREATE TABLE `table_name` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `creator` int(11) unsigned NOT NULL,
              `publisher` int(11) unsigned NOT NULL,
              `searchable_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT '',
              `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
              `product_type` int(10) unsigned NOT NULL,
              `generic` int(1) unsigned NOT NULL DEFAULT '1',
              `last_edited` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `creator` (`creator`),
              KEY `searchable_name` (`searchable_name`),
              KEY `publisher` (`publisher`),
              KEY `name` (`name`),
              KEY `product_type` (`product_type`),
              KEY `generic` (`generic`),
              KEY `last_edited` (`last_edited`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }

    /**
     * @return string[]
     */
    public function meta_tables() : array {
        return [
            'design_images',
            'design_mtg_card_relationships',
            'design_mtg_printing_relationships',
            'design_mtg_set_relationships',
            'design_mtg_tag_relationships',
        ];
    }

    /**
     * @return string[]
     */
    public function create_meta_table_queries() : array {
        return [
            'design_images'                  =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `design_id` int(11) unsigned NOT NULL,
                  `image_size` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `file_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `file` longtext COLLATE utf8mb4_unicode_ci,
                  `path` longtext COLLATE utf8mb4_unicode_ci,
                  `url` longtext COLLATE utf8mb4_unicode_ci,
                  PRIMARY KEY (`id`),
                  KEY `image_size` (`image_size`),
                  KEY `file_type` (`file_type`),
                  KEY `design_id` (`design_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'design_collection_relationships'   =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `collection_id` int(11) unsigned NOT NULL,
                  `design_id` int(11) unsigned NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `collection_id` (`collection_id`),
                  KEY `design_id` (`design_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'design_mtg_card_relationships'     =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `design_id` int(11) unsigned DEFAULT NULL,
                  `card_id` int(11) unsigned NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `card_id` (`card_id`),
                  KEY `design_id` (`design_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'design_mtg_printing_relationships' =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `design_id` int(11) unsigned NOT NULL,
                  `printing_id` int(11) unsigned NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `design_id` (`design_id`),
                  KEY `printing_id` (`printing_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'design_mtg_set_relationships'      =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `design_id` int(11) unsigned NOT NULL,
                  `set` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`),
                  KEY `set` (`set`),
                  KEY `design_id` (`design_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'design_mtg_tag_relationships'      =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                  `tag_id` int(11) unsigned DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `product` (`tag_id`),
                  KEY `sku` (`sku`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
        ];
    }

}