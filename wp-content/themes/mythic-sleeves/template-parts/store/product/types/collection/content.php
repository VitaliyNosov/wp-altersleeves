<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Artist_Functions;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\Objects\Collection;
use Mythic_Core\Objects\MC_User;
use Mythic_Core\System\MC_WP;

$idCollection = get_the_ID();
$isCreator    = false;
$idUser       = 0;
if( is_user_logged_in() ) {
    $idUser = wp_get_current_user()->ID;
    if( $idUser == MC_WP::authorId( $idCollection ) ) $isCreator = true;
}
$singles        = Collection::designs( $idCollection );
$count          = count( $singles );
$nameAlterist   = MC_WP::authorName( $idCollection );
$creatorProfile = MC_Artist_Functions::urlProfile( MC_WP::authorId( $idCollection ) );

?>
<div class="text-center py-3">
    <?php if( $idCollection == 178017 ) : ?>
        <h1>The #Secretstory by <a href="<?= $creatorProfile ?>"><?= $nameAlterist ?></a></h1>
    <?php else : ?>
        <h1><?php echo $count ?>Sleeve Collection by <a href="<?= $creatorProfile ?>"><?= $nameAlterist ?></a></h1>
    <?php endif; ?>

</div>
<?php if( $count <= 6 ) : ?>
    <div class="row justify-content-center px-3">
        <?php foreach( $singles as $idDesign ) :
            $idAlter = MC_Alter_Functions::design_alter( $idDesign );
            $image = MC_Alter_Functions::getCombinedImage( $idAlter ); ?>
            <div class="col-auto text-center py-2">
                <img class="card-display" src="<?= $image ?>" style="max-width:140px;width:100%;">
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if( $idCollection != 178017 ) : ?><?php if( MC_Backer_Functions::roleBacker() && empty( MC_User::meta( 'mc_sets_redeemed' ) ) ) : ?>
    <div class="text-center py-3">
        <button class="as-cart-collection add2Cart--set cas-button" data-product-id="<?= $idCollection ?>">
            Add Collection to cart
        </button>
        <p class="as-notice-collection pt-2" style="display: none;"><strong>Please confirm all your selections below to add the alters to your
                cart.</strong></p>
    </div>
<?php else : ?><?php if( !in_array( $idCollection, MC_Product_Functions::snapboltCollectionIds() ) ) : ?>
    <div class="text-center py-3">
        <button class="as-cart-collection cas-button" data-alter-id="<?= $idCollection ?>">
            Add Collection to cart
        </button>
        <p class="as-notice-collection pt-2" style="display: none;"><strong>Please confirm all your selections below to add the alters to your
                cart.</strong></p>
    </div>
<?php endif; ?><?php endif; ?><?php endif; ?>
<hr>
<div class="row">
    <?php
    $singles = array_values( $singles );
    foreach( $singles as $target => $idDesign ) {
        include 'design.php';
    } ?>
</div>
<input id="as-collection-required" type="hidden" value="<?= $count ?>">
<input id="as-collection-alters" type="hidden" value="">
<input id="as-collection-selected" type="hidden" value="0">