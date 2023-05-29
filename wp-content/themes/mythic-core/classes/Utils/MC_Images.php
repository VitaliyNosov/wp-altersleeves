<?php

namespace Mythic_Core\Utils;

use CURLFile;
use Exception;

/**
 * Class MC_Images
 *
 * @package Mythic_Core\Utils
 */
class MC_Images {
    
    /**
     * @param $data
     * @param $path
     *
     * @return string
     * @throws Exception
     */
    public static function base64ToImage( $data, $path ) : string {
        if( empty( $data ) ) return false;
        if( strpos( $data, 'data:image' ) === false ) {
            $type = pathinfo( $path, PATHINFO_EXTENSION );
            $data = 'data:image/'.$type.';base64,'.$data;
        }
    
        [ $type, $data ] = explode( ';', $data );
        [ $type, $data ] = explode( ',', $data );
    
        $data = base64_decode( $data );
        $write = file_put_contents( $path, $data );
        if( empty( $write ) ) return '';
    
        return $path;
    }
    
    /**
     * @param $path
     *
     * @return string
     */
    public static function imagePathToBase64( $path, $prefix = true ) {
        if( !file_exists( $path ) ) return '';
        $type   = pathinfo( $path, PATHINFO_EXTENSION );
        $data   = file_get_contents( $path );
        $base64 = base64_encode( $data );
        if( !$prefix ) return $base64;
        return 'data:image/'.$type.';base64,'.base64_encode( $data );
    }
    
    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isImgPath( $string = '' ) : bool {
        $file_types = self::fileTypes();
        $string     = MC_Vars::parseableString( $string );
        foreach( $file_types as $key => $file_type ) {
            $file_type = '.'.$file_type;
            if( MC_Vars::stringContains( $string, $file_type ) ) return true;
        }
        
        return false;
    }
    
    /**
     * @return string[]
     */
    public static function fileTypes() : array {
        return [
            'jpg',
            'jpeg',
            'png',
            'tiff',
            'pdf',
            'svg',
        ];
    }
    
    /**
     * @param string $originalImage
     * @param int    $quality
     *
     * @return mixed|string|string[]|void
     */
    public static function compress( $originalImage = '', $quality = 60 ) {
        if( empty( $originalImage ) ) return;
        if( strpos( get_site_url(), $originalImage ) !== false ) $originalImage = MC_Url::urlToPath( $originalImage );
        $mime   = mime_content_type( $originalImage );
        $info   = pathinfo( $originalImage );
        $name   = $info['basename'];
        $output = new CURLFile( $originalImage, $mime, $name );
        $data   = [ "files" => $output ];
        $ch     = curl_init();
        curl_setopt( $ch, CURLOPT_URL, 'http://api.resmush.it/?qlty='.$quality );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
        $result = curl_exec( $ch );
        if( curl_errno( $ch ) ) $result = curl_error( $ch );
        curl_close( $ch );
        $result = json_decode( $result, ARRAY_A );
        if( !isset( $result['dest'] ) ) return $result;
        $result          = str_replace( 'https', 'http', $result );
        $compressedImage = file_get_contents( $result['dest'] );
        file_put_contents( $originalImage, $compressedImage );
        
        return $result;
    }
    
}