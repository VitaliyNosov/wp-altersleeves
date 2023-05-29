<?php

if( empty( $user ) || empty( $idUser ) || $idUser != 6541 ) return;
?>
<div class="py-3">
    <h3>Published by <?= $user->display_name ?></h3>
    <div class="row justify-content-start">
        <?php
        
        $args       = [
            'post_type'   => 'design',
            'post__in'    => [ 179256, 179252, 179249 ],
            'post_status' => 'publish',
        ];
        $alterQuery = new WP_Query( $args );
        if( $alterQuery->have_posts() ) : while( $alterQuery->have_posts() ) : $alterQuery->the_post();
            $idDesign = MC_Alter_Functions::design_alter( get_the_ID() );
            include( TP_ITEMS_ALTER_A );
        endwhile; endif;
        ?>
    </div>
</div>