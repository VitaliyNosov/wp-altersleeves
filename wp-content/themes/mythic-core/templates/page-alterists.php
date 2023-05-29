<?php
/**
 * Template Name: Alterists
 */

use Mythic_Core\Functions\MC_Artist_Functions;

get_header(); ?>
    <div id="alterists" class="background-browsing">
        <div class="wing bg-blue"></div>
        <div class="container content bg-white-content">
            <?php
            if( have_posts() ) : while( have_posts() ) : the_post();
                echo '<h1 class="text-center">'.get_the_title().'</h1>';
                echo wpautop( get_the_content() );
                echo MC_Artist_Functions::displayAlteristsFromUsers();
            endwhile; endif;
            ?>
            <br>
        </div>
        <div class="wing bg-blue"></div>
    </div>
<?php

get_footer();
