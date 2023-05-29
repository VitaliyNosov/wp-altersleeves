<?php

use Mythic_Core\Display\MC_Tabs_With_Panes;

if( empty( $user_id ) ) return;

$tabs_info = [
    'new_share'         => [
        'title'   => 'Give license',
        'content' => [ 'slug' => 'rights-sharing-control/parts/artist', 'name' => 'new_share' ],
        'active'  => 1,
    ],
    'artist_all_shares' => [
        'title'   => 'Existing Agreements',
        'content' => [ 'slug' => 'rights-sharing-control/parts/artist', 'name' => 'all_shares' ],
        'active'  => 0,
    ],
];

MC_Tabs_With_Panes::mcTabsRender( $tabs_info, [ 'user_id' => $user_id ] );
