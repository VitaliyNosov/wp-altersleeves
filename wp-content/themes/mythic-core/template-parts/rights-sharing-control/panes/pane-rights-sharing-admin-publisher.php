<?php

use Mythic_Core\Display\MC_Render;

$current_user = wp_get_current_user();
$args         = [
    'classes'            => 'mc-affiliates-search',
    'label'              => 'Search for publisher',
    'existing_user_data' => [ 'displayName' => !empty( $current_user->display_name ) ? $current_user->display_name : '' ],
];
MC_Render::templatePart( 'search', 'mc-search-autocomplete-affiliates', $args ); ?>
<div class="mc-prod-share-publisher-loader">
    <?php MC_Render::templatePart( 'loading', 'loader-animation' ); ?>
</div>
<div class="mc-prod-shares-publisher">
    <?php MC_Render::templatePart( 'rights-sharing-control', 'rights-sharing-control-publisher', [ 'user_id' => $user_id ] ); ?>
</div>