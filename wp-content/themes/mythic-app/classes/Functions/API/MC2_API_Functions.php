<?php

namespace Mythic\Functions\API;

use Mythic\Abstracts\MC2_Class;

class MC2_API_Functions extends MC2_Class {

	const CACHE_PATH = ABSPATH.'files/api-cache/';

	/**
	 * @param string $file_name
	 *
	 * @return array
	 */
	public static function get_cached_json_data( string $file_name ) : array {
		$default = [];
		self::cache_dir();
		$file = self::CACHE_PATH.$file_name.'.json';
		if( !file_exists( $file ) ) return $default;
		if (time() - filemtime($file) >= 60 * 60 * 24 ) { // 1 day
			unlink($file);
			return [];
		}
		$contents = json_decode( file_get_contents( $file ) );
		return is_iterable($contents) ? $contents : [];
	}

	/**
	 * @param string $file_name
	 * @param mixed  $data
	 *
	 * @return bool
	 */
	public static function update_cached_json_file( string $file_name, $data ) : bool {
		self::cache_dir();
		$json_data = !is_string( $data ) ? json_encode( $data ) : $data;
		$file_path      = self::CACHE_PATH.$file_name.'.json';
		$file      = fopen( $file_path, "w" );
		fwrite( $file, $json_data );
		fclose( $file );

		return file_exists( $file_path );
	}

	public static function cache_dir() {
		if( !is_dir( self::CACHE_PATH ) ) {
			mkdir( self::CACHE_PATH, 0755, true );
		}
	}

}