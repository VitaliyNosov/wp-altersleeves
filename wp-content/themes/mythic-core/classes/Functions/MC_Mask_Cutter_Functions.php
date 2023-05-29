<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Objects\MC_Alter;
use Mythic_Core\Objects\MC_Mtg_Printing;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Utils\MC_Url;

/**
 * Class MC_Mask_Cutter_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Mask_Cutter_Functions {
    
    const TABLE_ELEMENTS                  = 'as_cutter_elements';
    const TABLE_ELEMENT_VARIATIONS        = 'as_cutter_element_variations';
    const TABLE_MASK_ELEMENTS             = 'as_cutter_mask_elements';
    const TABLE_MASK_MAPS                 = 'as_cutter_mask_maps';
    const TABLE_PRECONFIGURATION_ELEMENTS = 'as_cutter_preconfiguration_elements';
    const TABLE_PRECONFIGURATIONS         = 'as_cutter_preconfigurations';
    
    /**
     * @return array|object
     */
    public static function getElements() {
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_elements ORDER BY name';
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @param string $type
     * @param int    $id
     */
    public static function deleteComponent( $type = '', $id = 0 ) {
        if( empty( $id ) ) return;
        global $wpdb;
        
        switch( $type ) {
            case 'map' :
                $table = 'as_cutter_mask_maps';
                break;
            case 'element' :
                $table = 'as_cutter_elements';
                break;
            case 'element-variation' :
                $table = 'as_cutter_element_variations';
                break;
            case 'preconfiguration' :
                $table = 'as_cutter_preconfigurations';
                break;
            default :
                return;
        }
        $wpdb->delete( $table, [ 'id' => $id ] );
    }
    
    /**
     * @param $term
     *
     * @return array|object
     */
    public static function getMaskMapById( $term = 0 ) {
        if( empty( $term ) ) return [];
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_mask_maps WHERE id = '.$term;
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results[0];
    }
    
    /**
     * @param $term
     *
     * @return array|object
     */
    public static function getMaskMapsByVariationId( $term = 0 ) {
        if( empty( $term ) ) return [];
        global $wpdb;
        $query   = 'SELECT DISTINCT mask FROM as_cutter_mask_elements WHERE variation = '.$term;
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        foreach( $results as $key => $result ) {
            $results[ $key ] = $result->mask;
        }
        
        return $results;
    }
    
    /**
     * @param $term
     *
     * @return array|object
     */
    public static function getVariationsByElementId( $term = 0 ) {
        if( empty( $term ) ) return [];
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_element_variations WHERE element = '.$term;
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @param int $map
     * @param int $element
     *
     * @return array|object
     */
    public static function getVariationByMapAndElement( $map = 0, $element = 0 ) {
        if( empty( $map ) || empty( $element ) ) return [];
        global $wpdb;
        $query   = "SELECT * FROM as_cutter_mask_elements WHERE mask = $map AND element = $element";
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        $variation_id = $results[0]->variation;
        if( empty( $variation_id ) ) return [];
        
        return self::getElementVariationById( $variation_id );
    }
    
    /**
     * @param $term
     *
     * @return array|object
     */
    public static function getElementVariationById( $term = 0 ) {
        if( empty( $term ) ) return [];
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_element_variations WHERE id = '.$term;
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results[0];
    }
    
    /**
     * @param int $printing_id
     * @param int $product_id
     *
     * @return array
     */
    public static function data( $printing_id = 0, $product_id = 0 ) : array {
        if( !empty( $printing_id ) ) {
            $framecode = MC_Mtg_Printing_Functions::getFrameCodeId( $printing_id );
            $mask_maps = [ self::getMaskMapByFramecode( $framecode ) ];
        } else {
            $printing_id = $_GET['printing_id'] ?? self::defaultPrinting();
            $framecode   = MC_Mtg_Printing_Functions::getFrameCodeId( $printing_id );
            $mask_maps   = [ self::getMaskMapByFramecode( $framecode ) ];
        }
        $unmapped_only = !empty( $mask_maps ) ? empty( $mask_maps[0]->printing ) : false;
        
        $data     = [];
        $printing = new MC_Mtg_Printing( $printing_id );
        foreach( $mask_maps as $key => $mask_map ) {
            if( empty( $mask_map ) && isset( $printing_id ) ) {
                $data[ $key ] = [ 'exampleCardPNGURL' => $printing->imgJpgNormal ];
                continue;
            }
            
            // Get the data
            $mask_map_id           = $mask_map->id;
            $mask_map_name         = $mask_map->name;
            $mask_map_framecode_id = $mask_map->framecode;
            
            $mask_map_printing_id    = !empty( $printing_id ) ? $printing_id : $mask_map->printing;
            $mask_map_printing_image = $printing->imgJpgNormal;
            
            $mask_map_data = [];
            // Initial Data
            $mask_map_data['id']                = $mask_map_id;
            $mask_map_data['frameCode']         = $mask_map_framecode_id;
            $mask_map_data['friendlyName']      = $mask_map_name;
            $mask_map_data['exampleCardPNGURL'] = $mask_map_printing_image;
            $mask_map_data['canvasWidth']       = self::canvasWidth();
            $mask_map_data['canvasHeight']      = self::canvasHeight();
            // Elements
            $mask_elements = self::getElementsByMaskMap( $mask_map_id );
            
            $mapped_elements = [];
            foreach( $mask_elements as $mask_element ) {
                $element_id           = $mask_element->element;
                $element              = self::getElementById( $element_id );
                $element_variation_id = $mask_element->variation;
                $element_variation    = self::getElementVariationById( $element_variation_id );
                
                $mask_element_data               = [];
                $mask_element_data['id']         = $element_id ?? 0;
                $mask_element_data['name']       = $element->name ?? '';
                $mask_element_data['desc']       = $element->description ?? '';
                $mask_png_url                    = $element_variation->file;
                $mask_element_data['maskPNGURL'] = $mask_png_url;
                $mask_element_data['isLocked']   = $element->name == 'Corners' ? true : false;
                $mapped_elements[]               = $mask_element_data;
            }
            ksort( $mapped_elements );
            $mapped_elements               = array_values( $mapped_elements );
            $mask_map_data['maskElements'] = $mapped_elements;
            
            //Preconfigurations
            $preconfigurations = self::getPreconfigurations( $unmapped_only );
            if( !empty( $preconfigurations ) ) {
                $mask_map_data['preconfigurations'] = [];
                foreach( $preconfigurations as $preconfiguration ) {
                    $preconfiguration_id       = $preconfiguration->id;
                    $preconfiguration_name     = $preconfiguration->name;
                    $preconfiguration_elements = self::getElementIdsByPreconfiguration( $preconfiguration_id );
                    $preconfiguration_keys     = [];
                    foreach( $mask_elements as $preconfiguration_key => $mask_element ) {
                        $mask_element_id = $mask_element->element;
                        if( in_array( $mask_element_id, $preconfiguration_elements ) ) $preconfiguration_keys[] = $preconfiguration_key;
                    }
                    $preconfiguration_default             = strtolower( $preconfiguration_name ) == 'unmapped';
                    $mask_map_data['preconfigurations'][] = [
                        'id'               => $preconfiguration_id,
                        'name'             => $preconfiguration_name,
                        'desc'             => $preconfiguration->description,
                        'maskElementsKeys' => $preconfiguration_keys,
                        'isDefault'        => $preconfiguration_default,
                    ];
                }
            }
            $data[ $key ] = $mask_map_data;
        }
        if( !empty( $product_id ) ) {
            $product_image = trim( MC_WP::meta( MC_Alter::META_FILE_ORIGINAL, $product_id ) );
            if( !empty( $product_image ) ) {
                $data['product_image_data']['image']     = static::mcCurlGetImageAsBase64( $product_image );
                $data['product_image_data']['file_name'] = basename( $product_image );
            }
        }
        
        return $data;
    }
    
    public static function mcCurlGetImage( $url ) {
        $ch = curl_init( $url );
        
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_BINARYTRANSFER, 1 );
        
        $data = curl_exec( $ch );
        curl_close( $ch );
        
        return $data;
    }
    
    public static function mcCurlGetImageAsBase64( $url ) {
        $imageData  = base64_encode( static::mcCurlGetImage( $url ) );
        $mime_types = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'odt'  => 'application/vnd.oasis.opendocument.text ',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'gif'  => 'image/gif',
            'jpg'  => 'image/jpg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'bmp'  => 'image/bmp'
        ];
        $ext        = pathinfo( $url, PATHINFO_EXTENSION );
        
        $a = '';
        if( array_key_exists( $ext, $mime_types ) ) {
            $a = $mime_types[ $ext ];
        }
        return 'data: '.$a.';base64,'.$imageData;
    }
    
    /**
     * @param $term
     *
     * @return array|object
     */
    public static function getMaskMapByFramecode( $term = 0 ) {
        if( empty( $term ) ) return [];
        if( is_string( $term ) && !is_numeric( $term ) ) {
            $framecode = get_term_by( 'name', $term, 'frame_code' );
            if( empty( $framecode ) ) return [];
            $term = $framecode->term_id;
        }
        
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_mask_maps WHERE framecode = '.$term;
        $results = $wpdb->get_results( $query );
        if( is_array( $results ) && !empty( $results ) ) return $results[0];
        $query   = 'SELECT * FROM as_cutter_mask_maps WHERE framecode = 0';
        $results = $wpdb->get_results( $query );
        
        return $results[0];
    }
    
    /**
     * @return int
     */
    public static function defaultPrinting() {
        return 156162;
    }
    
    /**
     * @return string
     */
    public static function canvasWidth() {
        return '2980';
    }
    
    /**
     * @return string
     */
    public static function canvasHeight() {
        return '4160';
    }
    
    /**
     * @param int $term
     *
     * @return array
     */
    public static function getElementsByMaskMap( $term = 0 ) {
        if( empty( $term ) ) return [];
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_mask_elements WHERE mask = '.$term;
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @param $term
     *
     * @return array|object
     */
    public static function getElementById( $term = 0 ) {
        if( empty( $term ) ) return [];
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_elements WHERE id = '.$term;
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results[0];
    }
    
    /**
     * @param false $unmapped_only
     *
     * @return array|object
     */
    public static function getPreconfigurations( $unmapped_only = false ) {
        global $wpdb;
        $query = 'SELECT * FROM as_cutter_preconfigurations';
        if( $unmapped_only ) $query .= ' WHERE name = "Unmapped"';
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @param int $term
     *
     * @return array
     */
    public static function getElementIdsByPreconfiguration( $term = 0 ) {
        if( empty( $term ) ) return [];
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_preconfiguration_elements WHERE preconfiguration = '.$term;
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        foreach( $results as $key => $result ) {
            $results[ $key ] = $result->element;
        }
        
        return $results;
    }
    
    /**
     * @param $term
     *
     * @return array|object
     */
    public static function getMaskMapByPrinting( $term = 0 ) {
        if( empty( $term ) ) return [];
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_mask_maps WHERE printing = '.$term;
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results[0];
    }
    
    /**
     * @return array|object
     */
    public static function getMaskMaps() {
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_mask_maps';
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results;
    }
    
    /**
     * @param $term
     *
     * @return array|object
     */
    public static function getPreconfigurationById( $term = 0 ) {
        if( empty( $term ) ) return [];
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_preconfigurations WHERE id = '.$term;
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results[0];
    }
    
    /**
     * @param $term
     *
     * @return array|object
     */
    public static function getPreconfigurationByName( $term = 0 ) {
        if( empty( $term ) ) return [];
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_preconfigurations WHERE name = "'.$term.'"';
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        
        return $results[0];
    }
    
    /**
     * @param string $component
     * @param int    $id
     */
    public static function templatePartButton( $component = '', $id = 0 ) {
        if( empty( $component ) ) return;
        include DIR_THEME_TEMPLATE_PARTS.'/tools/cutter/management/components/save-button.php';
    }
    
    public static function savePreconfiguration( $variables = [] ) {
        if( empty( $variables ) || empty( $_GET['save'] ) ) return 0;
        global $wpdb;
        
        $preconfiguration_id = $variables['id'];
        $name                = $variables['preconfiguration_name'] ?? '';
        $description         = $variables['description'] ?? '';
        $element_ids         = [];
        foreach( $variables as $key => $variable ) {
            if( strpos( $key, 'element_' ) !== false ) $element_ids[] = $variable;
        }
        
        if( empty( $preconfiguration_id ) ) {
            $wpdb->insert( self::TABLE_PRECONFIGURATIONS, [
                'name'        => $name,
                'description' => $description,
            ] );
            $preconfiguration_id = $wpdb->insert_id;
        } else {
            $wpdb->update( self::TABLE_PRECONFIGURATIONS, [
                'name'        => $name,
                'description' => $description,
            ],             [
                               'id' => $preconfiguration_id,
                           ] );
        }
        
        $prexisting_elements = self::getElementIdsByPreconfiguration( $preconfiguration_id );
        foreach( $prexisting_elements as $element_id ) {
            $wpdb->delete( self::TABLE_PRECONFIGURATION_ELEMENTS, [
                'preconfiguration' => $preconfiguration_id,
                'element'          => $element_id,
            ] );
        }
        
        // Sort elements
        foreach( $element_ids as $element_id ) {
            $wpdb->insert( self::TABLE_PRECONFIGURATION_ELEMENTS, [
                'preconfiguration' => $preconfiguration_id,
                'element'          => $element_id,
            ] );
        }
        
        return $preconfiguration_id;
    }
    
    public static function saveElement( $variables = [] ) {
        if( empty( $variables ) || empty( $_GET['save'] ) ) return 0;
        global $wpdb;
        
        $element_id  = $variables['id'];
        $name        = $variables['element_name'] ?? '';
        $description = $variables['description'] ?? '';
        $locked      = $variables['locked'] ?? 0;
        
        if( empty( $element_id ) ) {
            $element_id = $wpdb->insert( self::TABLE_ELEMENTS, [
                'name'        => $name,
                'description' => $description,
                'locked'      => $locked,
            ] );
        } else {
            $wpdb->update( self::TABLE_ELEMENTS, [
                'name'        => $name,
                'description' => $description,
                'locked'      => $locked,
            ],             [
                               'id' => $element_id,
                           ] );
        }
        
        foreach( $variables as $key => $variable ) {
            switch( $key ) {
                case strpos( $key, 'variation_name_' ) !== false :
                    $variation_id = str_replace( 'variation_name_', '', $key );
                    $wpdb->update( self::TABLE_ELEMENT_VARIATIONS, [
                        'name' => $variable,
                    ],             [
                                       'id'      => $variation_id,
                                       'element' => $element_id,
                                   ] );
                    break;
                case strpos( $key, 'variation_file_' ) !== false :
                    $variation_id = str_replace( 'variation_file_', '', $key );
                    $wpdb->update( self::TABLE_ELEMENT_VARIATIONS, [
                        'file' => $variable,
                    ],             [
                                       'id'      => $variation_id,
                                       'element' => $element_id,
                                   ] );
                    break;
            }
        }
        
        return $element_id;
    }
    
    public static function saveMap( $variables = [] ) {
        if( empty( $variables ) || empty( $_GET['save'] ) ) return 0;
        global $wpdb;
        
        $map_id      = $variables['id'];
        $name        = $variables['map_name'] ?? '';
        $printing_id = $variables['map_printing'] ?? 0;
        $framecode   = $variables['map_framecode'] ?? 0;
        
        if( empty( $map_id ) ) {
            $wpdb->insert( self::TABLE_MASK_MAPS, [
                'name'      => $name,
                'printing'  => $printing_id,
                'framecode' => $framecode,
            ] );
            $map_id = $wpdb->insert_id;
        } else {
            $wpdb->update( self::TABLE_MASK_MAPS, [
                'name'      => $name,
                'printing'  => $printing_id,
                'framecode' => $framecode,
            ],             [
                               'id' => $map_id,
                           ] );
        }
        
        $previous_elements = self::getElementIdsByMaskMap( $map_id );
        $new_elements      = [];
        foreach( $variables as $key => $variable ) {
            if( strpos( $key, 'map_element_' ) === false || strpos( $key, 'map_element_variation_' ) !== false ) {
                continue;
            }
            $new_elements[] = $element_id = str_replace( 'map_element_', '', $key );
            if( !isset( $variables[ 'map_element_variation_'.$element_id ] ) ) continue;
            $element_variation_id = $variables[ 'map_element_variation_'.$element_id ];
            if( !in_array( $element_id, $previous_elements ) ) {
                $wpdb->insert( self::TABLE_MASK_ELEMENTS, [
                    'mask'      => $map_id,
                    'variation' => $element_variation_id,
                    'element'   => $element_id,
                ] );
            } else {
                $wpdb->update( self::TABLE_MASK_ELEMENTS, [
                    'variation' => $element_variation_id,
                ],             [
                                   'mask'    => $map_id,
                                   'element' => $element_id,
                               ] );
            }
        }
        foreach( $previous_elements as $previous_element ) {
            if( !in_array( $previous_element, $new_elements ) ) {
                $wpdb->delete( self::TABLE_MASK_ELEMENTS, [
                    'mask'    => $map_id,
                    'element' => $previous_element,
                ] );
            }
        }
        
        return $map_id;
    }
    
    /**
     * @param int $term
     *
     * @return array
     */
    public static function getElementIdsByMaskMap( $term = 0 ) {
        if( empty( $term ) ) return [];
        global $wpdb;
        $query   = 'SELECT * FROM as_cutter_mask_elements WHERE mask = '.$term;
        $results = $wpdb->get_results( $query );
        if( $results == null || empty( $results ) ) return [];
        foreach( $results as $key => $result ) {
            $results[ $key ] = $result->element;
        }
        
        return $results;
    }
    
    public static function templatePartVariation( $element_id = 0, $variation_id = 0 ) {
        if( empty( $element_id ) ) return;
        include DIR_THEME_TEMPLATE_PARTS.'/tools/cutter/management/components/card-variation.php';
    }
    
    /**
     * @param int $map_printing_id
     */
    public static function templatePartSelectedMapPrinting( $map_printing_id = 0 ) {
        include DIR_THEME_TEMPLATE_PARTS.'/tools/cutter/management/components/map-selected-printing.php';
    }
    
    /**
     * @param array $cards
     */
    public static function templatePartCardResults( $cards = [] ) {
        include DIR_THEME_TEMPLATE_PARTS.'/tools/cutter/management/components/card-results.php';
    }
    
    /**
     * @param array $printings
     */
    public static function templatePartPrintingResults( $printings = [] ) {
        include DIR_THEME_TEMPLATE_PARTS.'/tools/cutter/management/components/printing-results.php';
    }
    
    public static function upload() {
        $response = [
            'file_name' => '',
        ];
        
        if( !function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH.'wp-admin/includes/file.php' );
        }
        $uploadedfile     = $_FILES['file'];
        $upload_overrides = [ 'test_form' => false ];
        $dirUploads       = wp_upload_dir()['path'];
        $movefile         = wp_handle_upload( $uploadedfile, $upload_overrides );
        $basename         = str_replace( $dirUploads.'/', '', $movefile['file'] );
        
        $file_name = $response['file_name'] = sanitize_file_name( basename( $_FILES['file']['name'] ) );
        if( $movefile && !isset( $movefile['error'] ) ) {
            $response['message'] = "File Upload Successfully";
        } else {
            $response['message'] = $movefile['error'];
        }
        $file_path = $dirUploads.'/'.$basename;
        
        $message_uploads_path = ABSPATH.'/files/cutter/elements/';
        if( !is_dir( $message_uploads_path ) ) {
            mkdir( $message_uploads_path, 0755, true );
        }
        
        $stored_path = $message_uploads_path.'/'.$file_name;
        if( file_exists( $stored_path ) ) {
            for( $x = 1; $x <= 9999999; $x++ ) {
                $new_file_name = $x.'-'.$file_name;
                $stored_path   = $message_uploads_path.'/'.$new_file_name;
                if( file_exists( $stored_path ) ) continue;
                break;
            }
        }
        $response['path']        = $file_path;
        $response['stored_path'] = $stored_path;
        copy( $file_path, $stored_path );
        //unlink( $file_path );
        $url             = str_replace( get_home_url(), '', MC_Url::pathToUrl( $stored_path ) );
        $response['url'] = str_replace( '//', '/', $url );
        header( 'Content-Type: application/json' );
        echo json_encode( $response, JSON_UNESCAPED_SLASHES );
        die();
    }
    
}