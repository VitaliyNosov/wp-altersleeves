<?php

use Mythic_Core\Display\MC_Render;
use Mythic_Core\Display\MC_Table_Flex;
use Mythic_Core\Users\MC_Affiliates;

$is_ajax          = is_ajax();
$page             = get_query_var( 'paged', 1 );
$page             = !empty( $page ) ? $page : 1;
$per_page         = 20;
$offset           = ( $page - 1 ) * $per_page;
$search           = !empty( $search ) ? $search : '';
$affiliates_count = MC_Affiliates::searchAffiliatesCount( $search );
if( empty( $affiliates_count ) ) return;
$affiliates = MC_Affiliates::searchAffiliates( $search, $per_page, $offset );

$pagination_data = [];
if( $affiliates_count > $per_page ) {
    $pagination_data = [ $affiliates_count, $per_page, 'center', 1 ];
}

$page_body = '';
if( !$is_ajax ) {
    $uri_parts = explode( '?', $_SERVER['REQUEST_URI'], 2 );
    $page_body = home_url( $uri_parts[0] );
} else if( !empty( $_POST['currentPageUrl'] ) ) {
    $page_body = $_POST['currentPageUrl'];
}
$edit_link_body      = $page_body.'?affiliate_id=';
$coupons_link_body   = $page_body.'?coupons_affiliate_id=';
$dashboard_link_body = '/dashboard/creator-hub?affiliate_id=';
foreach( $affiliates as $affiliate_key => $affiliate ) {
    $affiliates[ $affiliate_key ]['name']           = '<a href="'.$edit_link_body.$affiliate['id'].'">'.$affiliates[ $affiliate_key ]['name'].'</a>';
    $affiliates[ $affiliate_key ]['dashboard_link'] = '<a href="'.$dashboard_link_body.$affiliate['id'].'">Dashboard</a>';
    $affiliates[ $affiliate_key ]['coupons_link']   = '<a href="'.$coupons_link_body.$affiliate['id'].'">Promotions</a>';
    unset( $affiliates[ $affiliate_key ]['id'] );
}
$header_data = [ 'Name', 'Email', '', 'Dashboard', 'Coupons Link' ];

if( !$is_ajax ) {
    MC_Render::templatePart( 'search/mc-search', 'panel' );
}
MC_Table_Flex::renderTableFlex( $affiliates, $header_data, '', $pagination_data );
