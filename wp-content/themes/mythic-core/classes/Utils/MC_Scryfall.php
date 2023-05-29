<?php

namespace Mythic_Core\Utils;

use MC_Mtg_Card_Functions;
use MC_Mtg_Printing_Functions;
use MC_Mtg_Set_Functions;

/**
 * Class MC_Scryfall
 *
 * @package Mythic_Core\Utils
 */
class MC_Scryfall {
    
    private $can_import;
    private $scryfall_data;
    public static $scryfall_path = ABSPATH.'/files/cards';
    public static $scryfall_path_png = ABSPATH.'/files/cards/imported/png';
    public static $scryfall_path_large = ABSPATH.'/files/cards/imported/large';
    public static $scryfall_path_normal = ABSPATH.'/files/cards/imported/normal';
    public static $scryfall_dir_png = '/files/cards/imported/png';
    public static $scryfall_dir_large = '/files/cards/imported/large';
    public static $scryfall_dir_normal = '/files/cards/imported/normal';
    public static $scryfall_file = ABSPATH.'/files/cards/imported/cards.json';
    
    /**
     * MC_Scryfall constructor.
     *
     * @param false $file
     * @param bool  $import
     * @param bool  $images
     */
    public function __construct( $file = true, $import = true, $images = true ) {
        $this->can_import();
        $this->init_path();
        $this->init_table();
        if( $file ) $this->retrieve_scryfall_file();
        if( $import ) $this->import_scryfall_data_to_table( $images );
        //$this->sync_scryfall_data_to_wordpress();
    }
    
    /**
     * Sets whether the site is allowed to run the import or not
     */
    public function can_import() {
        $this->can_import = 1 == get_current_blog_id();
    }
    
    /**
     * Makes directory required for storing files if needed
     */
    public function init_path() {
        if( !$this->can_import ) return;
        if( !is_dir( $dir = self::$scryfall_path ) ) mkdir( $dir, 0755, true );
        if( !is_dir( $dir = self::$scryfall_path_png ) ) mkdir( $dir, 0755, true );
        if( !is_dir( $dir = self::$scryfall_path_large ) ) mkdir( $dir, 0755, true );
        if( !is_dir( $dir = self::$scryfall_path_normal ) ) mkdir( $dir, 0755, true );
    }
    
    /**
     * Retrieves the Scryfall data from the bulk file endpoint: 'https://api.scryfall.com/bulk-data'
     */
    public function retrieve_scryfall_file() {
        if( !$this->can_import ) return;
        $data = file_get_contents( 'https://api.scryfall.com/bulk-data' );
        $data = json_decode( $data, ARRAY_A );
        if( empty( $data ) ) return;
        $jsons = $data['data'];
        foreach( $jsons as $json ) {
            if( $json['type'] != 'default_cards' ) continue;
            $url = $json['download_uri'];
            break;
        }
        if( empty( $url ) ) return;
        $json = file_get_contents( $url );
        if( empty( $json ) ) return;
        
        $fp = fopen( self::$scryfall_file, 'w' );
        fwrite( $fp, $json );
        fclose( $fp );
    }
    
    /**
     * Gets the scryfall data to an accessible variable
     *
     * @return array
     */
    public function get_scryfall_data() : array {
        if( empty( $this->scryfall_data ) ) $this->set_scryfall_data();
        return $this->scryfall_data ?? [];
    }
    
    /**
     * Sets the scryfall data to an accessible variable
     */
    public function set_scryfall_data() {
        if( !file_exists( $file = self::$scryfall_file ) ) $this->retrieve_scryfall_file();
        
        $data                = file_get_contents( $file );
        $this->scryfall_data = json_decode( $data, ARRAY_A );
    }
    
    /**
     * Imports the raw data to the wp_scryfall_cards table
     */
    public function import_scryfall_data_to_table( $images = false, $new_only = true ) {
        if( !$this->can_import ) return;
        $cards = $this->get_scryfall_data();
        if( !is_array( $cards ) ) return;
        foreach( $cards as $card ) {
            $scryfall_id = $card['id'];
            if( $new_only && self::existsByScryfallId( $scryfall_id ) ) continue;
            if( !self::is_scryfall_card_importable( $card ) ) continue;
            self::import_card_to_scryfall_table( $card, $images );
            self::importPrinting( $card );
        }
    }
    
