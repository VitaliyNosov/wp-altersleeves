<?php

use Mythic_Core\Utils\MC_Assets;

$site_name       = get_bloginfo( 'name' );
$logo_link       = get_site_url();
$logo_link_title = is_front_page() ? "You're already on the homepage!" : sprintf( __( "Return
 to the %s homepage", MC_TEXT_DOMAIN ), $site_name );
$logo_alt        = sprintf( __( "The %s logo", MC_TEXT_DOMAIN ), $site_name );
$logo_src        = MC_Assets::getImgUrl( 'logo/header.png' );

?>

    <div id="header-logo" class="logo col col-md-auto order-1  py-2">
        <a href="<?= $logo_link ?>" title="<?= $logo_link_title ?>">
            <img src="<?= $logo_src ?>" alt="<?= $logo_alt ?>">
        </a>
    </div>

<?php
