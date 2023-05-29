<?php

namespace Mythic\Helpers;

/**
 * Class MC2_Files
 *
 * @package Mythic\Helpers
 */
class MC2_Files {

    /**
     * @param string $file_name
     * @param false  $dot
     *
     * @return string|string[]
     */
    public static function extension( $file_name = '', $dot = false ) {
        $ext = pathinfo( $file_name, PATHINFO_EXTENSION );
        if( $dot && !MC2_Vars::stringContains( $ext, '.' ) ) $ext = '.'.$ext;
        return $ext;
    }

    /**
     * @param     $bytes
     * @param int $decimals
     *
     * @return string
     */
    public static function readableSize( $bytes, $decimals = 2 ) : string {
        $size   = [ 'B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' ];
        $factor = floor( ( strlen( $bytes ) - 1 ) / 3 );

        return sprintf( "%.{$decimals}f", $bytes / pow( 1024, $factor ) ).@$size[ $factor ];
    }

    /**
     * @param string $url
     * @param bool   $readable
     *
     * @return int|string
     */
    public static function sizeFromUrl( $url = '', $readable = true ) {
        $result = -1;
        if( empty( $url ) ) return $result;
        $curl = curl_init( $url );
        curl_setopt( $curl, CURLOPT_NOBODY, true );
        curl_setopt( $curl, CURLOPT_HEADER, true );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
        $data = curl_exec( $curl );
        curl_close( $curl );
        if( $data ) {
            $data           = strtolower( $data );
            $content_length = "unknown";
            $status         = "unknown";
            if( preg_match( "/^http\/2 (\d\d\d)/", $data, $matches ) ) {
                $status = (int) $matches[1];
            } else {
                if( preg_match( "/^http\/1 (\d\d\d)/", $data, $matches ) ) {
                    $status = (int) $matches[1];
                } else {
                    if( preg_match( "/^http\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
                        $status = (int) $matches[1];
                    } else {
                        if( preg_match( "/^http\/2\.[01] (\d\d\d)/", $data, $matches ) ) {
                            $status = (int) $matches[1];
                        }
                    }
                }
            }

            if( preg_match( "/content-length: (\d+)/", $data, $matches ) ) {
                $content_length = (int) $matches[1];
            }
            if( $status == 200 || ( $status > 300 && $status <= 308 ) ) {
                $result = $content_length;
            }
        }

        return $result;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public static function csvToArray( $path = '' ) : array {
        if( empty( $path ) ) return [];
        if( !file_exists( $path ) ) return [];
        $array  = $fields = [];
        $i      = 0;
        $handle = @fopen( $path, "r" );
        if( $handle ) {
            while( ( $row = fgetcsv( $handle, 4096 ) ) !== false ) {
                if( empty( $fields ) ) {
                    $fields = $row;
                    continue;
                }
                foreach( $row as $k => $value ) {
                    $kNice                 = str_replace( ' ', '_', strtolower( $fields[ $k ] ) );
                    $array[ $i ][ $kNice ] = $value;
                    unset( $array[ $i ][ $fields[ $k ] ] );
                }
                $i++;
            }
            if( !feof( $handle ) ) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose( $handle );
        }

        return $array;
    }

    /**
     * @param string $dir
     *
     * @return array|false
     */
    public static function scanDir( $dir = '' ) {
        $files = scandir($dir);
        unset($files[0]);
        unset($files[0]);
        return $files;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function file_exists($path = '' ) {
        if( strpos(strtolower($path), 'http') !== false ) {
           return self::url_file_exists($path);
        } else {
            return file_exists($path);
        }
    }

    /**
     * @param $url
     *
     * @return bool
     */
    public static function url_file_exists( $url ) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

}