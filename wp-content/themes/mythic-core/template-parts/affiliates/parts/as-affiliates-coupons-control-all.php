<?php

use Mythic_Core\Display\MC_Render;
use Mythic_Core\Display\MC_Table_Flex;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

$is_ajax                 = is_ajax();
$page                    = get_query_var( 'paged', 1 );
$page                    = !empty( $page ) ? $page : 1;
$per_page                = 20;
$offset                  = ( $page - 1 ) * $per_page;
$search                  = !empty( $search ) ? $search : '';
$affiliate_coupons_count = MC_Affiliate_Coupon::getAllAffiliatePromotionsCount( $search );
if( empty( $affiliate_coupons_count ) ) return;
$coupons = MC_Affiliate_Coupon::getAllAffiliatePromotions( $search, $per_page, $offset );

$pagination_data = [];
if( $affiliate_coupons_count > $per_page ) {
    $pagination_data = [ $affiliate_coupons_count, $per_page, 'center', 1 ];
}

$page_body = '';
if( !$is_ajax ) {
    $uri_parts = explode( '?', $_SERVER['REQUEST_URI'], 2 );
    $page_body = home_url( $uri_parts[0] );
} else if( !empty( $_POST['currentPageUrl'] ) ) {
    $page_body = $_POST['currentPageUrl'];
}
$edit_link_body       = $page_body.'?affiliate_id=';
$coupons_link_body    = $page_body.'?coupon_id=';
$coupon_prepared_data = [];
foreach( $coupons as $coupon_key => $coupon ) {
    $user                   = get_user_by( 'ID', $coupon['user_id'] );
    $user_name              = !empty( $user->display_name ) ? $user->display_name : '';
    $user_email             = !empty( $user->user_email ) ? $user->user_email : '';
    $coupon_prepared_data[] = [
        'code'           => $coupon['promotion_title'],
        'user_email'     => $user_email,
        'user_name'      => '<a href="'.$edit_link_body.$coupon['user_id'].'">'.$user_name.'</a>',
        'coupons_link'   => '<a href="'.$coupons_link_body.$coupon['wc_coupon_id'].'">Edit Promotion</a>',
        'mc_highlighted' => !empty( $coupon['highlighted_using'] ) ? 1 : 0,
    ];
}
$header_data = [ 'Promotion', 'User Email', 'User Name', 'Promotion Link' ];

if( !$is_ajax ) {
    MC_Render::templatePart( 'search/mc-search', 'panel' );
}
MC_Table_Flex::renderTableFlex( $coupon_prepared_data, $header_data, '', $pagination_data );