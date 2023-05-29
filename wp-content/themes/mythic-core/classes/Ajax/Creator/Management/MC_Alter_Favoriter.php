<?php

namespace Mythic_Core\Ajax\Creator\Management;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Alter_Functions;

/**
 * Class MC_Alter_Favoriter
 *
 * @package Mythic_Core\Ajax\Creator\Management\Design
 */
class MC_Alter_Favoriter extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $term               = $_REQUEST['term'];
        $response           = [
            'id'     => 0,
            'output' => '',
        ];
        $idDesign           = is_numeric( $term ) ? $term : url_to_postid( $term );
        $response['id']     = $idDesign;
        $alters             = MC_Alter_Functions::design_alters( $idDesign );
        $response['alters'] = $alters;
        if( empty( $alters ) || !is_array( $alters ) ) $this->success( $response );
        $idAlter = $alters[0];
        if( !is_numeric( $idAlter ) ) $this->success( $response );
        ob_start();
        $image = MC_Alter_Functions::getCombinedImage( $idAlter );
        ?>
        <div id="design-connected-id-<?= $idDesign ?>" class="col-md-2 col-sm-3 text-center mt-3">
            <p class="my-1 text-danger design-remove-fav" data-design-id="<?= $idDesign ?>">Remove
                <i class="mx-2 fas fa-times"></i></p>
            <img class="card-display" src="<?= $image ?>">
        </div>
        <?php
        $response['output'] = ob_get_clean();
        
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-alter-favoriter';
    }
    
}
