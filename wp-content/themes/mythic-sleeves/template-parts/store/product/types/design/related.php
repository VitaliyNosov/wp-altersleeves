<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\System\MC_WP;

global $post;

$current         = get_queried_object()->ID;
$idCreator       = MC_WP::authorId( $current );

$nameAlterist    = get_the_author_meta( 'display_name', $idCreator );
$creatorUserName = get_the_author_meta( 'user_nicename', $idCreator );
$args            = [
    'author'         => $idCreator,
    'post_type'      => 'product',
    'orderby'        => 'rand',
    'posts_per_page' => 10,
    'post__not_in'   => MC_Product_Functions::snapboltIds(),
    'post_status'    => 'publish',
];
$alterQuery      = new WP_Query( $args );

if( empty( $alterQuery->found_posts ) ) return;
?>

<h2>More alters by <a href="/alterist/<?= $creatorUserName ?>"><?= $nameAlterist ?></a></h2>
<div class="as-related-alters row justify-content-start">
    <?php
    $altersExcluded = [ MC_Product_Functions::snapboltIds() ];
    if( !empty( $post->ID ) ) $altersExcluded[] = $post->ID;

    $count = 0;
    $alters_id_list = [];
    if( $alterQuery->have_posts() ) : while( $alterQuery->have_posts() ) : $alterQuery->the_post();
        $alters = MC_Alter_Functions::design_alters( get_the_ID() );
        if( empty( $alters ) ) continue;
        foreach( $alters as $idx => $alter_id ) {
            if( get_post_status( $alter_id ) == 'publish' ) continue;
            if ( sizeOf($alters) - 1 === $idx ) continue 2;
        }
        if ( $alter_id ) {
            $alter_id = strval($alter_id);
        }
        if ( !in_array($alter_id, $alters_id_list) ) {
            $alters_id_list[$count] = $alter_id;
            $idDesign = $alter_id;
            $count++;
            include( TP_ITEMS_ALTER_D );
        }
        if( $count == 6 ) break;
    endwhile; endif;
    ?>
</div>