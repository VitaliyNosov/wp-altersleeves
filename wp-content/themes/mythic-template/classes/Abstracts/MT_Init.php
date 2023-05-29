<?php

namespace Mythic_Template\Abstracts;

/**
 * Class MT_Init
 *
 * @package Mythic_Template\Abstracts
 */
abstract class MT_Init {

    public $dir = '';
    public $dirClass = '';
    public $dirSystem = '';
    public $currentPrefix = '';
    public $currentNamespaceMain = '';
    public $curentAliasesClassName = '';
    public $curentConstantsClassName = '';
    public $curentGlobalsClassName = '';
    public $currentNamespaceExtended = '';

    public function __construct() {
        $this->setInitVariables();
        $this->initConstants();
        $this->initGlobals();
        $this->requireClassesFiles();
        $this->initClasses();
        $this->initAliases();
        is_admin() ? $this->adminLoading() : $this->publicLoading();
        add_action( 'init', [ $this, 'initAjax' ] );
        add_action( 'init', [ $this, 'initShortcodes' ] );
    }

    public function setInitVariables() {
        $this->dir                      = $this->getDir();
        $this->dirClass                 = $this->getClassDir();
        $this->dirSystem                = $this->getSystemDir();
        $this->currentPrefix            = $this->getCurrentPrefix();
        $this->currentNamespaceMain     = $this->getCurrentNamespaceMain();
        $this->currentNamespaceExtended = $this->getCurrentNamespaceExtended();
        $this->curentAliasesClassName   = $this->getAliasesClassName();
        $this->curentConstantsClassName = $this->getConstantsClassName();
        $this->curentGlobalsClassName   = $this->getGlobalsClassName();
    }

    public function initClasses() {
    }

    public function initAliases() {
        $file = $this->dirSystem.$this->getAliasesClassName().'.php';
        if( !file_exists( $file ) ) return;
        require_once( $file );
        $class = $this->getAliasesClass();
        if( class_exists( $class ) ) new $class();
    }

    public function initConstants() {
        $file = $this->dirSystem.$this->getConstantsClassName().'.php';
        if( !file_exists( $file ) ) return;
        require_once( $file );
        $class = $this->getConstantsClass();
        if( class_exists( $class ) ) new $class();
    }

    public function initGlobals() {
        $file = $this->dirSystem.$this->getGlobalsClassName().'.php';
        if( !file_exists( $file ) ) return;
        require_once( $file );
        $class = $this->getGlobalsClass();
        if( class_exists( $class ) ) new $class();
    }

    /**
     * @return string
     */
    public function getClassDir() : string {
        return $this->dir.'/classes/';
    }

    /**
     * @return string
     */
    public function getFunctionsDir() : string {
        return $this->dir.'/functions/';
    }

    /**
     * @return string
     */
    public function getSystemDir() : string {
        return $this->dirClass.'System/';
    }

    /**
     * @return string
     */
    public function getDir() : string {
        $namespace  = $this->getCurrentNamespaceMain();
        $stylesheet = get_stylesheet_directory();
        $template   = get_template_directory();
        if( $stylesheet == $template ) return $template;

        return strpos( __NAMESPACE__, $namespace ) !== false ? $template : $stylesheet;
    }

    /**
     * @return string
     */
    public function getCurrentPrefix() : string {
        return '';
    }

    /**
     * @return string
     */
    public function getCurrentNamespaceMain() : string {
        $namespace = explode( '\\', __NAMESPACE__ );

        return $namespace[0];
    }

    /**
     * @return string
     */
    public function getCurrentNamespaceExtended() : string {
        return $this->currentNamespaceMain.'\\Loader\\';
    }

    /**
     * @return string
     */
    public function getAliasesClassName() : string {
        return $this->currentPrefix.'Aliases';
    }

    /**
     * @return string
     */
    public function getConstantsClassName() : string {
        return $this->currentPrefix.'Constants';
    }

    /**
     * @return string
     */
    public function getGlobalsClassName() : string {
        return $this->currentPrefix.'Globals';
    }

    /**
     * @return string
     */
    public function getConstantsClass() : string {
        if( empty( $this->curentConstantsClassName ) ) return '';

        return $this->currentNamespaceMain.'\\System\\'.$this->curentConstantsClassName;
    }

    /**
     * @return string
     */
    public function getGlobalsClass() : string {
        if( empty( $this->curentGlobalsClassName ) ) return '';

        return $this->currentNamespaceMain.'\\System\\'.$this->curentGlobalsClassName;
    }

    /**
     * @return string
     */
    public function getAliasesClass() : string {
        if( empty( $this->curentAliasesClassName ) ) return '';

        return $this->currentNamespaceMain.'\\System\\'.$this->curentAliasesClassName;
    }

    public function requireClassesFiles() {
        $included = $this->getIncluded();
        $dirs     = [ $this->getClassDir(), $this->getFunctionsDir() ];

        foreach( $dirs as $dir ) {
            if( !file_exists( $dir ) ) continue;
            $sub_dirs = [ '/*', '/*/*', '/*/*/*', '/*/*/*/*' ];
            foreach( $sub_dirs as $sub_dir ) {
                foreach( glob( $dir.$sub_dir.'.php' ) as $filename ) {
                    if( in_array( $filename, $included ) ) {
                        continue;
                    }
                    require_once( $filename );
                    $included[] = $filename;
                }
            }
        }
    }

    /**
     * @return string[]
     */
    public function getIncluded() : array {
        return [ $this->curentConstantsClassName ];
    }

    public function adminLoading() {
        $this->initPublicLoaderClass();
        $this->initAdminLoaderClass();
    }

    public function publicLoading() {
        $this->initPublicLoaderClass();
    }

    public function initPublicLoaderClass() {
        $loader_name = $this->currentNamespaceExtended.$this->currentPrefix.'Public_Loader';
        if( !class_exists( $loader_name ) ) return;
        new $loader_name();
    }

    public function initAdminLoaderClass() {
        $loader_name = $this->currentNamespaceExtended.$this->currentPrefix.'Admin_Loader';
        if( !class_exists( $loader_name ) ) return;
        new $loader_name();
    }

    public function initAjax() {
        $this->initInitiable( 'Ajax' );
    }

    public function initShortcodes() {
        $this->initInitiable( 'Shortcodes' );
    }

    public function initInitiable( $class_parent = '' ) {
        $dir = $this->dir.'/classes/'.$class_parent;
        if( !file_exists( $dir ) ) return;
        $sub_dirs = [ '/*', '/*/*', '/*/*/*' ];
        $included = [];
        foreach( $sub_dirs as $sub_dir ) {
            foreach( glob( $dir.$sub_dir.'.php' ) as $filename ) {
                if( in_array( $filename, $included ) ) continue;
                $class = str_replace( $dir, '/'.$class_parent, $filename );
                $class = $this->getCurrentNamespaceMain().str_replace( '/', '\\', $class );
                $class = str_replace( '.php', '', $class );
                if( !class_exists( $class ) ) continue;
                new $class();
                $included[] = $filename;
            }
        }
    }

    public static function initClass( $class = '' ) {
        if( empty( $class ) || !class_exists( $class ) ) return;
        new $class();
    }

}
