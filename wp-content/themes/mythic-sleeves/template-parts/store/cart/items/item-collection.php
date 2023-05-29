<?php

use Mythic_Core\Objects\Collection;

if( !isset( $itemKey ) ) return;

$cartItem     = WC()->cart->get_cart_item( $itemKey );
$collectionId = $cartItem['product_id'];
$product      = wc_get_product( $collectionId );
$quantity     = $cartItem['quantity'];
$nameAlterist = MC_Alter_Functions::getAlteristDisplayName( $collectionId );
$designs      = Collection::designs( $collectionId );
$count        = count( $designs );
$name         = $count.' x Alter Sleeves';
?>

<div id="<?= $itemKey ?>" class="row align-items-center py-2 cart-item cart-item--collection">
    <div data-cart-item="<?= $itemKey ?>" class="col-auto cart-item__remove-wrapper action__remove-from-cart p-0 text-center">
        <i class="cart-item__remove fas fa-times"></i>
    </div>
    <div class="col">
        <p>Collection: <?= $name ?> by <?= $nameAlterist ?></p>
        <div class="row align-items-center justify-content-start">
            <?php
            foreach( $designs as $design ) :
                $idDesign = $design;
                $image = MC_Alter_Functions::getCombinedImage( $idDesign );
                $idPrinting = MC_Alter_Functions::printing( $idDesign );
                $name_card = MC_Mtg_Printing_Functions::name_card( $idPrinting );
                $setName = MC_Mtg_Printing_Functions::name_set( $idPrinting );
                ?>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 cart-item__info">
                    <img class="cart-item__image-alter card-corners mb-2" src="<?= $image ?>">
                    <p class=" mb-0"><?= $name_card ?><br>
                        <?= $setName ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
