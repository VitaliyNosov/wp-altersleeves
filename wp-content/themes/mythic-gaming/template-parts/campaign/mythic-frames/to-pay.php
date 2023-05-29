<?php

$user_id   = !empty( $user ) ? $user->ID : 0;
$backer_id = MC_Mythic_Frames_Functions::getBackerId();
$backer_id = MC_User_Functions::isAdmin() && !empty( $_GET['backer_id'] ) ? $_GET['backer_id'] : $backer_id;
if( empty( $backer_id ) ) return;
$fee_product_id = MC_Mythic_Frames_Functions::feeProductId( $backer_id );
$needs_to_pay   = !empty( $fee_product_id ) && !MC_Woo_Order_Functions::userIdHasBoughtProduct( $fee_product_id );
if( !$needs_to_pay ) return;
?>

<div class="col-sm-auto">
    <form class="cart" action="/cart" method="post" enctype="multipart/form-data">
        <div class="quantity d-none">
            <label class="screen-reader-text" for="quantity_60c0a28a8ee14">831-kickstarter-fee quantity</label>
            <input type="number" id="quantity_60c0a28a8ee14" class="input-text qty text" step="1" min="1" max="" name="quantity" value="1"
                   title="Qty" size="4" placeholder="" inputmode="numeric">
        </div>
        <button type="submit" name="add-to-cart" value="<?= $fee_product_id ?>" class="single_add_to_cart_button button alt m-0">Pay outstanding fee
        </button>
    </form>
</div>
