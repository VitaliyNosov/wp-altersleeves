<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Mtg_Printing_Functions;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;

if( !isset( $idDesign ) ) return;

$idAlter        = as_design_alter( $idDesign );
$nameAlterist   = MC_Alter_Functions::getAlteristDisplayName( $idAlter );
$creatorProfile = MC_Alter_Functions::getAlteristProfileUrl( $idAlter );
$url            = get_the_permalink( $idAlter );
$printing_id    = empty( $forcePrinting ) ? MC_Mtg_Printing_Functions::idForSelection( $idAlter ) : $forcePrinting;
$printing       = new MC_Mtg_Printing( $printing_id );
$name_card      = $printing->name;
$url            = get_the_permalink( $idAlter ).'?printing_id='.$printing_id.'&card_name='.urlencode( $name_card );

$image = MC_Alter_Functions::getCombinedImage( $idAlter, $printing_id );
$info  = '';
$info  .= '<span><a href="'.$creatorProfile.'">'.$nameAlterist.'</a></span>';
$cart  = true;
if( in_array( $idDesign, MC_Product_Functions::not_for_sale() ) ) $cart = false;
?>
<div id="<?= $idAlter ?>" class="browsing-item col-6 col-md-3 my-3">
    <div class="text-center">
        <a href="<?= $url ?>"><img class="card-corners card-display" src="<?= $image ?>"></a>
    </div>
    <?php if( $cart ) :
        $cart = '<i class="fas fa-shopping-cart"></i>';
        ?>
        <div class="row pt-2">
            <?php echo MC_Product_Functions::getPrices($idAlter); ?>
            <div data-alter-id="<?= $idAlter ?>" class="col-auto float-end cas-add-to-cart browsing-item__cart-link"
                 data-printing-id="<?= $printing_id ?>" data-alter-id="<?= $idAlter ?>"><?= $cart ?></div>
            <?php Mythic_Core\Ajax\Store\Cart\MC_Add_Alter::render_nonce(); ?>
        </div>
    <?php endif; ?>
    <div class="browsing-item__info">
        <?= $info ?>
    </div>
</div>

