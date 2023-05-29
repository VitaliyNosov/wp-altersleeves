<?php
/**
 * Template Name: Admin only
 */

if( !MC_User_Functions::isAdmin() && empty($_GET['access'])  ) MC_Redirects::home();
get_header(); ?>
    <div id="alterists" class="background-browsing">
        <div class="wing bg-blue"></div>
        <div class="container content bg-white-content">
            <?php
            if( have_posts() ) :
                while( have_posts() ) : the_post();
                    the_title( '<h1 class="text-center">', '</h1>' );
                    the_content();
                endwhile;
            endif;
            ?>
            <br>
        </div>
        <div class="wing bg-blue"></div>
    </div>
<?php

get_footer();
