<?php

namespace Mythic_Core\Ajax;

use MC_Images;
use MC_Url;
use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class MC_Store_Webcam_Image
 *
 * @package Mythic_Core\Ajax\Acceptance
 */
class MC_Store_Webcam_Image extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mc-store-webcam-image';
    }
    
    public function execute() {
        extract( $_POST );
        if( empty( $img_data ) ) $this->error( 'No image data provided' );
        if( empty( $folder ) ) $this->error( 'No folder provided' );
        if( empty( $filename ) ) $this->error( 'No filename provided' );
        $folder = MC_Url::getUrlAndServerLocations( $folder );
        $dir    = $folder['dir'];
        if( !is_dir( $dir ) ) mkdir( $dir, 0755, true );
        $dir = MC_Url::cleanPath( $folder['dir'].'/'.$filename );
        $url =
            MC_Images::base64ToImage( $img_data, $dir );
        if( !empty( $product_id ) ) update_post_meta( $product_id, 'mc_approval_image', $url );
        $this->success( [ 'url' => $url, $_POST, $folder ] );
    }
    
}