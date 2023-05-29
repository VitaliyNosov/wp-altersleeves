<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Mtg_Printing_Functions;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;

if( !isset( $idDesign ) ) return;
if( !isset( $type ) ) $type = 'design';

$nameAlterist   = MC_Alter_Functions::getAlteristDisplayName( $idDesign );
$creatorProfile = MC_Alter_Functions::getAlteristProfileUrl( $idDesign );
$url            = get_the_permalink( $idDesign );
$printing_id    = empty( $forcePrinting ) ? MC_Mtg_Printing_Functions::idForSelection( $idDesign ) : $forcePrinting;
$printing       = new MC_Mtg_Printing( $printing_id );
$name_card      = $printing->name;
$url            = get_the_permalink( $idDesign ).'?printing_id='.$printing_id.'&card_name='.urlencode( $name_card );

$image = MC_Alter_Functions::getCombinedImage( $idDesign, $printing_id );
$info  = '';
$info  .= '<span><a href="'.$creatorProfile.'">'.$nameAlterist.'</a></span>';
$cart  = true;
if( in_array( $idDesign, MC_Product_Functions::not_for_sale() ) ) $cart = false;

?>
<div id="<?= $idDesign ?>" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
    <div class="text-center">
        <a href="<?= $url ?>"><img class="card-corners card-display" src="<?= $image ?>"></a>
    </div>
    <?php if( $cart ) :
        $cart = '<i class="fas fa-shopping-cart"></i>';
        ?>
        <div class="row pt-2">
            <?php echo MC_Product_Functions::getPrices($idDesign); ?>
            <div data-alter-id="<?= $idDesign ?>" class="col-auto float-end cas-add-to-cart browsing-item__cart-link"
                 data-printing-id="<?= $printing_id ?>" data-alter-id="<?= $idDesign ?>"><?= $cart ?></div>
            <?php Mythic_Core\Ajax\Store\Cart\MC_Add_Alter::render_nonce(); ?>
        </div>
    <?php endif; ?>
    <div class="browsing-item__info">
        <?= $info ?>
    </div>
</div>

