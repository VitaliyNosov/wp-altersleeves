<?php

use Mythic_Core\Functions\MC_Alter_Functions;

if( empty( $idAlterist ) ) return;

$alters = MC_Alter_Functions::getAltersFromDesignGroupsByArtist( $idAlterist );

if( empty( $alters ) ) return;

?>
<div class="py-3">
    <h3>Designs by <?= $alterist->display_name ?> - <a href="/browse?browse_type=by&artist_id=<?= $idAlterist ?>">see all</a></h3>
    <div class="row justify-content-start">
        <?php
        
        foreach( $alters as $alter_id ) {
            $printing_id         = MC_Alter_Functions::printing( $alter_id );
            $args['alter_id']    = $alter_id;
            $args['printing_id'] = $printing_id;
            ?>
            <div class="col-lg-2 col-md-3 col-6 col-sm-4 my-2">
                <?php MC_Alter_Functions::render( $args ); ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>