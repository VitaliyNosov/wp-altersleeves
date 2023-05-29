<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\System\MC_WP;


if( isset( $idDesign ) ) {
    $idAlter = MC_Alter_Functions::design_alter( $idDesign );
} else {
    if( isset( $idAlter ) ) {
        $isAlter   = true;
        $idProduct = $idAlter;
        $idDesign  = MC_Alter_Functions::design( $idAlter );
    } else {
        $isAlter = MC_Product_Functions::isAlter( $idProduct );
        if( !$isAlter ) return;
        $idAlter  = $idProduct;
        $idDesign = MC_Alter_Functions::design( $idAlter );
    }
}

$isCollection = MC_Product_Functions::isCollection( $idProduct );
$typeDesign   = MC_Alter_Functions::design_type( $idDesign, 'key' );
$printing_id  = isset( $_GET['printing_id'] ) && $isAlter ? $_GET['printing_id'] : MC_Alter_Functions::printing( $idAlter );
if( empty( $printing_id ) ) $printing_id = MC_Alter_Functions::printing( $idAlter );
/*
if ( $idDesign ) {
    $alters = MC_Alter_Functions::design_alters($idDesign);
    if ( count($alters) > 1 ) {
        $framecode = MC_Mtg_Printing_Functions::codeFromId($printing_id);
        foreach($alters as $alter) {
            if ( has_term($framecode, 'frame_code', $alter) ) {
                $idAlter = $alter;
                break;
            }
        }
    }
}
*/
$anchorAlter = $idAlter;

$isCreator = false;
$idUser    = 0;
if( is_user_logged_in() ) {
    $idUser = wp_get_current_user()->ID;
    if( $idUser == MC_WP::authorId( $idAlter ) ) $isCreator = true;
}
?>
<div class="wing bg-white-wing"></div>
<div class="container">
    <div id="design-0" class="row align-items-stretch">
        <div class="cas-product-alter-primary col-md-9 bg-white-content">
            <div class="row">
                <div class="col-md-auto offset-md-0 offset-sm-2 offset-lg-2">
                    <?= MC_Alter_Functions::displaySlider( $idAlter, 0 ); ?>
                </div>
                <div class="d-none d-md-block col-md">
                    <?= MC_Alter_Functions::displayInfo( $idAlter, 0 ); ?>
                </div>
            </div>
            <?php
            //include 'tags.php';
            include 'designs.php';
            //include 'alters.php';
            ?>
        </div>
        <?php
        $idAlter = $anchorAlter;
        include 'sidebar.php'; ?>
        <div class="d-md-none text-center cas-product-alter-info--mobile" style="background:#e6e6e6;">
            <?= MC_Alter_Functions::displayInfo( $idAlter, 0 ); ?>
        </div>
    </div>
    <div class="row bg-white-content">
        <div class="col">
            <?php
            if( MC_User_Functions::isAdmin() || MC_User_Functions::isMod() ) include 'flagging.php';
            include 'related.php';
            ?></div>
    </div>
</div>
<div class="wing bg-white-wing"></div>


