<?php

namespace Mythic_Core\Ajax\Store\Cart;

use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class AddCollection
 *
 * @package Mythic_Core\Ajax\Store
 */
class MC_Add_Collection extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'cas-add-collection';
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $idProduct = $_POST['alter_id'];
        $designs   = stripslashes( $_POST['choices'] );
        $designs   = json_decode( $designs, ARRAY_A );
        $args      = [];
        $alters    = get_post_meta( $idProduct, '_bto_data' );
        if( is_array( $alters[0] ) ) $alters = $alters[0];
        
        $count = 0;
        foreach( $alters as $key => $alter ) {
            $selection    = [
                'product_id' => $designs[ $count ]['alter_id'],
                'quantity'   => 1,
            ];
            $args[ $key ] = $selection;
            $count++;
        }
        $cartKey = WC_CP()->cart->add_composite_to_cart( $idProduct, 1, $args );
        
        $this->success( [
                            'alters'        => $alters,
                            'args'          => $args,
                            'designs'       => $designs,
                            'id'            => $idProduct,
                            'cart_item_key' => $cartKey,
                            'count'         => WC()->cart->get_cart_contents_count(),
                        ] );
    }
    
}