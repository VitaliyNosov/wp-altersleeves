<?php

use Mythic_Core\Display\MC_Render;
use Mythic_Core\Display\MC_Tabs_With_Panes;


$tabs_info = [
    'add_new_promotion'       => [
        'title'   => 'Add new Promotion',
        'content' => [ 'slug' => 'affiliates/parts/as-affiliates-coupons-control', 'name' => 'add-new' ],
        'active'  => 1,
    ],
    'edit_existing_promotion' => [
        'title'   => 'Edit existing',
        'content' => [ 'slug' => 'affiliates/panes/pane', 'name' => 'edit_existing_promotion' ],
        'active'  => 0,
    ],
];

if( !empty( $args['existing_promotion']['promotionId'] ) ) {
    $tabs_info['add_new_promotion']['active']       = 0;
    $tabs_info['edit_existing_promotion']['active'] = 1;
}

if( MC_User_Functions::isAdmin() ) {
    MC_Render::templatePart( 'search', 'mc-search-autocomplete-affiliates', $args );
}
?>

<div class="mc-af-edit-existing-user-coupons <?php echo empty( $args['existing_user_data'] ) ? 'mc_hidden' : '' ?>">
    <?php MC_Tabs_With_Panes::mcTabsRender( $tabs_info, [ 'args' => $args ] ); ?>
</div>