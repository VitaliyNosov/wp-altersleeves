<?php

use Mythic_Core\Display\MC_Forms;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

$user_id   = !empty( $args['existing_user_id'] ) ? $args['existing_user_id'] : 0;
$coupon_id = !empty( $args['existing_promotion']['promotionId'] ) ? $args['existing_promotion']['promotionId'] : 0;

$form_classes = [ 'mc-af-edit-existing-coupon-form' ];

$fields = MC_Affiliate_Coupon::getExistingAffiliateCouponFields( $user_id, $coupon_id );

$hidden_fields = [
    [ 'id_part' => 'af-edit-existing-user-id-coupons', 'val' => $user_id ],
    [ 'mc_nonce' => 'mc_affiliate_coupon_data' ],
];

$args['existing_promotion']['freeProductsQuantity'] = !empty($args['existing_promotion']['freeProducts']) ? $args['existing_promotion']['freeProducts'] : 0;

MC_Forms::mcFormRender( $form_classes, $fields, 'Update', $hidden_fields, $args['existing_promotion'] );
?>