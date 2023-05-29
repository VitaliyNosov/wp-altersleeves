<?php
/**
 * Template Name: Scan API
 */

function json_response( $response = [], $status = '', $message = '' ) {
    if( !empty($status) ) {
        $response['status'] = $status;
    }
    
    if( !empty($message) ) {
        $response['message'] = $message;
    }
    if( empty($response) ) {
        $response = [ 'status' => 'error', 'message' => 'Insufficient data provided' ];
    }
    echo json_encode($response);
    die();
}

extract($_GET);

if( empty($set) && !empty($set_code) ) $set = $set_code;
if( !empty($set) && empty($set_code) ) $set_code = $set;
if( empty($collector_number) ) json_response();

$response = [ 'status' => 'error', 'term_id' => 0, 'term_slug' => 0, 'framecode' => '' ];

json_response($response = [ 'status' => 'success', 'term_id' => 123, 'term_slug' => 'fr-bd', 'framecode' => '2015-crtr-f-0-f-0-fs-1-lyt-nrml-txt-1' ]);

$args = [
    'post_type'      => 'printing',
    'post_status'    => [ 'publish' ],
    'posts_per_page' => 1,
    'fields' => 'ids',
    'meta_query' => [
        [
            'key' => 'mc_set_code',
            'value' => $set_code
        ],
        [
            'key' => 'mc_collector_number',
            'value' => $collector_number
        ],
    ]
];
$printings    = get_posts( $args );
if( empty($printings) ) json_response($response, 'error', 'No printings found');

$printing = new \Mythic_Core\Objects\MC_Mtg_Printing($printings[0]);

$response['term_id'] = $term_id = $printing->framecode_id;
$response['term_slug'] = $printing->framecode;
$response['framecode' ] = $framecode = 'v1-'.$term_id;

if( empty($scryfall_id = $printing->scryfall_id)) json_response($response, 'error', 'Scryfall ID not found for advanced framecode');

$scryfall_printing = \Mythic_Core\Utils\MC_Scryfall::get_card_by_scryfall_id($scryfall_id);
$response['framecode'] = $scryfall_printing ?? $framecode;

json_response($response, 'Success');

