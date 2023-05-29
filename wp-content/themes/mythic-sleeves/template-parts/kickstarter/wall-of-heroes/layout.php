<?php

use Mythic_Core\Shortcodes\Kickstarter\WallOfHeroes;

$imgHeader     = AS_URI_IMG.'/wall-of-heroes/header.jpg';
$imgBackground = AS_URI_IMG.'/wall-of-heroes/background.jpg';
$isBacker      = WallOfHeroes::backerValidity();

?>

<div class="cas-wall-of-heroes" style="background-image: url(<?= $imgBackground ?>);">
    <img src="<?= $imgHeader ?>" alt="The Alter Sleeves Wall of Heroes">
    <?php
    if( $isBacker ) {
        echo '<p class="py-3 text-center">Is your name missing from the wall and you would like it added? Send an email to support@mythicgaming.com</p>';
    } ?>

    <div class="wall-of-heroes-block p-3 p-md-4 text-center">
        <p>Alter Sleeves began as a dream but became a reality thanks to everyone here that believed in us. In April
            2019 we raised $62,059 <strong><a href="https://www.kickstarter.com/projects/altersleeves/alter-sleeves" target="_blank">on
                    Kickstarter</a></strong> which helped us buy our
            printer, set up offices and begin shipping before the end of the year. The response was overwhelming and we
            don't take for the granted the endless stream of support and positivity. Every person here is a hero: may
            their decks claim victory in every duel.</p>
    </div>
    <div class="mx-3 mx-md-4 cas-wall-of-heroes-separator"></div>
    <div class="wall-of-heroes-block p-3 p-md-4">
        <?php
        include( DIR_THEME_TEMPLATE_PARTS.'/kickstarter/wall-of-heroes/top-heroes.php' );
        ?>
    </div>
    <div class="wall-of-heroes-block p-3 p-md-4">
        <?php
        include( DIR_THEME_TEMPLATE_PARTS.'/kickstarter/wall-of-heroes/normal-heroes.php' );
        ?>
    </div>
</div>
