<?php
/**
 * Template Name: Mythic Frames
 */

if( !is_user_logged_in() && $_GET['cc_access'] != 'funded' && !MC_Mythic_Frames_Functions::isUserContributor() ) MC_Redirects::home();
$user_id = wp_get_current_user()->ID;

$teams = MC_Mythic_Frames_Functions::sorted_teams();

$printings = get_option( 'mc_rand_printings', [] );
if( empty( $printings ) ) $printings = MC_Mythic_Frames_Functions::rand_printings( true );

$creatures     = $printings['creatures'];
$noncreatures  = $printings['noncreatures'];
$land_plains   = $printings['plains'];
$land_mountain = $printings['mountain'];
$land_island   = $printings['island'];
$land_swamp    = $printings['swamp'];
$land_forest   = $printings['forest'];

get_header(); ?>
    <style>
    .gform_footer input {
        margin: 1rem 0;
    }
</style>
    <main id="designs" class="designs background-browsing">
        <div class="wing bg-white-wing"></div>
        <div class="container content bg-white-content p-3" style="background-color:#fff;">
            <h1>Mythic Frames</h1>
            <!-- Nav -->
            <nav>
                <div class="nav nav-tabs mythic-tabs" id="nav-tab" role="tablist">
                    <?php foreach( $teams as $team_id => $team ) :
                        $team_name = $team['design_name'].'<br>'.$team['alterist_name'].' - '.$team['content_creator_name'];
                        ?>
                        <a class="nav-item nav-link" id="nav-<?= $team_id ?>-tab" data-bs-toggle="tab" href="#nav-<?= $team_id ?>" role="tab"
                           aria-controls="nav-<?= $team_id ?>" aria-selected="true"><?= $team_name ?></a>
                    <?php endforeach; ?>
                </div>
            </nav>

            <div class="row my-3">
                <div class="col-sm-6">
                    <div class="border p-2">
                        <h3>Submit Playmat design</h3>
                        <?= do_shortcode( '[gravityform id=41 title=false description=false ajax=true]' ); ?>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="border p-2">
                    <h3>Submit Deckbox design</h3>
                        <?= do_shortcode( '[gravityform id=42 title=false description=false ajax=true]' ); ?>
                    </div>
                </div>

            </div>

            <div class="tab-content" id="nav-tabContent">
                <?php foreach( $teams as $team_id => $team ) {
                    include DIR_THEME_TEMPLATE_PARTS.'/tools/mythic-frames-creator-hub.php';
                }
                ?>
            </div>

        </div>

        <div class="wing bg-white-wing"></div>
    </main>
<?php

get_footer();
