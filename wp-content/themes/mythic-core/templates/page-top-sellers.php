<?php
/**
 * Template Name: Admin - Top Sellers
 */

use Mythic_Core\Objects\MC_User;

if( !MC_User::isAdmin() ) MC_Redirects::home();

$top_selling = \Mythic_Core\Objects\MC_Ranked_Sale::getForHome();

get_header(); ?>

    <div id="alterists" class="background-browsing">
        <div class="wing bg-blue"></div>
        <div class="container content bg-white-content">
            <h2>Top Sellers</h2>

            <div class="row">
                <?php
                
                $count = 1;
                foreach( $top_selling as $top_seller ) :
                    $product_id = $top_seller->product_id;
                    if( get_post_status( $product_id ) != 'publish' ) continue;
                    $sales = $top_seller->total;
                    $image = MC_Alter_Functions::getCombinedImage( $product_id );
                    if( empty( $image ) || empty( $sales ) ) continue;
                    $artist_id   = MC_WP::authorId( $product_id );
                    $artist_name = MC_WP::authorName( $product_id );
                    ?>
                    <div class="col-6 col-sm-3 col-md-2 py-3">
                        
                        <a href="<?= get_the_permalink( $product_id ) ?>" target="_blank">
                            <img src="<?= $image ?>">
                            <h3 class="mb-0">#<?= $count ?> - <?= $product_id ?></h3>
                            </a>
                        <strong>Sales: </strong> <?= $sales ?><br>
                        <small><a href="<?= MC_Artist_Functions::urlProfile( $artist_id ) ?>"><?= $artist_name ?></a></small>
                    </div>
                <?php
                
                $count++;
                endforeach; ?>
            </div>
            <br>
        </div>
        <div class="wing bg-blue"></div>
    </div>
<?php

get_footer();
