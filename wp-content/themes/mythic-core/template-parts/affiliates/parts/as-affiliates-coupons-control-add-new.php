<?php

use Mythic_Core\Display\MC_Forms;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

$form_classes = [ 'mc-af-add-new-coupon-form' ];

$fields        = MC_Affiliate_Coupon::getRegisterNewAffiliateCouponFields();
$hidden_fields = [ [ 'mc_nonce' => 'mc_affiliate_coupon_data' ] ];

MC_Forms::mcFormRender( $form_classes, $fields, 'Create', $hidden_fields );
?>