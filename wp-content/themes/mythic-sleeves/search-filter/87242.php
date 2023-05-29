<?php

use Mythic_Core\Utils\MC_Pagination;

$userId = wp_get_current_user()->ID;
$args   = [
    'post_type'      => 'product',
    'author'         => $userId,
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'tax_query'      => [
        [
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => 'composite',
        ],
    ],
];
// Get pages
if( isset( $_GET['sf_paged'] ) ) {
    $currentPage   = $_GET['sf_paged'];
    $args['paged'] = $currentPage;
}
// Get Search
if( isset( $_GET['_sf_s'] ) ) {
    $args['s'] = $_GET['_sf_s'];
}
// Set Type
if( isset( $_GET['_sft_set_type'] ) ) {
    $args['tax_query'] = [
        'relation' => 'AND',
        [
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => 'composite',
        ],
        [
            'taxonomy' => 'set_type',
            'field'    => 'slug',
            'terms'    => $_GET['_sft_set_type'],
        ],
    ];
}
if( MC_User_Functions::isAdmin() ) unset( $args['author'] );
$query = new WP_Query( $args );

$currentPage = 1;
if( $query->have_posts() ) {
    MC_Pagination::searchAndFilter( $query, 12 );
    ?>
    <div class="row">
    <?php
    $output = '';
    while( $query->have_posts() ) {
        $query->the_post();
        $id           = get_the_ID();
        $previewImage = get_post_meta( get_the_ID(), 'mc_lo_res_combined_png', true );
        if( $previewImage == '' ) {
            continue;
        }
        $setName      = get_post_meta( $id, 'mc_set_name', true );
        $alteristName = get_the_author();
        $url          = get_the_permalink();
        $imageClasses = '';
        $cat          = wp_get_post_terms( $id, 'set_type' )[0]->slug;
        if( isset( $cat ) ) $imageClasses = 'setPreview__image--'.$cat;
        $catName = wp_get_post_terms( $id, 'set_type' )[0]->name;
        $col     = 3;
        if( $cat == 28 ) $col = 4;
        
        $output .= '<div class="col-sm-6 col-lg-4 setPreview">';
        $output .= '<div class="setPreview__type">';
        $output .= '<strong>'.$catName.'</strong>';
        $output .= '</div>';
        $output .= '<a href="'.$url.'"><div class="setPreview__image '.$imageClasses.'" style="background-image:url('.$previewImage.');"><img src="'.$previewImage.'"></div></a>';
        $output .= '<a href="/dashboard/submit-collection?collection_id='.$id.'"><button class="cas-button cas-button--blue">Edit Collection</button></a>';
        $output .= '</div>';
    }
    echo $output;
    echo '</div>';
    MC_Pagination::searchAndFilter( $query, 12 );
} else {
    echo "<p>It appears you don't have any sets yet try creating one <a href='/dashboard/alterist/create-set'>here</a></p>";
}