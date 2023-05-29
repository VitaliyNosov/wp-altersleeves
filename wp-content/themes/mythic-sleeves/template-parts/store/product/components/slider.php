<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Mtg_Printing_Functions;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;
use Mythic_Core\Objects\MC_User;
use Mythic_Core\Users\MC_Affiliates;

$switch  = false;
$isAlter = false;
if( !isset( $target ) ) $target = 0;
if( !isset( $idProduct ) ) $idProduct = MC_Product_Functions::id();
if( isset( $idDesign ) ) {
    $idAlter = MC_Alter_Functions::design_alter( $idDesign );
} else {
    if( isset( $idAlter ) ) {
        $isAlter   = true;
        $idProduct = $idAlter;
        $idDesign  = MC_Alter_Functions::design_alter( $idAlter );
    } else {
        $isAlter = MC_Product_Functions::isAlter( $idProduct );
        if( !$isAlter ) return;
        $switch   = true;
        $idAlter  = $idProduct;
        $idDesign = MC_Alter_Functions::design( $idAlter );
    }
}
$isCollection = MC_Product_Functions::isCollection( $idProduct );

$printing_id = isset( $_GET['printing_id'] ) && $isAlter ? $_GET['printing_id'] : MC_Alter_Functions::printing( $idAlter );
if( empty( $printing_id ) ) $printing_id = MC_Alter_Functions::printing( $idAlter );
$printing = new MC_Mtg_Printing( $printing_id );

$imgPrinting       = $printing->imgJpgNormal;
$imgPrintingRes    = $printing->imgPng;
$framecodePrinting = $printing->framecode_id;
$alters            = MC_Alter_Functions::design_alters( $idDesign );
foreach( $alters as $alter ) {
    $printingAlter            = MC_Alter_Functions::printing( $alter );
    $printing                 = new MC_Mtg_Printing( $printing_id );
    $framecodePrintingCompare = MC_Mtg_Printing_Functions::codeFromId( $printingAlter );
    if( $framecodePrinting != $framecodePrintingCompare ) continue;
    $idAlter = $alter;
    break;
}
$imgAlter = MC_Alter_Functions::image( $idAlter );
?>

<div id="as-slider-<?= $target ?>" class="product-slider-wrapper">
    <?php if( MC_User_Functions::isAdmin() && !$isCollection && is_singular( 'product' ) ) : ?>
        <div class="row">
            <div class="col-sm text-center">
                <button data-design-id="<?= $idAlter ?>" class="blue--button design--ads">ASSETS</button>
            </div>
            <div class="col-sm text-center">
                <button data-design-id="<?= $idAlter ?>" class="red--button design--regenerate">REGENERATE</button>
            </div>
            <div class="col-sm text-center">
                <button data-design-id="<?= $idAlter ?>" class="green--button design--reindex">REINDEX</button>
            </div>
        </div>
    <?php endif; ?>
    <?php if( ( MC_User::isContentCreator() ) && is_singular( 'product' ) )  :
        $favDesigns = get_user_meta( wp_get_current_user()->ID, 'mc_fav_designs', true );
        $favorited = in_array( $idAlter, $favDesigns );
        ?>
        <div class="row">
            <div class="col-sm text-center">
                <button data-design-id="<?= $idAlter ?>" class="blue--button design--ads">GET IMAGES</button>
            </div>
            <div class="col">
                <?php if( $favorited ) : ?>
                    <button data-alter-id="<?= $idAlter ?>" class="red--button p-2 action-unfav">UNFAVORITE</button>
                <?php else : ?>
                    <button data-alter-id="<?= $idAlter ?>" class="green--button p-2 action-fav">FAVORITE</button>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="product-slider product-slider--down" data-target="<?= $target ?>">
        <div class="product-slider-images">
            <img class="product-slider-image product-slider-image__printing" src="<?= $imgPrinting ?>">
            <img class="product-slider-image product-slider-image__alter product-slider--shake" src="<?= $imgAlter ?>">
        </div>
    </div>
    
    <?php Mythic_Core\Ajax\Marketing\MC_Get_Assets::render_nonce(); ?>
    <?php Mythic_Core\Ajax\Production\MC_Regenerate_Files::render_nonce(); ?>
</div>
