<?php

/**
 * File for add brand logo to top of login-form
 *
 * Add your own Icon Logo using the filter: 'mc_icon_logo'
 *
 * @param string $content   - OPTIONAL - Custom HTML for the site's login
 * @param string $icon_logo - REQUIRED - Custom image src for the site's Icon Logo
 *
 */

$icon_logo = apply_filters( 'mc_icon_logo_src', $icon_logo ?? '' );
$content   = apply_filters( 'mc_login_start', '' );

if( empty( $icon_logo ) && empty( $content ) ) return;

?>

<div class="login-start p-3">
    <?= $content ?>
    <?php if( !empty( $icon_logo ) ) : ?>
        <a href="<?= MC_SITE ?>" title="Return to the <?= MC_SITENAME ?> homepage">
            <img class="icon-logo mx-auto clearfix" src="<?= $icon_logo ?>" alt="Icon logo for <?= MC_SITENAME ?>">
        </a>
    <?php endif ?>
</div>