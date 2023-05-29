<?php

namespace Mythic\Helpers;

use Mythic\Functions\MC2_FunctionsFunctions;

/**
 * Class MC2_Url
 *
 * @package Mythic\Helpers
 */
class MC2_Url {

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function is( $url = '' ) : bool {
        if( empty( $url ) ) return false;
        if( MC2_Vars::stringContains($url, 'http') ) return true;
        $regex = "((https?|ftp)\:\/\/)?";                                      // SCHEME
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
        $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})";                               // Host or IP
        $regex .= "(\:[0-9]{2,5})?";                                           // Port
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";                               // Path
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?";                  // GET Query
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?";                               // Anchor
        if( preg_match( "/^$regex$/i", $url ) ) return true;

        return false;
    }

    /**
     * @return bool
     */
    public static function isCartPage() : bool {
        return in_array( MC2_Server::primaryPath(), [ 'cart' ] );
    }

    /**
     * @return bool
     */
    public static function isCheckoutPage() : bool {
        return in_array( MC2_Server::primaryPath(), [ 'checkout' ] );
    }

    /**
     * @return bool
     */
    public static function isLoginPage() : bool {
        return in_array( MC2_Server::primaryPath(), [ 'wp-login.php', 'wp-register.php', 'login' ] );
    }

    /**
     * @return bool
     */
    public static function isDashboard() : bool {
        return in_array( MC2_Server::primaryPath(), [ 'dashboard', 'my-account' ] );
    }

    /**
     * @return bool
     */
    public static function isProductPage() : bool {
        if( !WOO_ACTIVE ) return false;
        return is_product();
    }

    /**
     * @return bool
     */
    public static function isRegistrationPage() : bool {
        return in_array( MC2_Server::primaryPath(), [ 'registration' ] );
    }


    public static function loginUrl() : string {
        return SITE_URL.'/login';
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function matches( $string = '' ) : bool {
        $primary_path = MC2_Server::primaryPath();
        return $string == $primary_path;
    }

    /**
     * @return bool
     */
    public static function isBrowse() : bool {
        $primary_path = MC2_Server::requestUri( false );
        return MC2_Vars::stringContains( $primary_path, 'browse' );
    }

    /**
     * @return bool
     */
    public static function isCampaign() : bool {
        $primary_path = MC2_Server::primaryPath();
        return MC2_Vars::stringContains( $primary_path, 'campaign' );
    }

    /**
     * @param string $string
     * @param string $url
     *
     * @return bool
     */
    public static function contains( $string = '', $url = '' ) : bool {
        if( empty( $string ) ) return false;
        if( empty( $url ) ) $url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

        return MC2_Vars::stringContains( $url, $string );
    }


	/**
	 * $site_url can be used for test it on local server for live values
	 * @param string $string
	 * @param string $site_url
	 * @return string
	 */
	public static function urlToPath($string = '', $site_url = '' ) : string {
        if( empty( $string ) ) return '';
		$site_url = !empty($site_url) ? $site_url : SITE_URL;
        $string = str_replace( $site_url, ABSPATH, $string );
        $string = str_replace( '///', '/', $string );
		$string = str_replace( '//', '/', $string );

        return trim( $string );
    }

    /**
     * @param string $path
     * @param bool   $no_domain
     *
     * @return string|string[]
     */
    public static function pathToUrl( $path = '', $no_domain = false ) : string {
        $replace = $no_domain ? '' : get_home_url();

        return str_replace( ABSPATH, $replace.'/', $path );
    }

    /**
     * @param bool $clean
     *
     * @return string
     */
    public static function current( $clean = true ) : string {
        return ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" )."://$_SERVER[HTTP_HOST]".'/'.MC2_Server::requestUri( $clean );
    }

    /**
     * @param string $unedited
     *
     * @return array
     */
    public static function getUrlAndServerLocations( string $unedited = '' ) : array {
        $path = $unedited;
        $path = str_replace(SITE_URL, '', $path );
        $path = str_replace(ABSPATH, '', $path );
        return [
            'path' => $path,
            'url' => MC2_Url::cleanPath(SITE_URL.$path),
            'dir' => MC2_Url::cleanPath(ABSPATH.$path),
            'unedited' => $unedited
        ];
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function cleanPath( string $path = '' ) : string {
        foreach( [ 'https', 'http', 'HTTPS', 'HTTP' ] as $protocol ) {
            if( strpos($path, $protocol) === false ) continue;
            $path = str_replace($protocol.'://', '' , $path );
            $url = true;
        }
        $path = str_replace('//', '/', $path );
        if( empty($url) ) return $path;
        return 'https://'.$path;
    }

    public static function getSubdomain() {
        $host = explode('.', $_SERVER['HTTP_HOST']);
        return $host[0];
    }

}