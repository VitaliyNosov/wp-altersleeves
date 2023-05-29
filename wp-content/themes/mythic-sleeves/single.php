<?php

get_header();

?>

    <div id="page" class="page">
        <div class="wing bg-white-wing"></div>
        <div class="container content bg-white-content">
            <?php
            if( have_posts() ) :
                while( have_posts() ) : the_post();
                    the_title( '<h1 class="text-center">', '</h1>' );
                    the_content();
                endwhile;
            endif;
            ?>
        </div>
        <div class="wing bg-white-wing"></div>
    </div>

<?php
get_footer();
