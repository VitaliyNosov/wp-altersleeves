<?php

use Mythic_Core\Display\MC_Forms;
use Mythic_Core\Users\MC_Affiliates;

$form_classes = [ 'mc-af-add-new-affiliate-form' ];

$fields        = MC_Affiliates::getRegisterNewAffiliateFields();
$hidden_fields = [
    [
        'mc_nonce' => 'mc_affiliate_data',
    ],
    [
        'mc_nonce' => 'mc_affiliate_coupon_data',
    ],
];
MC_Forms::mcFormRender( $form_classes, $fields, 'Register', $hidden_fields );
?>