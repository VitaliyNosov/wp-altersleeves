<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Objects\MC_User;

if( !isset( $idAlter ) ) return;
$idDesign = MC_Alter_Functions::design( $idAlter );
if( empty( $idDesign ) ) return;
$connectedDesigns = MC_Alter_Functions::design_connected( $idDesign );
$connectedDesigns = !empty( $connectedDesigns ) && is_array( $connectedDesigns ) ? $connectedDesigns : [ $idDesign ];
$connectedDesigns = array_unique( $connectedDesigns );
$framecode        = MC_Alter_Functions::framecodeId( $idAlter );
$alters           = [];
foreach( $connectedDesigns as $connectedDesign ) {
    $alters = MC_Alter_Functions::design_alters( $connectedDesign );
    foreach( $alters as $alter_id ) {
        if( MC_Alter_Functions::framecodeId( $alter_id ) == $framecode && ( get_post_status( $alter_id ) == 'publish' || MC_User_Functions::isAdmin() ) ) $alters[] = $alter_id;
    }
}
$alters = array_unique( $alters );
if( count( $alters ) < 2 ) return;

?>
<div class="py-3">
    <h2>Alters with related design</h2>
    <div class="row justify-content-start">
        <?php
        foreach( $alters as $alter_id ) :
            if( get_post_status( $alter_id ) != 'publish' ) continue;
            $image = MC_Alter_Functions::getCombinedImage( $alter_id );
            ?>
            <div class="col-4 col-sm-2 <?php if( $alter_id == $idAlter ) echo 'live-link'; ?>"><a href="<?= get_the_permalink( $alter_id ) ?>">
                    <a href="<?= get_the_permalink( $alter_id ) ?>">
                        <img class="card-display my-2" src="<?= $image ?>" style="max-width:100px;">
                    </a>
                    <?php if( MC_User::authorCurrentObject() || MC_User_Functions::isAdmin() ) : ?>
                        <br><?= $alter_id ?>
                    <?php endif; ?>
            </div>
        <?php
        endforeach; ?>
    </div>
</div>