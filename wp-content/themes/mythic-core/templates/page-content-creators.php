<?php
/**
 * Template Name: Creators - Content Creators
 */

use Mythic_Core\Objects\MC_User;

get_header(); ?>

    <div id="alterists" class="background-browsing">
        <div class="wing bg-blue"></div>
        <div class="container content bg-white-content">
            <?php
            if( have_posts() ) : while( have_posts() ) : the_post();
                echo '<h1 class="text-center my-3">Content Creators</h1>';
                $args     = [
                    'role'       => 'content_creator',
                    'number'     => -1,
                    'orderby' => 'display_name',
                    'order' => 'ASC',
                ];
                $creators = get_users( $args );
                
                $output = '<div class="alterists row">';
                foreach( $creators as $creator ) {
                    $idCreator = $creator->ID;
                    if( user_can( $idCreator, 'administrator' ) ) continue;
                    $name    = get_the_author_meta( 'display_name', $idCreator );
                    $image   = MC_User::avatar( $idCreator );
                    $charity = MC_User::charity( $idCreator );
                    if( empty( $image ) || preg_match( '/profile.png/', $image ) ) continue;
                    $output .= '<div class="col-6 col-sm-4 col-md-3 alterist__item">';
                    $output .= '<a href="'.'/content-creator/'.get_the_author_meta( 'user_nicename', $idCreator ).'">';
                    $output .= '<div class="alterist__image" style="background-image:url('.$image.');background-position:center;"></div>';
                    $output .= '<div class="alterist__name">'.$name.'</div>';
                    if( !empty($charity['status']) && !empty( $charity['name'] ) ) {
                        $output .= '<div class="supporting_charity">Supporting '.$charity['name'].'</div>';
                        $output .= '<img src="'.get_template_directory_uri().'/src/img/gift.png">';
                    }
                    $output .= '</a>';
                    $output .= '</div>';
                }
                $output .= '</div>';
                echo $output;
            endwhile; endif;
            ?>
            <br>
        </div>
        <div class="wing bg-blue"></div>
    </div>
<?php

get_footer();
