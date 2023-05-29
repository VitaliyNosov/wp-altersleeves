<?php

use Mythic_Core\Display\MC_Render;

$current_user = wp_get_current_user();
$args         = [
    'classes'            => 'mc-alterists-search',
    'label'              => 'Search for artist',
    'existing_user_data' => [ 'displayName' => !empty( $current_user->display_name ) ? $current_user->display_name : '' ],
];

MC_Render::templatePart( 'search', 'mc-search-autocomplete-affiliates', $args ); ?>
<div class="mc-prod-share-artist-loader">
    <?php MC_Render::templatePart( 'loading', 'loader-animation' ); ?>
</div>
<div class="mc-prod-shares-artist">
    <?php
    MC_Render::templatePart( 'rights-sharing-control', 'rights-sharing-control-artist', [ 'user_id' => $user_id ] );
    Mythic_Core\Ajax\Finance\MC_Finance_Get_Data_Admin::render_nonce(); ?>
</div>