    /**
     * Checks whether the card is allowed to be imported
     */
    public static function is_scryfall_card_importable( $card = [] ) : bool {
        $card = !is_array( $card ) ? (array) $card : $card;
        if( empty( $card ) ) return false;
        if( $card['layout'] == 'art_series' ) return false;
        if( strlen( $card['name'] ?? '' ) > 125 ) return false;
        if( strpos( strtolower( $card['set_name'] ?? '' ), 'promos' ) !== false ) return false;
        $errors = [ 'oversized', 'digital' ];
        foreach( $errors as $error ) if( !empty( $card[ $error ] ) ) return false;
        if( !in_array( 'paper', $card['games'] ?? [] ) ) return false;
        return true;
    }
    
    /**
     * @param string $scryfall_id
     */
    public static function import_card_by_scryfall_id( $scryfall_id = '' ) {
        if( empty( $scryfall_id ) || !is_string( $scryfall_id ) ) return;
        $url  = "https://api.scryfall.com/cards/$scryfall_id?format=json";
        $data = file_get_contents( $url );
        $card = json_decode( $data, ARRAY_A );
        if( empty( $card ) ) return;
        self::import_card_to_scryfall_table( $card, true );
        self::importPrinting( $card );
    }
    
    /**
     *
     * Imports the Scryfall Object to the main scryfall table accessible by all sites
     *
     * Scryfall Card images are only imported as jpg: png is only used for image processing and can be pulled on a 1 by 1 basis!
     *
     *
     * @param array $card
     * @param false $import_images
     *
     * @return int
     */
    private static function import_card_to_scryfall_table( $card = [], $import_images = false ) {
        global $wpdb;
        $table_name = "{$wpdb->prefix}scryfall_cards";
        
        $card  = !is_array( $card ) ? (array) $card : $card;
        $cards = self::convert_card_to_card_face_array( $card );
        if( empty( $cards ) ) return 0;
        
        $face    = 1;
        $effects = $card['frame_effects'] ?? [];
        foreach( $cards as $card ) {
            $images = $card['image_uris'];
            $info   = [
                'face'                => $face,
                'scryfall_id'         => $scryfall_id = $card['id'],
                'name'                => $name = $card['name'] ?? '',
                'searchable_name'     => $searchable_name = sanitize_title_with_dashes( $name ),
                'set_name'            => $set_name = $card['set_name'],
                'searchable_set_name' => sanitize_title_with_dashes( $set_name ),
                'set'                 => $set_code = $card['set'],
                'collector_number'    => $cn = $card['collector_number'],
                'lang'                => $lang = $card['lang'] ?? 'en',
                'release_date'        => $card['released_at'],
                'type_line'           => $type_line = $card['type_line'],
                'card_type'           => self::type_line_to_card_type( $type_line ),
                'border_color'        => $card['border_color'],
                'frame'               => $card['frame'],
                'frame_effects'       => !empty( $effects ) ? serialize( $effects ) : '',
                'layout'              => $card['layout'] ?? 2015,
                'full_art'            => !empty( $card['full_art'] ) ? 1 : 0,
                'textless'            => !empty( $card['textless'] ) ? 1 : 0,
                'foil_stamp'          => self::card_has_foil_stamp( $card ),
                'edhrec_rank'         => $card['edhrec_rank'] ?? 0,
                'framecode'           => self::framecode_from_card( $card ),
            ];
            $result = $wpdb->update( $table_name, $info, [ 'scryfall_id' => $scryfall_id, 'face' => $face ] );
            if( empty( $result ) ) {
                $result = $wpdb->get_row( "SELECT * FROM $table_name WHERE scryfall_id = '$scryfall_id' AND face = '$face' " );
                if( empty( $result ) ) $wpdb->insert( $table_name, $info );
            }
            if( !empty( $import_images ) ) {
                $images = [
                    'png'        => [
                        'file' => $images['png'],
                        'path' => self::$scryfall_path_png,
                        'dir'  => self::$scryfall_dir_png,
                    ],
                    'jpg_large'  => [
                        'file' => $images['large'],
                        'path' => self::$scryfall_path_large,
                        'dir'  => self::$scryfall_dir_large,
                    ],
                    'jpg_normal' => [
                        'file' => $images['normal'],
                        'path' => self::$scryfall_path_normal,
                        'dir'  => self::$scryfall_dir_normal,
                    ],
                ];
                foreach( $images as $type => $image ) {
                    $image_file = $image['file'];
                    $file_name  = $set_code.'-'.$cn.'-'.$lang.'-'.$face.'-'.sanitize_file_name( $searchable_name );
                    $path       = $image['path'].'/'.$file_name.'.jpg';
                    if( file_exists( $path ) && filesize( $path ) > 0 && ( time() - filectime( $path ) ) < 86400 && filesize( $path ) < 1000000 ) continue;
                    
                    $dir = $image['dir'].'/'.$file_name.'.jpg';
                    if( $type == 'png' ) {
                        $path = str_replace( '.jpg', '.png', $path );
                        $dir  = str_replace( '.jpg', '.png', $dir );
                    }
                    
                    $content = file_get_contents( $image_file );
                    if( empty( $content ) ) continue;
                    if( $path && file_exists( $path ) ) unlink( $path );
                    
                    $fp = fopen( $path, "w" );
                    fwrite( $fp, $content );
                    fclose( $fp );
                    
                    MC_Images::compress( $path, $type == 'png' ? 55 : 45 );
                    $wpdb->update( $table_name, [ $type => $dir ], [ 'scryfall_id' => $scryfall_id, 'face' => $face ] );
                }
            }
            $face++;
            
            $wpdb->update( $table_name, [ 'imported' => date( 'Y-m-d H:i:s' ) ], [ 'scryfall_id' => $scryfall_id, 'face' => $face ] );
        }
        return count( $cards );
    }
    
