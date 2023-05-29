<?php

use Mythic_Core\Users\MC_Affiliates;

$alterist   = get_queried_object();
$alteristId = $alterist->ID;
$type       = MC_Affiliates::isContentCreator( $alteristId ) ? 'content-creator' : 'alterist';
$type       = MC_Affiliates::isContentCreator( $alteristId ) && MC_Affiliates::isArtist( $alteristId ) ? 'alterist' : $type;

get_header(); ?>
    <div id="<?= $type ?>" class="profile <?= $type ?>">
        <div class="wing bg-white-wing"></div>
        <div class="container bg-white-content">
            <?php MC_Render::templatePart( 'profile/'.$type.'/layout' ); ?>
        </div>
        <div class="wing bg-white-wing"></div>
    </div>
<?php
get_footer();
