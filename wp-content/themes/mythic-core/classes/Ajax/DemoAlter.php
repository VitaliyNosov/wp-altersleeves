<?php

namespace Mythic_Core\Ajax;

use MC_Alter_Functions;
use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Objects\MC_Mtg_Printing;

class DemoAlter extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $type     = (int) $_POST['type'] ?? 0;
        $alter_id = $_POST['alter_id'] ?? 0;
        
        if( !empty($alter_id) ) {
            $alter_id     = MC_Alter_Functions::get_random_single( $type );
            $alter_img    = MC_Alter_Functions::image( $alter_id );
            $printing_id  = MC_Alter_Functions::printingId( $alter_id );
            $printing     = new MC_Mtg_Printing( $printing_id );
            $printing_img = $printing->image;
        }
        
        while( empty( $printing_img ) && empty( $alter_img ) ) {
            $alter_id     = MC_Alter_Functions::get_random_single( $type );
            $alter_img    = MC_Alter_Functions::image( $alter_id );
            $printing_id  = MC_Alter_Functions::printingId( $alter_id );
            $printing     = new MC_Mtg_Printing( $printing_id );
            if( empty($printing->image) ) continue;
            if( strpos( $printing->image, 'svg') !== false ) continue;
            $printing_img = $printing->image;
        }
        
        $this->success( [
                            'alter_id'     => $alter_id,
                            'printing_id'  => $printing_id,
                            'alter_img'    => $alter_img,
                            'printing_img' => $printing_img
                        ] );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'demo-alter';
    }
    
}