    /**
     * @param string $scryfall_id
     *
     * @return bool
     */
    public static function existsByScryfallId( $scryfall_id = '' ) {
        global $wpdb;
        $table_name = $wpdb->prefix."scryfall_cards";
        $result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE scryfall_id = '$scryfall_id' and face = 1" );
        return !empty( $result );
    }
    
    /**
     * Converts a card into an array with appropriate number of faces
     *
     * @param array $card
     */
    public static function convert_card_to_card_face_array( $card = [] ) {
        $faces = $card['card_faces'] ?? [];
        if( empty( $faces ) ) return [ $card ];
        $cards = [];
        foreach( $faces as $face ) {
            $base_card_detail = $card;
            unset( $base_card_detail['card_faces'] );
            $cards[] = array_merge( $base_card_detail, $face );
        }
        return $cards;
    }
    
    /**
     * @param array $card
     *
     * @return int
     */
    public static function card_has_foil_stamp( $card = [] ) : int {
        $has_stamp = $card['frame'] ?? 0 > 2002 && in_array( $card['rarity'], [ 'mythic', 'legendary' ] );
        return !empty( $has_stamp ) ? 1 : 0;
    }
    
    /**
     *
     * Converts the card's type line into an indexable card type
     *
     * @param string $type_line
     *
     * @return string
     */
    public static function type_line_to_card_type( $type_line = '' ) {
        $types = [
            'Legendary Artifact Creature' => 'creature',
            'Artifact Creature'           => 'creature',
            'Artifact Land'               => 'non_creature',
            'Tribal Artifact'             => 'non_creature',
            'Artifact'                    => 'non_creature',
            'Legendary Snow Land'         => 'non_creature',
            'Basic Land'                  => 'basic_land',
            'Basic Snow Land'             => 'basic_land',
            'Land Creature'               => 'creature',
            'Land'                        => 'basic_land',
            'Enchantment Creature'        => 'creature',
            'Legendary Creature'          => 'creature',
            'Creature'                    => 'creature',
            'Enchantment'                 => 'non_creature',
            'Instant'                     => 'non_creature',
            'Legendary Artifact'          => 'non_creature',
            'Legendary Enchantment'       => 'non_creature',
            'Legendary Land'              => 'non_creature',
            'Legendary Planeswalker'      => 'planeswalker',
            'Legendary Snow Enchantment'  => 'non_creature',
            'Legendary Sorcery'           => 'non_creature',
            'Sorcery'                     => 'non_creature',
            'Summon'                      => 'creature',
            'Planeswalker'                => 'planeswalker',
            'Tribal Enchantment'          => 'non_creature',
            'Tribal Instant'              => 'non_creature',
            'Tribal Sorcery'              => 'non_creature',
            'World Enchantment'           => 'non_creature',
        ];
        foreach( $types as $type => $result ) {
            if( strpos( $type_line, $type ) !== false ) continue;
            return $result;
        }
        return '';
    }
    
