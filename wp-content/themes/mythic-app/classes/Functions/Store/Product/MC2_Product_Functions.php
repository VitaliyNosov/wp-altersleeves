<?php

namespace Mythic\Functions\Store\Product;

use Mythic\Abstracts\MC2_DB_Table;
use Mythic\Objects\System\MC2_Action;

class MC2_Product_Functions extends MC2_DB_Table {
    
    protected static $table_name = 'products';
    
    /**
     * @return string
     */
    public function create_table_query() : string {
        return "CREATE TABLE `wp_mc_products` (
                  `id` int(11) unsigned NOT NULL,
                  `sku` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
                  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `searchable_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `product_type` int(10) unsigned NOT NULL,
                  `last_edited` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `price_id` int(11) unsigned DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `sku` (`sku`),
                  KEY `product_type` (`product_type`),
                  KEY `last_edited` (`last_edited`),
                  KEY `name` (`name`),
                  KEY `searchable_name` (`searchable_name`),
                  KEY `product_rate_id` (`price_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }
    
    /**
     * @return string[]
     */
    public function meta_tables() : array {
        return [
            'product_images',
            'product_mtg_card_relationships',
            'product_mtg_printing_relationships',
            'product_mtg_set_relationships',
            'product_mtg_site_relationships',
            'product_mtg_tag_relationships',
            'product_mtg_type_relationships',
            'product_types',
        ];
    }
    
    /**
     * @return string[]
     */
    public function create_meta_table_queries() : array {
        return [
            'product_images'                     =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `product_id` int(10) unsigned NOT NULL,
                  `image_size` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `file_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `file` longtext COLLATE utf8mb4_unicode_ci,
                  `path` longtext COLLATE utf8mb4_unicode_ci,
                  `url` longtext COLLATE utf8mb4_unicode_ci,
                  PRIMARY KEY (`id`),
                  KEY `image_size` (`image_size`),
                  KEY `file_type` (`file_type`),
                  KEY `product_id` (`product_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'product_mtg_card_relationships'     =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `sku` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `card_id` int(11) unsigned NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `card_id` (`card_id`),
                  KEY `sku` (`sku`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'product_mtg_printing_relationships' =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `sku` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `printing_id` int(11) unsigned NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `card_id` (`printing_id`),
                  KEY `sku` (`sku`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'product_mtg_set_relationships'      =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `sku` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `set` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`),
                  KEY `sku` (`sku`),
                  KEY `set` (`set`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'product_mtg_site_relationships'     =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `sku` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `product_id` int(11) unsigned NOT NULL,
                  `site_id` int(11) unsigned NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `sku` (`sku`),
                  KEY `product_id` (`product_id`),
                  KEY `site_id` (`site_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'product_mtg_tag_relationships'      =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                  `tag_id` int(11) unsigned DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `product` (`tag_id`),
                  KEY `sku` (`sku`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'product_mtg_type_relationships'     =>
                "CREATE TABLE `table_name` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `sku` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `product_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`),
                  KEY `product_type` (`product_type`),
                  KEY `sku` (`sku`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
            'product_types'                      =>
                "CREATE TABLE `table_name` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `product_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                  `price_id` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `product_type` (`product_type`),
                  KEY `name` (`name`),
                  KEY `product_rate_id` (`price_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
        ];
    }
    
    public function actions() {
        MC2_Action::new( 'init', [ $this, 'author_support' ] );
    }
    
    /**
     * Allows users to be authors of products in WP environment
     */
    public static function author_support() {
        add_post_type_support( 'product', 'author' );
    }
    
}