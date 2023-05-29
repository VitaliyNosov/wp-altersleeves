<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Backer_Functions;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\Objects\MC_User;

if( empty( $idDesign ) || !isset( $target ) ) return;

$idAlter     = MC_Alter_Functions::design_alter( $idDesign );
$printing_id = MC_Alter_Functions::printing( $idAlter );

?>
<div id="design-<?= $target ?>" class="as-design col-md-6 p-3">
    <div class="row">
        <div class="col-sm-auto">
            <?= MC_Alter_Functions::design_displaySlider( $idDesign, $target ); ?>
        </div>
        <div class="text-center text-sm-start py-3 col-sm as-product-info" data-target="<?= $target ?>">
            <?= MC_Alter_Functions::design_displayInfo( $idDesign, $target ); ?>
            <?php if( ( MC_Backer_Functions::roleBacker() && empty( MC_User::meta( 'mc_sets_redeemed' ) ) || !in_array( $idAlter,
                                                                                                                        MC_Product_Functions::snapboltIds() ) ) ) : ?>
                <button class="cas-add-to-cart  cas-button" data-alter-id="<?= $idAlter ?>" data-printing-id="<?= $printing_id ?>">
                    Add to cart
                </button>
            <?php endif; ?>

            <button class="cas-button--green cas-button as-confirm " data-alter-id="<?= $idAlter ?>" data-printing-id="<?= $printing_id ?>"
                    data-collection-key="<?= $target ?>" style="display: none;">
                Confirm
            </button>
            <span class="cas-green as-confirmed" style="display: none;">Alter confirmed</span>
            <span class="cas-red as-undo mx-2" style="display: none;" data-collection-key="<?= $target ?>"><strong><i
                            class="fas fa-times"></i></strong></span>
            <?php Mythic_Core\Ajax\Store\Cart\MC_Add_Alter::render_nonce(); ?>
        </div>
    </div>
</div>
