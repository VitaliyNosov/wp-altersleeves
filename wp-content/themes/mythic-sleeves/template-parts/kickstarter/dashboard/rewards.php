<?php

global $current_user;

$email         = $current_user->user_email;
$backer_object = get_page_by_title( $email, OBJECT, 'backer' );
$idBacker      = '';
if( $backer_object ) {
    $idBacker = $backer_object->ID;
}
$current_user = wp_get_current_user();
?>
<ul class="list__rewards">
    <?php
    /** Commissions */
    if( isset( MC_WP::meta( '10comddx1', $idBacker )[0] ) ) :?>
        <li>10 commissioned digital alters with 1 printed Alter Sleeve of each</li>
    <?php endif; ?>
    <?php if( isset( MC_WP::meta( '4compx4', $idBacker )[0] ) ) : ?>
        <li>4 commissioned digital alters with 4 printed Alter Sleeves of each</li>
    <?php endif; ?>
    <?php if( isset( MC_WP::meta( '4comddx4', $idBacker )[0] ) ) : ?>
        <li>4 commissioned digital alters with 4 printed Alter Sleeves of each</li>
    <?php endif; ?>
    <?php if( isset( MC_WP::meta( '1compx4', $idBacker )[0] ) ) : ?>
        <li>1 commissioned painted alter and 4 printed Alter Sleeves prints</li>
    <?php endif; ?>
    <?php if( isset( MC_WP::meta( '1comddx4', $idBacker )[0] ) ) : ?>
        <li>1 commissioned digital design with 4 Alter Sleeve prints</li>
    <?php endif; ?>
    <?php
    /** Sets **/
    $set = MC_WP::meta( 'set3bl', $idBacker );
    $set = ( strlen( $set ) > 0 ? $set : '' );
    if( $set != '' ) :?>
        <li><?= $set; ?> x 3 Sleeve Set - Basic Lands</li>
    <?php endif; ?>
    <?php
    $set = MC_WP::meta( 'set4s', $idBacker );
    $set = ( strlen( $set ) > 0 ? $set : '' );
    if( $set != '' ) :?>
        <li><?= $set; ?> x 4 Sleeve Set - Four Seasons</li>
    <?php endif; ?>
    <?php
    $set = MC_WP::meta( 'set5pan', $idBacker );
    $set = ( strlen( $set ) > 0 ? $set : '' );
    if( $set != '' ) :?>
        <li><?= $set; ?> x 5 Sleeve Set - Panoramic Lands</li>
    <?php endif; ?>
    <?php
    $set = MC_WP::meta( 'set6com', $idBacker );
    $set = ( strlen( $set ) > 0 ? $set : '' );
    if( $set != '' ) :?>
        <li><?= $set; ?> x 6 Sleeve Set - Commander</li>
    <?php endif; ?>
    <?php if( isset( MC_WP::meta( 'setsnapbolt', $idBacker )[0] ) ) : ?>
        <li>Exclusive Kickstarter 2 Sleeve Set - Snap/Bolt</li>
    <?php endif; ?>
    
    <?php
    $count      = 0;
    $quantities = [ 1, 2, 4, 6, 8, 10, 12, 30, 40, 60, 75, 100 ];
    /** Individual Sleeves **/
    foreach( $quantities as $quantity ) {
        $sleeves = MC_WP::meta( 'sleevesx'.$quantity, $idBacker );
        $count   = ( isset( $sleeves ) && $sleeves != '' ? $count + ( $sleeves * $quantity ) : $count );
    }
    if( $count != 0 ) :
        $text = ( $count == 1 ? '1 Alter Sleeve' : $count.' x Alter Sleeves' );
        ?>
        <li><?= $text ?> of your choice from our library of designs</li>
    <?php endif; ?>
    
    <?php
    /** Bonuses */
    if( isset( MC_WP::meta( 'access1', $idBacker )[0] ) ) :?>
        <li>1st Access to the Alter Sleeves Webstore</li>
    <?php endif; ?>
    <?php if( isset( MC_WP::meta( 'access2', $idBacker )[0] ) ) : ?>
        <li>2nd Access to the Alter Sleeves Webstore</li>
    <?php endif; ?>
    <?php if( isset( MC_WP::meta( 'superhastewebskin', $idBacker )[0] ) ) : ?>
        <li>Exclusive Super Haste webskin for the site when gated access closes</li>
    <?php endif; ?>
    <?php if( isset( MC_WP::meta( 'topwallheroes', $idBacker )[0] ) ) : ?>
        <li>Top Wall of Heroes Placement</li>
    <?php endif; ?>
    <?php if( isset( MC_WP::meta( 'placewallheroes', $idBacker )[0] ) ) : ?>
        <li>Wall of Heroes Placement</li>
    <?php endif; ?>
</ul>
