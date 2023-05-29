<?php

use Mythic_Core\Display\MC_Forms;
use Mythic_Core\Functions\MC_Licensing_Functions;

if( empty( $user_id ) ) return;

$form_classes  = [ 'mc-new-prod-share-form' ];
$fields        = MC_Licensing_Functions::getNewProdShareFields();
$artist_id     = !empty( $_GET['artist_id'] ) ? $_GET['artist_id'] : get_current_user_id();
$affiliate_id  = !empty( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : 0;
$hidden_fields = [
    [ 'id_part' => 'prod-share-artist-id', 'val' => $artist_id ],
    [ 'id_part' => 'prod-share-publisher-id', 'val' => $affiliate_id ],
];

MC_Forms::mcFormRender( $form_classes, $fields, 'Send Rights', $hidden_fields );