<?php
/**
 * Template Name: Dashboard (All) - Universal page
 *
 */

use Mythic_Core\System\MC_Redirects;

if( !is_user_logged_in() ) MC_Redirects::home();

get_header(); ?>
    <div id="dashboard" class="dashboard">
        <div class="wing bg-white"></div>
        <div class="dashboard-inner container">
            <div class="row h-100">
                <?php
                MC_Render::templatePart( 'dashboard/nav', 'md' ); ?><?php
                MC_Render::templatePart( 'dashboard/content' ); ?>
            </div>
        </div>
        <div class="wing bg-white-content"></div>
    </div>
<?php
get_footer();
