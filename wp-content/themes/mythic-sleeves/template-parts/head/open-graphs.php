<?php

use Mythic_Core\System\MC_WP;

$title = get_bloginfo( 'name' ).' - '.get_bloginfo( 'description' );

if( !is_search() && !is_404() ) {
    $currentUrl             = MC_SITE;
    $siteName               = get_bloginfo( 'name' );
    $defaultSocialSquare    = AS_URI_IMG.'/generation/social-poster/sharing/square.jpg';
    $defaultSocialLetterbox = AS_URI_IMG.'/generation/social-poster/sharing/letterbox.jpg';
    $previewDesc            = get_bloginfo( 'description' );
    if( !is_404() ) {
        global $wp;
        $currentUrl = home_url( add_query_arg( [], $wp->request ) );
        $postType   = get_queried_object()->post_type;
        $postID     = get_queried_object()->ID;
        $getPost    = get_post( $postID );
        if( !is_author() && !is_search() ) {
            $previewDesc = $getPost->post_content != '' && $getPost->post_content == strip_tags( $getPost->post_content ) ? $getPost->post_content : $previewDesc;
        }
        $twitterPreviewImg  = MC_WP::meta( 'mc_social_square', $postID );
        $twitterPreviewImg  = strlen( $twitterPreviewImg ) > 0 && !is_404() ? $twitterPreviewImg : $defaultSocialSquare;
        $facebookPreviewImg = MC_WP::meta( 'mc_social_letterbox', $postID );
        $facebookPreviewImg = strlen( $facebookPreviewImg ) > 0 && !is_404() ? $facebookPreviewImg : $twitterPreviewImg;
        $facebookPreviewImg = strlen( $twitterPreviewImg ) > 0 && !is_404() ? $facebookPreviewImg : $defaultSocialLetterbox;
    }
    
    ?>
    <meta property="og:title" content="<?= $title ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= $currentUrl ?>" />
    <meta property="og:image" content="<?= $facebookPreviewImg ?>" />
    <meta property="og:site_name" content="<?= $siteName ?>" />
    <meta property="og:description" content="<?= $previewDesc ?>" />
    <meta name="twitter:card" content="summary_large_image">
    <meta name=”twitter:site” content="<?= $siteName ?>">
    <meta name=”twitter:title” content="<?= $title ?>">
    <meta name=”twitter:description” content="<?= $previewDesc ?>">
    <meta name=”twitter:image” content="<?= $twitterPreviewImg; ?>">
    <?php
}