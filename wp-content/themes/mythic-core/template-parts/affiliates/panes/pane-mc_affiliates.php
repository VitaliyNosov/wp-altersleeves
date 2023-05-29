<?php

use Mythic_Core\Display\MC_Tabs_With_Panes;

$tabs_info = [
    'all_affiliates'          => [
        'title'   => 'All affiliates',
        'content' => [ 'slug' => 'affiliates/parts/as-affiliates-control', 'name' => 'all' ],
        'active'  => 1,
    ],
    'add_new_affiliate'       => [
        'title'   => 'Add new affiliate',
        'content' => [ 'slug' => 'affiliates/parts/as-affiliates-control', 'name' => 'add-new' ],
        'active'  => 0,
    ],
    'edit_existing_affiliate' => [
        'title'   => 'Edit existing',
        'content' => [ 'slug' => 'affiliates/panes/pane', 'name' => 'edit_existing_affiliate' ],
        'active'  => 0,
    ],
];

if( !empty( $args['existing_user_data'] ) && empty( $args['existing_promotion'] ) ) {
    $tabs_info['all_affiliates']['active']          = 0;
    $tabs_info['edit_existing_affiliate']['active'] = 1;
}

MC_Tabs_With_Panes::mcTabsRender( $tabs_info, [ 'args' => $args ] );