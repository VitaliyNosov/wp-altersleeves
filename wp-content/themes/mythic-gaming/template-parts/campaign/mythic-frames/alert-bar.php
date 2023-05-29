<?php

if( !is_user_logged_in() ) return;

$teams     = MC_Mythic_Frames_Functions::sorted_teams();
$user_id   = !empty( $user ) ? $user->ID : 0;
$backer_id = MC_Mythic_Frames_Functions::getBackerId();
$backer_id = MC_User_Functions::isAdmin() && !empty( $_GET['backer_id'] ) ? $_GET['backer_id'] : $backer_id;
if( empty( $backer_id ) ) return;
$backer_name = !empty( $user ) ? $user->display_name : '';

$credits        = MC_Mythic_Frames_Functions::hasRemainingCredits( $backer_id );
$address        = MC_Mythic_Frames_Functions::hasAddress( $backer_id );
$fee_product_id = MC_Mythic_Frames_Functions::feeProductId( $backer_id );
$needs_to_pay   = !empty( $fee_product_id ) && !MC_Woo_Order_Functions::userIdHasBoughtProduct( $fee_product_id );
if( !$credits && $address && !$needs_to_pay ) return;
if( ( MC_Url::isCheckoutPage() || MC_Url::isCartPage() ) && !$credits && !$needs_to_pay ) return;
?>

<div class="bg-dark">
    <div class="container">
        <h3 class="text-light my-2">Action Required - Click buttons to action</h3>
        <div class="row align-items-center">
            <?php if( $credits ) : ?>
                <div class="col-sm-auto">
                    <a class="button" href="/campaign?backer_id=<?= $backer_id ?>" title="Click to allocate credits to design">Allocate credits</a>
                </div>
            <?php endif; ?>

            <?php if( !$address ) : ?>
                <div class="col-sm-auto">
                    <a class="button" href="/dashboard/edit-address/" title="Click here to change address">Add shipping address</a>
                </div>
            <?php endif; ?>

            <?php MG_Render::campaign( 'mythic-frames', 'to-pay' ); ?>
        </div>
    </div>
</div>
