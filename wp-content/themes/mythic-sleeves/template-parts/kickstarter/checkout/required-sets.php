<?php

global $current_user;

$email         = $current_user->user_email;
$backer_object = get_page_by_title( $email, OBJECT, 'backer' );
$idBacker      = '';
if( $backer_object ) {
    $idBacker = $backer_object->ID;
}
$current_user = wp_get_current_user();
$output       = '';
if( $idBacker != '' ) :
    $setKeys = [
        [ 'key' => 'set3bl', 'name' => '3 Sleeve Set - Basic Lands' ],
        [ 'key' => 'set4s', 'name' => '4 Sleeve Set - Four Seasons' ],
        [ 'key' => 'set5pan', 'name' => '5 Sleeve Set - Panoramic Lands' ],
        [ 'key' => 'set6com', 'name' => '6 Sleeve Set - Commander' ]
    ];
    
    foreach( $setKeys as $setKey ) {
        $key  = $setKey['key'];
        $name = $setKey['name'];
        $set  = MC_WP::meta( $key, $idBacker );
        $set  = ( strlen( $set ) > 0 ? $set : '' );
        if( $set != '' ) {
            $output .= '<li>'.$set.' x '.$name.'</li>';
        }
    }
    if( isset( MC_WP::meta( 'setsnapbolt', $idBacker )[0] ) ) {
        $output .= '<li>Exclusive Kickstarter 2 Sleeve Set - Snap/Bolt</li>';
    }
    
    if( $output != '' ) {
        $warning = '<p class="red"><strong>Before you can submit your order, the sets in your cart must match your set rewards:</strong></p>';
        $output  = $warning.'<ul class="red">'.$output.'</ul>';
    }
endif;
echo $output;
echo '<p>You can select your sets from <a href="/sets">here</a></p>';