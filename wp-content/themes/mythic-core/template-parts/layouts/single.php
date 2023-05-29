<div class="container">
    <?php MC_Render::breadcrumbs() ?>
    <div class="d-flex">
        <?php get_sidebar(); ?>
        <div class="<?php post_class( 'page' ); ?>" style="flex:1">
            <?php MC_WP_Post_Functions::defaultLoop( true, false, true ); ?>
        </div>
    </div>
</div>