<?php

namespace Mythic_Core\Ajax\Acceptance;

use MC_Alter_Functions;
use MC_Production_Functions;
use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class MC_Send_Approval_Files
 *
 * @package Mythic_Core\Ajax\Acceptance\Alter
 */
class MC_Send_Approval_Files extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $count = $_POST['count'] ?? 100;
        
        $args   = [
            'post_type'      => 'product',
            'posts_per_page' => $count,
            'post_status'    => [ 'verify', 'internal_verify' ],
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'tax_query'      => [
                'RELATION' => 'AND',
                [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'simple',
                ],
                [
                    'taxonomy' => 'product_group',
                    'field'    => 'slug',
                    'terms'    => 'alter',
                ],
            ],
            'fields'         => 'ids',
        ];
        $alters = get_posts( $args );
        
        $path   = ABSPATH.'files/orders-'.MC_Production_Functions::user();
        $file   = "$path/approvals.txt";
        $file   = fopen( $file, "w" );
        $output = "approval\n";
        fwrite( $file, $output );
        $output = '';
        foreach( $alters as $alter_id ) {
            $image = MC_Alter_Functions::image( $alter_id );
            if( empty( $image ) ) continue;
            $output .= "$alter_id\n";
        }
        fwrite( $file, $output );
        fclose( $file );
        $this->success( [
                            'success' => 1,
                        ] );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-send-approvals';
    }
    
    /**
     * @return bool
     */
    protected function is_public() : bool {
        return false;
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'as-acceptance-data';
    }
    
}