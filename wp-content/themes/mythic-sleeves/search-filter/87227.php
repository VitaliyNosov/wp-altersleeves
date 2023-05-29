<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Utils\MC_Pagination;

$args = [
    'post_type'      => 'product',
    'post_status'    => [ 'publish' ],
    'posts_per_page' => 8,
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
$args['author__not_in'] = [ 1341, 1, 3118 ];
$query                  = new WP_Query( $args );

$currentPage = 1;
if( $query->have_posts() ) {
    MC_Pagination::searchAndFilter( $query, 8 );
    ?>
    <div class="row justify-content-start">
    <?php
    $output = '';
    while( $query->have_posts() ) {
        $query->the_post();
        $id = get_the_ID();
        
        $previewImage = MC_WP::meta( 'mc_lo_res_combined_png' );
        if( $previewImage == '' ) {
            wp_update_post( [
                                'ID'          => $id,
                                'post_status' => 'pending',
                            ] );
            continue;
        }
        $printing_id = MC_Alter_Functions::printing( $id );
        $printing    = new MC_Mtg_Printing( $printing_id );
        
        $setName      = $printing->set_name;
        $nameAlterist = get_the_author();
        $url          = get_the_permalink();
        $imageClasses = '';
        $cat          = wp_get_post_terms( $id, 'set_type' )[0]->slug;
        if( isset( $cat ) ) $imageClasses = 'setPreview__image--'.$cat;
        $catName = wp_get_post_terms( $id, 'set_type' )[0]->name;
        $col     = 3;
        //if ($cat == 28) $col = 4;
        
        $output .= '<div class="col-sm-6 col-lg-'.$col.' setPreview">';
        $output .= '<div class="setPreview__type">';
        $output .= '<strong>'.$catName.'</strong>';
        $output .= '</div>';
        $output .= '<a href="'.$url.'"><div class="setPreview__image '.$imageClasses.'" style="background-image:url('.$previewImage.');"><img src="'.$previewImage.'"></div></a>';
        $output .= '<div class="setPreview__credit">';
        $output .= '<p>by '.$nameAlterist.'</p>';
        $output .= '</div>';
        $output .= '</div>';
    }
    echo $output;
    echo '</div>';
    MC_Pagination::searchAndFilter( $query, 12 );
} else {
    echo "<p>Sorry, but your search didn't yield any results. Try another search?";
}
