<?php

namespace Mythic_Core\Ajax;

use MC_Giftcard_Functions;
use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class SelectCard
 *
 * @package Mythic_Core\Ajax\Store\Products\Design\Frame
 */
class MC_Switch_Gift_Card_Type extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response               = [
            'output'     => '',
            'product_id' => 0,
        ];
        $response['product_id'] = $product_id = $_REQUEST['product_id'] ?? 0;
        $response['is_digital'] = $is_digital = $_REQUEST['digital'] ?? 0;
        $response['digital']    = $digital = MC_Giftcard_Functions::digital( $product_id );
        $response['sleeves']    = $sleeves = MC_Giftcard_Functions::sleeves( $product_id );
        if( empty( $product_id ) ) $this->success( $response );
        
        ob_start();
        MC_Giftcard_Functions::fieldSleevesByType( [ 'digital' => $is_digital, 'sleeves' => $sleeves ] );
        $response['output'] = ob_get_clean();
        
        if( $is_digital != $digital ) {
            if( $is_digital == 1 && $sleeves < 5 ) $sleeves = 5;
            $args = [
                'post_type'      => 'product',
                'posts_per_page' => -1,
                'tax_query'      => [
                    'RELATION' => 'AND',
                    [
                        'taxonomy' => 'product_group',
                        'field'    => 'slug',
                        'terms'    => 'giftcard',
                    ],
                ],
                'fields'         => 'ids',
                'meta_query'     => [
                    'sleeves' => [
                        'key'   => 'mc_sleeves',
                        'value' => $sleeves,
                    ],
                    'digital' => [
                        'key'   => 'mc_digital',
                        'value' => 1,
                    ],
                ],
            ];
            if( empty( $digital ) ) {
                $args['meta_query']['digital']['compare'] = 'NOT EXISTS';
            }
            
            $giftcards = get_posts( $args );
            if( !empty( $giftcards ) ) {
                $product_id = $giftcards[0];
            }
            $response['digital'] = $digital = MC_Giftcard_Functions::digital( $product_id );
            $response['sleeves'] = $sleeves = MC_Giftcard_Functions::sleeves( $product_id );
        }
        
        $product       = wc_get_product( $product_id );
        $product_price = $product->get_price();
        if( !$is_digital ) $product_price = $product_price + 1;
        
        $response['price'] = $product_price;
        
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-giftcard-type';
    }
    
}