<?php

// https://ogp.me/
?>

<meta property="og:title" content="<?php do_action( 'mc_graph_title' ) ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php do_action( 'mc_current_url' ) ?>" />
<meta property="og:image" content="<?php do_action( 'mc_graph_letterbox_image' ) ?>" />
<meta property="og:site_name" content="<?= get_bloginfo( 'name' ) ?>" />
<meta property="og:description" content="<?php do_action( 'mc_graph_description' ) ?>" />
<meta name="twitter:card" content="summary_large_image">
<meta name=”twitter:site” content="<?= get_bloginfo( 'name' ) ?>">
<meta name=”twitter:title” content="<?php do_action( 'mc_graph_title' ) ?>">
<meta name=”twitter:description” content="<?php do_action( 'mc_graph_description' ) ?>">
<meta name=”twitter:image” content="<?php do_action( 'mc_graph_square_image' ) ?>">
