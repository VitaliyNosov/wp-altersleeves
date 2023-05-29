<?php

global $current_user;

use Mythic_Core\Objects\MC_Backer;

$email         = $current_user->user_email;
$backer_object = get_page_by_title( $email, OBJECT, 'backer' );
if( empty( $backer_object ) ) return;
$idBacker = $backer_object->ID;

$has_ordered    = MC_Woo_Order_Functions::userHasPreviouslyPurchased( $idBacker );
$backer_credits = MC_Backer::remainingSingleCredits();

if( empty( $has_ordered ) ) {
    $sets = [
        'set3bl'  => 6,
        'set4s'   => 8,
        'set5pan' => 10,
        'set6com' => 12
    ];
    foreach( $sets as $key => $value ) {
        $set = MC_WP::meta( $key, $idBacker );
        if( empty( $set ) ) continue;
        $value          = $value * $set;
        $backer_credits = $backer_credits - $value;
    }
}
if( empty( $backer_credits ) || $backer_credits < 0 ) return;
?>
<p class="text-success fw-bold">You have <?= $backer_credits ?> single credits available to use</p>