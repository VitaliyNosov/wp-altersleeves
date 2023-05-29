<?php

namespace Mythic\Abstracts;

/**
 * This is the master loading class! This class requires all the files and initialises the application.
 */
abstract class MC2_Init {

    public function __construct() {
        $this->vendorFiles();
        $this->autoload_classes();
    }

    protected function vendorFiles() {
        $vendor_dir      = '/vendor';
        $vendor_autoload = '/vendor/autoload.php';
        $themes          = [ get_template_directory(), get_stylesheet_directory() ];
        $message         = 'Vendor files not found. Please contact '.get_bloginfo( 'admin_email' );
        foreach( $themes as $theme ) {
            if( !file_exists( $theme.$vendor_dir ) ) continue;
            if( !file_exists( $autoload = $theme.$vendor_autoload ) ) die( $message );
            require_once $autoload;
        }
    }

    /**
     * @return string
     */
    protected function getCurrentNamespaceMain() : string {
        $namespace = explode( '\\', __NAMESPACE__ );

        return $namespace[0];
    }

    /**
     * Gets all the class files then instantiates all the functions files to call their actions
     */
    protected function autoload_classes() {
        spl_autoload_register([$this, 'autoloader']);
    }
    
    
    protected function autoloader($class) {
        $dir = __DIR__;
        if( strpos($dir, STYLESHEET_DIR) !== false && STYLESHEET_DIR == TEMPLATE_DIR ) return;
        
        
        include 'lib/' . $class . '.php';
    }
    
    protected function init_classes() {
        $template = get_template_directory();
        $themes   = [ $template ];
        if( $template != $stylesheet = get_stylesheet_directory() ) $themes[] = $stylesheet;
        $sub_dirs = [ '/*', '/*/*', '/*/*/*', '/*/*/*/*' ];
        $included = [];
    
    
        foreach( $themes as $theme ) {
            $class_dir     = $theme.'/classes/';
            $functions_dir = $class_dir.'Functions/';
            foreach( $sub_dirs as $sub_dir ) {
                foreach( glob( $functions_dir.$sub_dir.'.php' ) as $filename ) {
                    if( strpos($filename, '_Transaction_') !== false ) continue;
                    $namespace = $this->getCurrentNamespaceMain();
                    $class     = $namespace.'\\'.str_replace( $class_dir, '', $filename );
                    $class     = str_replace( '//', '/', $class );
                    $class = str_replace( '/', '\\', $class );
                    $class     = str_replace( '.php', '', $class );
                    if( !class_exists( $class ) ) continue;
                    new $class();
                }
            }
        }
    }

}