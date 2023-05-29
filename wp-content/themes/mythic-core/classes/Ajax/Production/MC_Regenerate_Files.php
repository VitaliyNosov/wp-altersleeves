<?php

namespace Mythic_Core\Ajax\Production;

use MC_Alter_Functions;
use MC_Opendrive;
use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class MC_Regenerate_Files
 *
 * @package Mythic_Core\Ajax\Production
 */
class MC_Regenerate_Files extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $idAlter = $_POST['product_id'];
        
        $opendrive = new MC_Opendrive();
        $folders   = [
            'ODdfMTgwNjgxNF9VaFd6Ug',
        ];
        
        foreach( $folders as $folder ) {
            $readyFiles = $opendrive->opendriveFolderContent( $folder );
            $readyFiles = $readyFiles['Files'];
            foreach( $readyFiles as $readyFile ) {
                $name   = $readyFile['Name'];
                $name   = str_replace( '.pdf', '', $name );
                $fileId = $readyFile['FileId'];
                if( $name == $idAlter ) {
                    $opendrive->opendriveFileRemove( $fileId, $folder );
                    break;
                }
            }
        }
        
        MC_Alter_Functions::designProcessor( $idAlter );
        $printings = MC_Alter_Functions::printingsBrowsing( $idAlter );
        if( is_array( $printings ) ) {
            foreach( $printings as $printing_id ) {
                MC_Alter_Functions::designProcessor( $idAlter, $printing_id );
            }
        }
        
        $this->success( [
                            'success' => 1,
                            'product' => $idAlter,
                        ] );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'cas-regenerate-variation-files';
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