<?php

use Mythic_Core\Display\MC_Render;

if( MC_User_Functions::isAdmin() ) {
    $admin_id = get_current_user_id();
} else if( MC_User_Functions::isArtist() ) {
    $artist_id = get_current_user_id();
} else if( MC_User_Functions::isContentCreator() ) {
    $publisher_id = get_current_user_id();
}

if( empty( $admin_id ) && empty( $artist_id ) && empty( $publisher_id ) ) return; ?>

<div class="mc-rights-sharing-control-container">
    <?php if( !empty( $admin_id ) ) {
        MC_Render::templatePart( 'rights-sharing-control', 'rights-sharing-control-admin', [ 'user_id' => $admin_id ] );
    } else if( !empty( $artist_id ) ) {
        MC_Render::templatePart( 'rights-sharing-control', 'rights-sharing-control-artist', [ 'user_id' => $artist_id ] );
    } else {
        MC_Render::templatePart( 'rights-sharing-control', 'rights-sharing-control-publisher', [ 'user_id' => $publisher_id ] );
    }
    Mythic_Core\Ajax\ProductRightsSharing\MC_Product_Rights_Sharing_New::render_nonce(); ?>
</div>
