<?php

namespace Mythic_Core\Ajax\Production;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Production_Functions;
use Mythic_Core\Utils\MC_Vars;

class MC_File_To_Rasterlink extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $idProduct = $_POST['product_id'];
        $quantity  = isset( $_POST['quantity'] ) ? $_POST['quantity'] : 1;
        
        $order_id = $_POST['order_id'];
        
        $path = ABSPATH.'files/orders-'.MC_Production_Functions::user();
        if( !is_dir( $path ) ) mkdir( $path, 0755, true );
        
        for( $x = 1; $x <= $quantity; $x++ ) {
            $prefix = MC_Vars::generate( 3 );
            $file   = "$path/$order_id-$prefix-$idProduct.txt";
            $file   = fopen( $file, "w" );
            $output = "$order_id-$prefix-single\n";
            $output .= "$idProduct\n";
            fwrite( $file, $output );
            fclose( $file );
        }
        
        $this->success( [
                            'success' => 1,
                        ] );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-alter-pdf-rasterlink';
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
        return 'as-order-data';
    }
    
}