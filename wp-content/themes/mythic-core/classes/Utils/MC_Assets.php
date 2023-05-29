<?php

namespace Mythic_Core\Utils;

/**
 * Class MC_Assets
 *
 * @package Mythic_Core\Utils
 */
class MC_Assets {
    
    const CSS_FOLDER   = '/src/css/';
    const DOCS_FOLDER  = '/src/docs/';
    const FONTS_FOLDER = '/src/fonts/';
    const JS_FOLDER    = '/src/js/';
    const IMG_FOLDER   = '/src/img/';
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getCssComponentsUrl( $file_name = '', $parent_only = false ) : string {
        return self::getCssUrl( '/components/'.$file_name, $parent_only );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getCssLayoutsUrl( $file_name = '', $parent_only = false ) : string {
        return self::getCssUrl( '/layouts/'.$file_name, $parent_only );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getCssModulesUrl( $file_name = '', $parent_only = false ) : string {
        return self::getCssUrl( '/modules/'.$file_name, $parent_only );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getCssSectionsUrl( $file_name = '', $parent_only = false ) : string {
        return self::getCssUrl( '/sections/'.$file_name, $parent_only );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getCssVendorUrl( $file_name = '', $parent_only = false ) : string {
        return self::getCssUrl( '/vendor/'.$file_name, $parent_only );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getCssUrl( $file_name = '', $parent_only = false ) : string {
        $file = self::getAssetPath( $file_name, 'css', $parent_only );
        
        return MC_Url::pathToUrl( $file );
    }
    
    /**
     * @param string $file_name
     * @param string $ext
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getAssetPath( $file_name = '', $ext = '', $parent_only = false ) : string {
        if( empty( $file_name ) ) return '';
        $dirs = [
            get_stylesheet_directory(),
            get_template_directory(),
        ];
        if( $parent_only ) unset( $dirs[0] );
        $ext = !empty( $ext ) ? $ext : MC_Files::extension( $ext );
        if( empty( $ext ) ) return '';
        $minfiable = false;
        switch( $ext ) {
            case 'css' :
                $minfiable = true;
                $folder    = self::CSS_FOLDER;
                break;
            case 'pdf' :
                $folder = self::DOCS_FOLDER;
                break;
            case 'ttf' :
            case 'otf' :
            case 'woff' :
                $folder = self::FONTS_FOLDER;
                break;
            case 'js' :
                $minfiable = true;
                $folder    = self::JS_FOLDER;
                break;
            case 'jpg' :
            case 'jpeg' :
            case 'png' :
            case 'bmp' :
            case 'svg' :
                $folder = self::IMG_FOLDER;
                break;
            default :
                return '';
        }
        $ext = '.'.$ext;
        
        $renders = [
            '',
        ];
        
        if( $minfiable ) {
            $renders[] = '.all.min';
            $renders[] = '.min';
        }
        $renders = array_reverse( $renders );
        
        foreach( $dirs as $dir ) {
            foreach( $renders as $render ) {
                $file = $dir.$folder.$file_name.$render.$ext;
                if( file_exists( $file ) ) return $file;
            }
        }
        
        return '';
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getImgUrl( $file_name = '', $parent_only = false ) : string {
        $ext       = MC_Files::extension( $file_name );
        $file_name = str_replace( '.'.$ext, '', $file_name );
        $file      = self::getAssetPath( $file_name, $ext, $parent_only );
        
        return MC_Url::pathToUrl( $file, true );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getRetinaImgUrl( $file_name = '', $parent_only = false ) : string {
        $img = self::getImgUrl( $file_name, $parent_only );
        if( MC_Vars::stringContains( $file_name, '@2x' ) ) return $img;
        $ext = MC_Files::extension( $file_name, true );
        
        return str_replace( $ext, '@2x'.$ext, $img );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getImgPath( $file_name = '', $parent_only = false ) : string {
        return self::getAssetPath( $file_name, 'img', $parent_only );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getJsUrl( $file_name = '', $parent_only = false ) : string {
        $file = self::getAssetPath( $file_name, 'js', $parent_only );
        
        return MC_Url::pathToUrl( $file );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getJsComponentsUrl( $file_name = '', $parent_only = false ) : string {
        return self::getJsUrl( '/components/'.$file_name, $parent_only );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getJsFunctionsUrl( $file_name = '', $parent_only = false ) : string {
        return self::getJsUrl( '/functions/'.$file_name, $parent_only );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getJsModulesUrl( $file_name = '', $parent_only = false ) : string {
        return self::getJsUrl( '/modules/'.$file_name, $parent_only );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getJsSectionsUrl( $file_name = '', $parent_only = false ) : string {
        return self::getJsUrl( '/sections/'.$file_name, $parent_only );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getJsVendorUrl( $file_name = '', $parent_only = false ) : string {
        return self::getJsUrl( '/vendor/'.$file_name, $parent_only );
    }
    
    /**
     * @param string $file_name
     * @param bool   $parent_only
     *
     * @return string
     */
    public static function getJsAdminUrl( $file_name = '', $parent_only = false ) : string {
        return self::getJsUrl( '/admin/'.$file_name, $parent_only );
    }
    
}