    /**
     *
     * Generates the unique framecode used by the site for cards
     *
     * @param string $card
     *
     * @return string
     */
    public static function framecode_from_card( $card = [] ) : string {
        $template_elements = [];
        // Full Art
        $template_elements['fa'] = !empty( $card['full_art'] ) ? 1 : 0;
        // Full Art
        $template_elements['fe'] = !empty( $frame_effects = $card['frame_effects'] ?? '' ) ? implode( $frame_effects ) : 0;
        // Foil Stamp
        $template_elements['fs'] = self::card_has_foil_stamp( $card );
        // Full Art
        $template_elements['layout'] = $card['layout'];
        // Textless - Reversed to table value for abbreviation
        $template_elements['txt'] = !empty( $card['textless'] ) ? 0 : 1;
        ksort( $template_elements );
        
        $framecode = $card['frame'].'-'.self::type_line_to_card_type( $card['type_line'] );
        foreach( $template_elements as $key => $template_element ) {
            $framecode .= '-'.$key.'-'.$template_element;
        }
        $framecode = strtolower( $framecode );
        $framecode = sanitize_title_with_dashes( $framecode );
        $vowels    = [ "a", "e", "i", "o", "u" ];
        return str_replace( $vowels, "", $framecode );
    }
    
    /**
     * @param string $scryfall_id
     *
     * @return array|object|void|null
     */
    public static function get_card_by_scryfall_id( $scryfall_id = '' ) {
        global $wpdb;
        $table_name = $wpdb->prefix."scryfall_cards";
        $result     = $wpdb->get_row( "SELECT * FROM $table_name WHERE scryfall_id = '$scryfall_id'" );
        if( !empty( $result ) ) return $result;
        MC_Scryfall::import_card_by_scryfall_id( $scryfall_id );
        return $wpdb->get_row( "SELECT * FROM $table_name WHERE scryfall_id = '$scryfall_id'" );
    }
    
