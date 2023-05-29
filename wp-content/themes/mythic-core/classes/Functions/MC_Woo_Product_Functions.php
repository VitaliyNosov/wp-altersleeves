<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Abstracts\MC_Post_Type_Functions;

/**
 * Class MC_Woo_Product_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Woo_Product_Functions extends MC_Post_Type_Functions {
    
    /**
     * MC_Woo_Product_Functions constructor.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'addWPAuthorSupport' ], 999 );
    }
    
    /**
     * @param null $product
     */
    public static function redirect( $product = null ) {
        if( empty( $product ) ) return;
        $product_id = is_object( $product ) ? $product->id ?? 0 : $product;
        if( empty( $product_id ) ) return;
        $url = get_the_permalink( $product_id );
        if( empty( $url ) ) return;
        wp_redirect( $url );
        die();
    }
    
    /**
     * @return array
     */
    public static function availabilityOptions() : array {
        return [
            'available' => [
                'label'    => __( 'Available in store', MC_TEXT_DOMAIN ),
                'value'    => 'available',
                'selected' => true,
            ],
            'internal'  => [
                'label'    => __( 'For internal use only', MC_TEXT_DOMAIN ),
                'value'    => 'internal',
                'selected' => false,
            ],
            
        ];
    }
    
    /**
     * @return int
     */
    public static function setVariationThreshold() : int {
        return apply_filters( 'mc_variation_threshold', 100 );
    }
    
    public static function addWPAuthorSupport() {
        add_post_type_support( 'product', 'author' );
    }
    
}