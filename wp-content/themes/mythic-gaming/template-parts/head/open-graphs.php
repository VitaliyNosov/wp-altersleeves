<?php

// https://ogp.me/

use Mythic_Gaming\System\MG_Content;

if( MG_Content::isMythicFrames() ) :

    ?>
    <meta property="og:title" content="Mythic Frames" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= get_permalink() ?>" />
    <meta property="og:site_name" content="Mythic Frames" />
    <meta property="og:description" content="The Mythic Frames kickstarter is live now!" />
    <meta name="twitter:card" content="summary_large_image">
    <meta name=”twitter:site” content="Mythic Frames">
    <meta name=”twitter:title” content="The Kickstarter is live now!">
    <meta name=”twitter:description” content="Support the Kickstarter to bring this project to life.">
<?php

else : ?>
    <meta property="og:title" content="Mythic Gaming" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= get_permalink() ?>" />
    <meta property="og:site_name" content="Mythic Gaming" />
    <meta property="og:description" content="Sign up to our newsletter for upcoming announcements. Be Legendary!" />
    <meta name=”twitter:site” content="Mythic Gaming">
    <meta name=”twitter:title” content="Be Legendary!">
    <meta name=”twitter:description” content="Sign up to our newsletter for upcoming announcements.">
<?php
endif;