    /**
     * Creates the scryfall card table if needed
     */
    public function init_table() {
        if( !$this->can_import ) return;
        global $wpdb;
        
        $table_name = $wpdb->prefix."scryfall_cards";
        
        $sql = "CREATE TABLE `$table_name` (
              `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
              `scryfall_id` varchar(128) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
              `face` int(1) NOT NULL DEFAULT '1',
              `name` varchar(128) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
              `searchable_name` varchar(128) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
              `set_name` varchar(128) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
              `searchable_set_name` varchar(128) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
              `set` varchar(5) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
              `collector_number` mediumint(3) NOT NULL,
              `lang` varchar(10) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT 'en',
              `release_date` datetime NOT NULL,
              `type_line` varchar(128) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
              `card_type` varchar(30) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
              `border_color` varchar(30) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT 'black',
              `frame` mediumint(5) NOT NULL DEFAULT '2015',
              `frame_effects` longtext COLLATE utf8_unicode_520_ci,
              `layout` varchar(64) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT 'normal',
              `full_art` int(1) NOT NULL DEFAULT '0',
              `textless` int(1) NOT NULL DEFAULT '0',
              `foil_stamp` int(1) NOT NULL DEFAULT '0',
              `framecode` varchar(64) COLLATE utf8_unicode_520_ci NOT NULL DEFAULT '',
              `edhrec_rank` mediumint(3) NOT NULL DEFAULT '0',
              `png` longtext COLLATE utf8_unicode_520_ci,
              `jpg_large` longtext COLLATE utf8_unicode_520_ci,
              `jpg_normal` longtext COLLATE utf8_unicode_520_ci,
              `imported` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `scryfall_id` (`scryfall_id`),
              KEY `face` (`face`),
              KEY `searchable_name` (`searchable_name`),
              KEY `searchable_set_name` (`searchable_set_name`),
              KEY `set` (`set`),
              KEY `collector_number` (`collector_number`),
              KEY `lang` (`lang`),
              KEY `release_date` (`release_date`),
              KEY `type_line` (`type_line`),
              KEY `card_type` (`card_type`),
              KEY `border_color` (`border_color`),
              KEY `frame` (`frame`),
              KEY `edhrec_rank` (`edhrec_rank`),
              KEY `full_art` (`full_art`),
              KEY `textless` (`textless`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_520_ci;";
        
        require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
    
    /**
     * @param array $printing
     * @param array $args
     *
     * @return array
     */
    public static function importPrinting( $printing = [], $args = [] ) : array {
        $response = [];
        
        //$images = $args['images'] ?? 0;
        $images = true;
        $fresh  = $args['fresh'] ?? 0;
        
        if( empty( $printing ) || !is_array( $printing ) ) return $response;
        extract( $printing );
        $post_name  = $name.' ('.$set_name.' / '.$set.') - '.$collector_number;
        $prexisting = get_page_by_title( $post_name, OBJECT, 'printing' );
        
        if( empty( $prexisting ) ) {
            $printing_id = wp_insert_post( [
                                               'post_type'      => 'printing',
                                               'post_title'     => $post_name,
                                               'post_author'    => 1,
                                               'post_status'    => 'publish',
                                               'comment_status' => 'closed',
                                               'ping_status'    => 'closed',
                                           ] );
        } else {
            $printing_id = $prexisting->ID;
        }
        $meta = get_post_meta( $printing_id );
        
        $response['id']     = $printing_id;
        $response['name']   = $name;
        $response['set']    = $set_name;
        $response['time']   = $time = time();
        $response['images'] = $images;
        $response['fresh']  = $fresh;
        
        foreach( self::keys() as $scryfall_key ) {
            switch( $scryfall_key ) {
                case 'uri' :
                case 'mc_scryfall_uri';
                case 'mc_cmc';
                    continue 2;
                case 'frame_effects' :
                    if( empty( $printing['frame_effects'] ) ) continue 2;
                    $meta_key = 'mc_frame_effects';
                    break;
                case 'id' :
                    $meta_key = 'mc_scryfall_id';
                    break;
                case 'set' :
                    $meta_key = 'mc_set_code';
                    break;
                case 'name' :
                    $meta_key = 'mc_card_name';
                    break;
                default :
                    $meta_key = 'mc_'.$scryfall_key;
                    break;
            }
            $scryfall_value = $printing[ $scryfall_key ] ?? '';
            if( empty( $scryfall_value ) || empty( $meta_key ) ) continue;
            $meta_value = $meta[ $meta_key ] ?? '';
            if( empty( $fresh ) && $scryfall_value == $meta_value ) continue;
            if( empty( $scryfall_value ) ) continue;
            update_post_meta( $printing_id, $meta_key, $scryfall_value );
        }
        MC_Mtg_Printing_Functions::frameSync( $printing_id );
        
        $set_id = MC_Mtg_Set_Functions::idByCode( $set );
        wp_set_object_terms( $printing_id, [ $set_id ], 'mtg_set' );
        $card_id = MC_Mtg_Card_Functions::id( $name );
        wp_set_object_terms( $printing_id, [ $card_id ], 'mtg_card' );
        $name = MC_Vars::parseableString( $name );
        update_term_meta( $card_id, 'mc_searchable_name', $name );
        update_term_meta( $card_id, 'mc_search_term', $name );
        
        return $response;
    }
    
    /**
     * @return array
     */
    public static function keys() : array {
        return [
            'collector_number',
            'frame',
            'frame_effects',
            'full_art',
            'id',
            'layout',
            'name',
            'released_at',
            'rarity',
            'set',
            'set_name',
            'textless',
            'type_line'
        ];
    }
    
}