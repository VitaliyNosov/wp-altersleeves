<?php

$designs = MC_Ranked_Sale::getAll( 10 );
shuffle( $designs );
$designs = array_slice( $designs, 0, 6 );
?>
<div class="py-3">
    <h3>Check out these popular alters</a></h3>
    <div class="row justify-content-start">
        <?php
        foreach( $designs as $design ) {
            $idDesign = $design->product_id;
            if( get_post_type( $idDesign ) !== 'product' && get_post_type( $idDesign ) !== 'design' ) continue;
            if( get_post_type( $idDesign ) == 'design' ) $idDesign = MC_Alter_Functions::design_alter( $idDesign );
            include( TP_ITEMS_ALTER_A );
        }
        ?>
    </div>
</div>
