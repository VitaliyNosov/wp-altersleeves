<?php

namespace Mythic_Core\Functions;

use Intervention\Image\ImageManagerStatic;
use Mythic_Core\Objects\MC_Mtg_Printing;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Utils\MC_Images;
use Mythic_Core\Utils\MC_Url;
use Mythic_Core\Utils\MC_Vars;
use phpmailerException;
use WP_Error;

/**
 * Class MC_Collection_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Collection_Functions {
    
    /**
     * @param int $idCollection
     *
     * @return array
     */
    public static function singles( int $idCollection = 0 ) {
        if( empty( $idCollection ) ) return [];
        $data = MC_WP::meta( '_bto_data', $idCollection );
        
        return !empty( $data ) ? $data : [];
    }
    
}