<?php

namespace Mythic_Core\Utils;

/**
 * Class MC_Woo
 *
 * @package Mythic_Core\Utils
 */
class MC_Woo {
    
    /**
     * @return bool
     */
    public static function active() {
        return MC_WOO_ACTIVE;
    }
    
    /**
     * @return string
     */
    public static function dashboardUrl() : string {
        return self::link( 'dashboard' );
    }
    
    /**
     * @param string $target
     *
     * @return string
     */
    public static function link( string $target = '' ) : string {
        if( empty( $target ) ) return '';
        $target = MC_Vars::parseableString( $target );
        switch( $target ) {
            case 'dashboard' :
            case 'myaccount' :
                $option_name = 'woocommerce_myaccount_page_id';
                break;
        }
        if( empty( $option_name ) ) return '';
        return get_permalink( get_option( $option_name, '' ) );
    }
    
    /**
     * @param $enqueue
     *
     * @return false|mixed
     */
    public static function enqueueStyle( $enqueue ) {
        if( MC_Url::isLoginPage() ) return false;
        
        return $enqueue;
    }
    
    /**
     * @param      $args
     * @param      $key
     * @param null $value
     *
     * @return array
     */
    public static function formClasses( $args, $key, $value = null ) : array {
        // Start field type switch case
        switch( $args['type'] ) {
            case "select" :  /* Targets all select input type elements, except the country and state select input types */
                $args['class'][]     = 'form-group';                   // Add a class to the field's html element wrapper - woocommerce input types (fields) are often wrapped within a <p></p> tag
                $args['input_class'] = [ 'form-control', 'input-lg' ]; // Add a class to the form input itself
                //$args['custom_attributes']['data-plugin'] = 'select2';
                $args['label_class']       = [ 'control-label' ];
                $args['custom_attributes'] = [ 'data-plugin' => 'select2', 'data-allow-clear' => 'true', 'aria-hidden' => 'true', ]; // Add custom data attributes to the form input itself
                break;
            
            case 'country' : /* By default WooCommerce will populate a select with the country names - $args defined for this specific input type targets only the country select element */
                $args['class'][]     = 'form-group single-country';
                $args['label_class'] = [ 'control-label' ];
                break;
            
            case "state" : /* By default WooCommerce will populate a select with state names - $args defined for this specific input type targets only the country select element */
                $args['class'][]     = 'form-group';                   // Add class to the field's html element wrapper
                $args['input_class'] = [ 'form-control', 'input-lg' ]; // add class to the form input itself
                //$args['custom_attributes']['data-plugin'] = 'select2';
                $args['label_class']       = [ 'control-label' ];
                $args['custom_attributes'] = [ 'data-plugin' => 'select2', 'data-allow-clear' => 'true', 'aria-hidden' => 'true', ];
                break;
            
            case "password" :
            case "text" :
            case "email" :
            case "tel" :
            case "number" :
                $args['class'][] = 'form-group';
                //$args['input_class'][] = 'form-control input-lg'; // will return an array of classes, the same as bellow
                $args['input_class'] = [ 'form-control', 'input-lg' ];
                $args['label_class'] = [ 'control-label' ];
                break;
            
            case 'textarea' :
                $args['input_class'] = [ 'form-control', 'input-lg' ];
                $args['label_class'] = [ 'control-label' ];
                break;
            
            case 'checkbox' :
                break;
            
            case 'radio' :
                break;
            
            default :
                $args['class'][]     = 'form-group';
                $args['input_class'] = [ 'form-control', 'input-lg' ];
                $args['label_class'] = [ 'control-label' ];
                break;
        }
        
        return $args;
    }
    
}