<?php


if( empty( $user_id ) ) return;

$page              = get_query_var( 'paged', 1 );
$page              = !empty( $page ) ? $page : 1;
$per_page          = 20;
$offset            = ( $page - 1 ) * $per_page;
$status            = !empty( $status ) ? $status : 0;
$publisher_id      = !empty( $publisher ) ? $publisher : 0;
$product_id        = !empty( $product_id ) ? $product_id : 0;
$prod_shares_count = MC_Licensing_Functions::getProductRightsSharingCount( $status, $user_id, $publisher_id, $product_id );
$pagination_data   = [];
$prod_shares_data  = [];
$data_for_filter   = [];

if( !empty( $prod_shares_count ) ) {
    $prod_shares = MC_Licensing_Functions::getProductRightsSharing( $status, $user_id, $publisher_id, $product_id, $per_page, $offset );

    if( $prod_shares_count > $per_page ) {
        $pagination_data = [ $prod_shares_count, $per_page, 'center', 1 ];
    }

    foreach( $prod_shares as $prod_share_key => $prod_share ) {
        $user               = get_user_by( 'ID', $prod_share['publisher_id'] );
        $user_name          = !empty( $user->display_name ) ? $user->display_name : '';
        $user_email         = !empty( $user->user_email ) ? ' ('.$user->user_email.')' : '';
        $prod_shares_data[] = [
            'product_id' => $prod_share['product_id'],
            'publisher'  => $user_name.$user_email,
            'status'     => MC_Licensing_Functions::prepareStatusLabel( $prod_share['status'] ),
            'actions'    => MC_Licensing_Functions::generateStatusActionForArtist( $prod_share['id'], $prod_share['status'] ),
        ];
        if( empty( $data_for_filter['product'][ $prod_share['product_id'] ] ) ) $data_for_filter['product'][ $prod_share['product_id'] ] = $prod_share['product_id'];
        if( empty( $data_for_filter['publisher'][ $prod_share['publisher_id'] ] ) ) $data_for_filter['publisher'][ $prod_share['publisher_id'] ] = $user_name.$user_email;
    }
    $data_for_filter['status'] = MC_Licensing_Functions::$status_labels;

    if( empty( $skip_filters_html ) ) {
        MC_Table_Flex::renderTableFlexFilters( $data_for_filter, 4, [ 'mc-existing-shares' ] );
    }
}

$header_data = [ 'Product ID', 'Publisher', 'Status', 'Actions' ];
MC_Table_Flex::renderTableFlex( $prod_shares_data, $header_data, 'mc-existing-shares', $pagination_data );