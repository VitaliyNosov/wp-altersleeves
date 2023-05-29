<?php

use Mythic_Core\Display\MC_Render;
use Mythic_Core\Display\MC_Tabs_With_Panes;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

use Mythic_Core\Users\MC_Affiliates;
use Mythic_Core\Functions\MC_User_Functions;

if( !MC_User_Functions::isAdmin() && !MC_Affiliates::is() ) return;

$is_admin  = MC_User_Functions::isAdmin();
$tabs_info = [
    'mc_affiliates' => [
        'title'   => 'Affiliates',
        'content' => [ 'slug' => 'affiliates/panes/pane', 'name' => 'mc_affiliates' ],
        'active'  => 1,
    ],
    'mc_promotions' => [
        'title'   => 'Promotions',
        'content' => [ 'slug' => 'affiliates/panes/pane', 'name' => 'mc_promotions' ],
        'active'  => 0,
    ],
];
$args      = [
    'existing_user_id'   => 0,
    'existing_user_data' => [],
    'existing_promotion' => [],
];
if( !empty( $_GET['coupon_id'] ) ) {
    // coupon_id exists if current screen for coupon editing
    $tabs_info['mc_affiliates']['active'] = 0;
    $tabs_info['mc_promotions']['active'] = 1;
    $args['existing_promotion']           = MC_Affiliate_Coupon::getAffiliatePromotionDataById( $_GET['coupon_id'] );
    $args['existing_user_id']             = !empty( $args['existing_promotion']['userId'] ) ? $args['existing_promotion']['userId'] : 0;
} else if( !empty( $_GET['coupons_affiliate_id'] ) ) {
    // coupons_affiliate_id exists if current screen for all affiliate coupons
    $tabs_info['mc_affiliates']['active'] = 0;
    $tabs_info['mc_promotions']['active'] = 1;
    $args['existing_user_id']             = $_GET['coupons_affiliate_id'];
    $affiliate_coupons                    = MC_Affiliate_Coupon::getAffiliatePromotions( $args['existing_user_id'] );
    if( !empty( $affiliate_coupons[0]['wc_coupon_id'] ) ) {
        $args['existing_promotion'] = MC_Affiliate_Coupon::getAffiliatePromotionDataById( $affiliate_coupons[0]['wc_coupon_id'] );
    } else {
        $args['existing_promotion']['promotionId'] = 0;
    }
} else if( !empty( $_GET['affiliate_id'] ) ) {
    // affiliate_id exists if current screen for affiliate editing
    $args['existing_user_id'] = $_GET['affiliate_id'];
}

if( !$is_admin ) {
    $current_user_id = get_current_user_id();
    if( $args['existing_user_id'] != $current_user_id ) {
        $args['existing_user_id'] = $current_user_id;
        $affiliate_coupons        = MC_Affiliate_Coupon::getAffiliatePromotions( $args['existing_user_id'] );
        if( !empty( $affiliate_coupons[0]['wc_coupon_id'] ) ) {
            $args['existing_promotion'] = MC_Affiliate_Coupon::getAffiliatePromotionDataById( $affiliate_coupons[0]['wc_coupon_id'] );
        } else {
            $args['existing_promotion']['promotionId'] = 0;
        }
    }
}

if( !empty( $args['existing_user_id'] ) ) {
    $affiliate_data = MC_Affiliates::getAffiliateData( $args['existing_user_id'] );
    if( !empty( $affiliate_data['userData'] ) ) {
        $args['existing_user_data'] = $affiliate_data['userData'];
    }
} ?>

<div class="mc-affiliates-control-container">
    <?php if( $is_admin ) {
        MC_Tabs_With_Panes::mcTabsRender( $tabs_info, [ 'args' => $args ] );
    } else {
        MC_Render::templatePart( 'affiliates/panes/pane', 'promotions_by_affiliate', [ 'args' => $args ] );
    } ?>
</div>
