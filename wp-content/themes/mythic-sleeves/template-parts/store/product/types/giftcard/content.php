<?php

use Mythic_Core\Functions\MC_Giftcard_Functions;

$gift_card_id = 173705;
$digital      = MC_Giftcard_Functions::digital( $gift_card_id );
$sleeves      = MC_Giftcard_Functions::sleeves( $gift_card_id );
$gift_card    = wc_get_product( $gift_card_id );
$price        = (int) $gift_card->get_price();
$price        = $price + 1;

?>
<div class="wing bg-white-wing"></div>
<div class="container bg-white-content">
    <h1 class="text-center py-3">Alter Sleeves Gift Cards</h1>
    <div class="row align-items-stretch mb-3" id="design-0">
        <div class="col-md-auto offset-md-0">
            <div class="product-slider-wrapper  my-4" id="as-slider-0">
                <div class="product-slider product-slider--down" data-target="0">
                    <div class="product-slider-images">
                        <img alt="Gift card image" class="product-slider-image product-slider-image__printing"
                             src="<?= AS_URI_IMG.'/store/giftcardcard.png' ?>">
                    </div>
                </div>
            </div>
        </div>
        <aside class="bg-blue cas-product-sidebar cas-product-alter-sidebar cas-product-action col-sm-4">
            <div class="cas-product-sidebar-content-container cas-product-alter-sidebar-content-container">
                <div class="cas-product-sidebar-content cas-product-alter-sidebar-content" style="padding-top:3rem;">
                    <?php MC_Giftcard_Functions::fieldSleevesByType( [
                                                                         'digital' => $digital,
                                                                         'sleeves' => $sleeves,
                                                                     ] ); ?>
                    <p class="cas-product-sidebar-info"></p>
                    <p class="cas-product-sidebar-info">Quantity</p>
                    <p><input class="cas-product-sidebar-input--text cas-product-sidebar-input cas-product-quantity" id="quantity-128514"
                              type="number" value="1"></p>

                    <div class="mb-3 cas-product-sidebar-info">
                        <label for="input-gift-card-type">Type of Gift Card</label>
                        <select class="form-control" id="input-gift-card-type" name="giftcard_type">
                            <option value="0">Physical - shipped by mail</option>
                            <option value="1"<?php if( $digital ) : ?> selected <?php endif; ?>>Digital - attached to email</option>
                        </select>
                    </div>

                    <div class="text-white"><span class="cas-product-sidebar-price">$<span class="gift-card-price"><?= $price ?></span></span><br>Inc.
                        VAT for EU customers<span
                                class="gift-card-type-info" <?php if( $digital ) : ?> style="display: none;"<?php endif; ?>><br><small>$1 charge for
                                physical gift cards</small></span>
                    </div>
                    <button class="cas-add-gift-card-to-cart cas-button" data-product-id="<?= $gift_card_id ?>">Add to cart</button>
                </div>
            </div>
        </aside>
    </div>
    <div class="text-sm-center mx-sm-5 my-4" style="font-size: 20px;">
        <p>Alter Sleeves gift cards are used to redeem specified quantity of sleeves: these can be used over multiple orders.</p>

        <p>Digital gift cards will be sent attached to the purchase confirmation email. They will be instantly redeemable.</p>

        <p>If five or more sleeves are purchased at a single time using a gift card, then untracked shipping is free; if less then additional shipping
            costs may apply. There is an additional charge of $1 for physical gift cards.</p>
    </div>
</div>
<div class="wing bg-white-wing"></div>