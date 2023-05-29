<?php

use Mythic_Core\Display\MC_Forms;
use Mythic_Core\Users\MC_Affiliates;

// clear user data if current page is part of promotion flow
if( !empty( $args['existing_promotion'] ) ) $args['existing_user_data'] = [];

$form_classes  = [ 'mc-af-edit-existing-affiliate-form' ];
$fields        = MC_Affiliates::getUpdateAffiliateFields();
$hidden_fields = [
    [
        'id_part' => 'af-edit-existing-user-id',
        'val'     => !empty( $args['existing_user_data']['userId'] ) ? $args['existing_user_data']['userId'] : 0,
    ],
    [
        'mc_nonce' => 'mc_affiliate_data',
    ],
];

MC_Forms::mcFormRender( $form_classes, $fields, 'Update', $hidden_fields, $args['existing_user_data'] );