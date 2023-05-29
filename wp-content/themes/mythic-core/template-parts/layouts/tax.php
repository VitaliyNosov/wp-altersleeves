<?php

$object = get_queried_object();
$tax = get_taxonomy($object->taxonomy);
$tax_name = $tax->labels->singular_name;

if( have_posts() ) : while( have_posts() ) : the_post(); ?>
    <div class="container mb-5">
    <?php MC_Render::breadcrumbs() ?>
        <h1><?= $tax_name.': '.get_the_title() ?></h1>
        <div class="row">
        <?php get_sidebar(); ?>
            <div <?php post_class( 'page col-lg' ); ?> style="flex:1">
            <?php the_content(); ?>
        </div>
    </div>
</div>

<?php endwhile; endif;

