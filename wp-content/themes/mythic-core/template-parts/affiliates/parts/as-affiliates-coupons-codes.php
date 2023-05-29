<?php

use Mythic_Core\Display\MC_Render;
use Mythic_Core\Display\MC_Table_Flex;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

if( empty( $promotion_id ) || empty( MC_Affiliate_Coupon::checkIfPromotionExistById( $promotion_id ) ) ) return;

$promotion_redirect_link = MC_Affiliate_Coupon::getAffiliatePromotionRedirectLinkById( $promotion_id );

$page                  = get_query_var( 'paged', 1 );
$page                  = !empty( $page ) ? $page : 1;
$per_page              = 1000;
$offset                = ( $page - 1 ) * $per_page;
$search                = !empty( $search ) ? $search : '';
$promotion_codes_count = MC_Affiliate_Coupon::getPromotionCodesForPromotionCount( $promotion_id, $search );
if( empty( $promotion_codes_count ) ) return;
$promotion_codes = MC_Affiliate_Coupon::getPromotionCodesForPromotion( $promotion_id, $search, $per_page, $offset );

$pagination_data = [];
if( $promotion_codes_count > $per_page ) {
    $pagination_data = [ $promotion_codes_count, $per_page, 'center', 1 ];
}

$prepared_codes_data = [];
if( !empty( $promotion_codes ) ) {
    $code_link_body = home_url( $promotion_redirect_link.'?af_code=' );
    foreach( $promotion_codes as $promotion_code ) {
        $prepared_codes_data[] = [
            //'code'         => $promotion_code['code'],
            'link'         => $code_link_body.$promotion_code['code'],
            'email'        => $promotion_code['email'],
            'already_used' => !empty( $promotion_code['already_used'] ) ? '<span>yes</span>' : '<span>no</span>',
            'mc_striked'   => !empty( $promotion_code['already_used'] ) ? 1 : 0,
        ];
    }
}

$header_data = [ 'Link', 'Email', 'Used' ];

if( !is_ajax() ) {
    MC_Render::templatePart( 'search/mc-search', 'panel' );
}
MC_Table_Flex::renderTableFlex( $prepared_codes_data, $header_data, '', $pagination_data );