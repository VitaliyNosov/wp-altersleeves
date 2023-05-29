<?php

use Mythic_Core\Functions\MC_Product_Functions;

//@Todo resolve after live
return;
if( empty( $idAlterist ) ) return;

$args       =
    [
        'author'         => $idAlterist,
        'post_type'      => 'product',
        'orderby'        => 'rand',
        'posts_per_page' => 4,
        'post__not_in'   => MC_Product_Functions::snapboltIds(),
        'post_status'    => 'publish',
        'tax_query'      => [
            [
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'composite',
            ],
        ],
    ];
$alterQuery = new WP_Query( $args );
if( empty( $alterQuery->found_posts ) ) return;

?>
<div class="py-3">
    <h3>Collections by <?= $alterist->display_name ?></h3>
    <div class="row justify-content-start">
        <?php
        
        $args       =
            [
                'author'         => $idAlterist,
                'post_type'      => 'product',
                'orderby'        => 'rand',
                'posts_per_page' => 4,
                'post__not_in'   => MC_Product_Functions::snapboltIds(),
                'post_status'    => 'publish',
                'tax_query'      => [
                    [
                        'taxonomy' => 'product_type',
                        'field'    => 'slug',
                        'terms'    => 'composite',
                    ],
                ],
            ];
        $alterQuery = new WP_Query( $args );
        if( $alterQuery->have_posts() ) : while( $alterQuery->have_posts() ) : $alterQuery->the_post();
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
            
            $output = '<div class="col-6 col-sm-3 setPreview">';
            $output .= '<div class="setPreview__type">';
            $output .= '<strong>'.$catName.'</strong>';
            $output .= '</div>';
            $output .= '<a href="'.$url.'"><div class="d-inline-block setPreview__image '.$imageClasses.'" style="background-image:url('.$previewImage.');"><img src="'.$previewImage.'"></div></a><br>';
            $output .= '</div>';
            echo $output;
        endwhile; endif;
        ?>
    </div>
</div>