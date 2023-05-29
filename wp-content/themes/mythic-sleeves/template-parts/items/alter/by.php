<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Mtg_Printing_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;

if( !isset( $idDesign ) ) return;
if( get_post_type( $idDesign ) !== 'product' && get_post_type( $idDesign ) !== 'design' ) return;
$idAlter = MC_Alter_Functions::design_alter( $idDesign );
if( !isset( $type ) ) $type = 'design';

$printing_id = MC_Alter_Functions::printing( $idAlter );

$printing       = new MC_Mtg_Printing( $printing_id );
$name_card      = $printing->name;
$card           = get_term_by( 'name', $name_card, 'mtg_card' );
$setName        = $printing->set_name;
$set            = get_term_by( 'name', $setName, 'mtg_set' );
$nameAlterist   = MC_Alter_Functions::getAlteristDisplayName( $idAlter );
$creatorProfile = MC_Alter_Functions::getAlteristProfileUrl( $idAlter );
$image          = MC_Alter_Functions::getCombinedImage( $idAlter );
if( empty( $image ) ) return;

$cart = true;
$url  = get_the_permalink( $idDesign );
if( !empty( $outputPrimary ) ) {
    $printing_id = MC_Mtg_Printing_Functions::idForSelection( $idDesign );
    $url         = get_the_permalink( $idDesign ).'?printing_id='.$printing_id.'&card_name='.urlencode( $name_card );
}
?>
<div id="<?= $idAlter ?>" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
    <div class="text-center">
        <a href="<?= $url ?>"><img class="card-corners card-display" src="<?= $image ?>"></a>
    </div>
    <div class="row pt-2">
        <?php echo MC_Product_Functions::getPrices( $idAlter ); ?>
        <div class="col-auto">
            <a class="cas-add-to-cart" href="javascript:void(0);" data-printing-id="<?= $printing_id ?>" data-alter-id="<?= $alter_id ?>"><i
                        class="fas fa-shopping-cart"></i></a>
            <?php Mythic_Core\Ajax\Store\Cart\MC_Add_Alter::render_nonce(); ?>
        </div>
    </div>
</div>
