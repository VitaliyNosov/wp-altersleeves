<?php

use Mythic_Core\Display\MC_Tabs_With_Panes;

$tabs_info = [
    'all_promotions'          => [
        'title'   => 'All Promotions',
        'content' => [ 'slug' => 'affiliates/parts/as-affiliates-coupons-control', 'name' => 'all' ],
        'active'  => 1,
    ],
    'promotions_by_affiliate' => [
        'title'   => 'Promotions by affiliate',
        'content' => [ 'slug' => 'affiliates/panes/pane', 'name' => 'promotions_by_affiliate' ],
        'active'  => 0,
    ],
];

if( !empty( $args['existing_promotion'] ) ) {
    $tabs_info['all_promotions']['active']          = 0;
    $tabs_info['promotions_by_affiliate']['active'] = 1;
}

MC_Tabs_With_Panes::mcTabsRender( $tabs_info, [ 'args' => $args ] );