<?php

use Mythic_Core\Users\MC_Affiliates;

$idUser = wp_get_current_user()->ID;
if( MC_User_Functions::isAdmin() ) $idUser = 2;
$userOrders        = wc_get_customer_order_count( $idUser );
$commissionBackers = [ 1612, 382, 950, 496, 105, 902, 350, 101, 633, 1243, 803, 366, 847, 680, 291, 1182, 304 ];

$commissionBacker = false;
if( in_array( $idUser, $commissionBackers ) ) $commissionBacker = true;

global $wp;
$url = home_url( $wp->request );

?>

<nav class="sidebar d-md-block col-md-auto">
    <a class="nav-item" href="<?= MC_SITE ?>/dashboard">Dashboard</a>
    <?php
    // TODO: disable Affiliate control for non-admins for now (if it's necessary)
    if( MC_User_Functions::isAdmin() || MC_Affiliates::is() ) : ?>
        <a class="nav-item" href="<?= MC_SITE ?>/dashboard/affiliate-control">Affiliate Control</a>
    <?php
    endif; ?>
    <?php
    if( MC_User_Functions::isArtist() || MC_User_Functions::isContentCreator() || MC_User_Functions::isRetailer() ) : ?>
        <a class="nav-item" href="<?= MC_SITE ?>/dashboard/creator-hub">Creator Hub</a>
    <?php
    endif; ?>
	<?php
	if( MC_User_Functions::isContentCreator() ) : ?>
      <a class="nav-item" href="<?= MC_SITE ?>/dashboard/promo-mailing">Promo mailing</a>
	<?php
	endif; ?>
    <?php
    if( MC_User_Functions::isArtist() || MC_User_Functions::isAdmin() ) : ?>
        <a class="nav-item" href="<?= MC_SITE ?>/dashboard/submit-alter">Submit alter</a>
        <a class="nav-item" href="<?= MC_SITE ?>/dashboard/manage-alters">Manage alters</a>
        <a class="nav-item" href="<?= MC_SITE ?>/dashboard/manage-collections">Manage Collections</a>
    <?php
    endif; ?>
    <?php
    if( MC_User_Functions::isMod() || MC_User_Functions::isAdmin() ) : ?>
        <a class="nav-item" href="<?= MC_SITE ?>/dashboard/moderator-approval">Mod approval</a>
    <?php
    endif; ?>

    <a class="nav-item" href="<?= MC_SITE ?>/dashboard/account-details">Account Details</a>
    <?php
    if( $userOrders > 0 ) : ?>
        <a class="nav-item" href="<?= MC_SITE ?>/dashboard/orders">Orders</a>
    <?php
    endif; ?>
    <?php
    if( MC_Backer_Functions::roleBacker() || MC_User_Functions::isAdmin() ) : ?>
        <a class="nav-item" href="<?= MC_SITE ?>/dashboard/kickstarter-rewards">Kickstarter Rewards</a>
    <?php
    endif; ?>

    <?php
    if( MC_Affiliates::is() || MC_User::isContentCreator() || MC_User_Functions::isArtist() || MC_User_Functions::isAdmin() ) : ?>
        <a class="nav-item" href="<?= MC_SITE ?>/dashboard/update-profile">Edit Profile</a>
    <?php
    endif; ?>
    <?php
    if( MC_Affiliates::is() || MC_User_Functions::isAdmin() ) : ?>
        <a class="nav-item" href="<?= MC_SITE ?>/dashboard/select-favorites">Select Favorites</a>
    <?php
    endif; ?>
</nav>