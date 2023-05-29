<?php

namespace Mythic_Core\Functions;

/**
 * Class MC_Product_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Product_Functions {
    
    /**
     * @param int $product_id
     *
     * @return false|string
     */
    public static function type( $product_id = 0 ) {
        switch( $product_id ) {
            case self::isAlter( $product_id ) :
                return 'design';
            case self::isCollection( $product_id ) :
                return 'collection';
            case self::isGiftCard( $product_id ) :
                return 'giftcard';
            default :
                return '';
        }
    }
    
    /**
     * @param int $product_id
     *
     * @return bool
     */
    public static function isAlter( $product_id = 0 ) {
        return has_term( 'alter', 'product_group', $product_id );
    }
    
    /**
     * @param int $product_id
     *
     * @return bool
     */
    public static function isCollection( $product_id = 0 ) {
        $byGroup = has_term( 'collection', 'product_group', $product_id );
        if( $byGroup ) return true;
        $product = wc_get_product( $product_id );
        if( !method_exists( $product, 'get_type' ) ) return false;
        if( $product->get_type() == 'composite' ) return true;
        
        return false;
    }
    
    /**
     * @param int $product_id
     *
     * @return bool
     */
    public static function isGiftCard( $product_id = 0 ) {
        return has_term( 'giftcard', 'product_group', $product_id );
    }
    
    /**
     * @return int
     */
    public static function id() {
        $object = get_queried_object();
        if( !isset( $object->ID ) ) {
            $product_id = get_the_ID();
        } else {
            return $object->ID;
        }
        if( $product_id ) return $product_id;
        
        return 0;
    }
    
    /**
     * @return int[]
     */
    public static function counterShieldIds() {
        return [ 156811, 158544, 158991, 159064, 159065, 159066, 159067, 159068 ];
    }
    
    /**
     * @return string
     */
    public static function snapboltIdsSql() {
        $ids = self::snapboltIds();
        
        return '('.implode( ',', $ids ).')';
    }
    
    /**
     * @return array
     */
    public static function snapboltIds() {
        return [
            // Raanef - Lightning Bolt
            33810,
            33805,
            33804,
            33803,
            // Raaanef - Snapcaster Mage
            33811,
            // Luun - Opt
            33643,
            33594,
            // Luun - Serum Visions
            33598,
            33597,
            33697,
            33596,
            33595,
            // Neferentium - Rhystic
            86739,
            33654,
            // Neferentium - Mystic
            33653,
            // Nordic Alters
            33926,
            33927,
            // Riel - Fireball
            87790,
            87789,
            87786,
            87785,
            87784,
            33644,
            // Riel - Channel
            87794,
            87793,
            87792,
            87791,
            33645,
            // DESIGNS
            153025,
            153028,
            153931,
            153926,
            // OVERLAY PREVIEWS
            // Crystalline Giant
            156811,
            156811, // GIANT
            158544 // KATHRIL
        ];
    }
    
    public static function snapboltRedirect() {
        if( !MC_User_Functions::isAdmin() ) return;
        global $wp_query;
        $product_id  = $wp_query->get_queried_object_id();
        $snapboltIds = self::snapboltIds();
        if( !in_array( $product_id, $snapboltIds ) ) return;
        wp_redirect( MC_SITE );
        exit;
    }
    
    public static function snapboltCollectionIds() {
        return [ 87399, 87452, 87537, 87629, 87687 ];
    }
    
    /**
     * @return int[]
     */
    public static function not_for_sale() {
        return [ 167955, 167954, 167960, 167961 ];
    }
    
    /**
     * @param $link_text
     * @param $product_data
     *
     * @return string
     */
    public static function cartName( $link_text, $product_data ) : string {
        $product_id = $product_data['product_id'];
        if( self::isAlter( $product_id ) ) {
            $title = MC_Alter_Functions::getCartName( $product_id );
        } else {
            $title = get_the_title( $product_id );
        }
        
        return sprintf( '<a href="%s">%s </a>', get_the_permalink( $product_id ), $title );
    }
    
    /**
     * @param string $image
     * @param        $product_data
     *
     * @return string
     */
    public static function cartImage( string $image, $product_data ) : string {
        $product_id = $product_data['product_id'];
        if( !self::isAlter( $product_id ) ) return $image;
        
        return '<img class="card-display" src="'.MC_Alter_Functions::getCartImage( $product_id ).'">';
    }


    /**
     * @param int $product_id
     *
     * @return string
     */
    public static function getPrices( int $product_id ): string {
        $product = wc_get_product( $product_id );    
        if (!$product) {
            return 'Product not found';
        }
        $product_price = $product->get_price();
        $product_regular_price = $product->get_regular_price();

        $result = '<div class="col">';
        if( $product_regular_price-$product_price!=0 ) {
            $result.= '<span class="discounted">$'.$product_regular_price.'</span> $'.$product_price;
        } else {
            $result.='$'.$product_price;
        }
        $result.='</div>';

        return $result;
    } 


    /**
     * One time function /wp-admin/?set_global_sale_on=1
     * 
     * @param $sale_category
     * 
     * @return bool
     */
    public static function set_products_sale_on_as_default( $sale_category ) : bool {

        $products = get_posts( array(
            'numberposts' => -1,
            'post_type'   => 'product',
        ) );

        if( !count( $products ) ){
            Echo '<div>You have no products to update</div>';
            return false;
        }

        foreach( $products as $product ){
           wp_set_object_terms($product->ID, $sale_category->term_id, 'product_cat');
        }

        Echo '<div>Products updated.</div>';
        return true;
    }
    
}