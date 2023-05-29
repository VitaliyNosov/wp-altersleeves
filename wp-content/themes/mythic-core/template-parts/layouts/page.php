<?php

if( have_posts() ) : while( have_posts() ) : the_post(); ?>
    <div class="container">
        <?php MC_Render::breadcrumbs() ?>
        <?php if( function_exists( 'is_cart' ) && !is_cart() ) the_title( '<h1>', '</h1>' ); ?>
        <div class="row">
            <?php get_sidebar(); ?>
            <div <?php post_class( 'col-lg' ); ?> style="flex:1">
                <?php the_content(); ?>
            </div>
            <?php if( function_exists('is_cart') && is_cart() ) MC_Render::component( 'browse', 'bestselling' ); ?>
        </div>
    </div>
<?php endwhile; endif;

