<?php

use Mythic_Core\Display\MC_Tabs_With_Panes;

if( empty( $user_id ) ) return;

$tabs_info = [
    'admin_artist_shares'    => [
        'title'   => 'Licensing (Artist)',
        'content' => [ 'slug' => 'rights-sharing-control/panes/pane-rights-sharing-admin', 'name' => 'artists' ],
        'active'  => 1,
    ],
    'admin_publisher_shares' => [
        'title'   => 'Publishing',
        'content' => [ 'slug' => 'rights-sharing-control/panes/pane-rights-sharing-admin', 'name' => 'publisher' ],
        'active'  => 0,
    ],
];

MC_Tabs_With_Panes::mcTabsRender( $tabs_info, [ 'user_id' => $user_id ] );
