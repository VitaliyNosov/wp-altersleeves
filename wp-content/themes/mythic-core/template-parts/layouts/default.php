<div <?php post_class() ?>>

    <div class="<?php post_class( 'row' ); ?>">
        <section id="content" class="content col">
            <?php if( have_posts() ) {
                while( have_posts() ) {
                    the_post();

                    $id = get_the_ID();
                    the_title( '<h1>', '</h1>' );
                    if( get_post_type() == 'post' ) the_date( "d-m-Y", '<p class="date">', '</p>' );
                    the_content();
                }
            } ?>
        </section>
        <?php get_sidebar(); ?>
    </div>

</div>
