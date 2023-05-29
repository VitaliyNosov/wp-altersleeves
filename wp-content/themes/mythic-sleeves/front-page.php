<?php

get_header();

$sale           = get_option( 'sws_storewide_sale_enable', '' );
$banner_url     = !empty( $sale ) ? 'discount-banner-url' : 'regular-banner-url';
$banner_url     = get_option( $banner_url, '' );
$desktop_banner = !empty( $sale ) ? 'desktop-discount-banner' : 'desktop-regular-banner';
$mobile_banner  = !empty( $sale ) ? 'mobile-mobile-banner' : 'mobile-regular-banner';
$mobile_banner  = get_option( $mobile_banner );

if( !empty( get_transient( 'home_content' ) ) && !MC_User_Functions::isAdmin() ) {
    $output = get_transient( 'home_content' );
} else {
    ob_start(); ?>
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm   mt-3">
                <?php
                if( time() < 1638248400 ) : ?>
                    <img src="<?= AS_URI_IMG.'/store/banner/cyber-banner.jpg' ?>" class="desktop-banner">
                    <img src="<?= AS_URI_IMG.'/store/banner/cyber-banner.jpg' ?>" class="mobile-banner">
                <?php
                elseif( !empty( $desktop_banner = get_option( $desktop_banner, '' ) ) ): ?>
                    <?php if( !empty( $banner_url ) ): ?><a href="<?php echo get_option( 'discount-banner-url' ) ?>"><?php endif ?>
                    <img src="<?= $desktop_banner ?>" class="desktop-banner">
                    <img src="<?= !empty( $mobile_banner ) ? $mobile_banner : $desktop_banner ?>" class="mobile-banner">
                    <?php if( !empty( $banner_url ) ): ?></a><?php endif ?>
                <?php endif ?>
            </div>
            <div class="col-12 col-sm-auto text-center mt-3">
                <a href="https://www.altersleeves.com/gift-card"><img src="/wp-content/themes/mythic-sleeves/src/img/store/giftcard.gif" style="max-width:250px"></a>
            </div>
        </div>
    </div>

    <div id="layout-browse" <?php post_class( 'py-3' ); ?>>
        <?php
        MC_Render::component( 'browse', 'bestselling' );
        MC_Render::component( 'browse', 'results' );
        ?>
    </div>
    <?php
    $output = ob_get_clean();
    set_transient( 'home_content', $output, 1 * HOUR_IN_SECONDS );
}
echo $output;

get_footer();