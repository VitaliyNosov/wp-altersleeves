<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>

    <?php
    echo(123);
    // https://support.google.com/tagmanager/answer/6103696?hl=en
    // https://developers.google.com/analytics/devguides/collection/gtagjs
    do_action( 'mc_head_open' );

    // https://css-tricks.com/using-relpreconnect-to-establish-network-connections-early-and-increase-performance/
    do_action( 'mc_head_preconnects' );

    // https://developer.mozilla.org/en-US/docs/Web/HTML/Preloading_content
    do_action( 'mc_head_preloads' );

    // https://www.w3.org/2005/10/howto-favicon
    do_action( 'mc_favicon' );

    ?>

    <title><?php do_action( 'mc_head_title' ); ?></title>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />

    <?php

    // https://ogp.me/ - Open Graphs and previews
    do_action( 'mc_open_graphs' );

    // https://www.facebook.com/business/help/952192354843755
    do_action( 'mc_facebook_pixel' );

    // https://developers.google.com/tag-manager/enhanced-ecommerce
    do_action( 'mc_google_ecommerce' );

    // https://developer.wordpress.org/reference/functions/wp_head/
    wp_head();

    ?>
</head>

<body <?php body_class() ?>>
<?php

do_action( 'mc_body_open' );

do_action( 'mc_cart_notice' );

do_action( 'mc_header' );
