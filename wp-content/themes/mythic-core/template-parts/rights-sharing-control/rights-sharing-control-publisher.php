<?php

use Mythic_Core\Display\MC_Tabs_With_Panes;


$user_id = MC_User_Functions::isAdmin() && isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : $user_id ?? 0;

if( empty( $user_id ) ) return;

$tabs_info = [
    'not_accepted_shares'  => [
        'title'   => 'Outstanding Offers',
        'content' => [ 'slug' => 'rights-sharing-control/parts/publisher', 'name' => 'not_accepted_shares' ],
        'active'  => 1,
    ],
    'publisher_all_shares' => [
        'title'   => 'Existing Agreements',
        'content' => [ 'slug' => 'rights-sharing-control/parts/publisher', 'name' => 'all_shares' ],
        'active'  => 0,
    ],
]; ?>

    <input type="hidden" id="mc-prod-share-current-publisher-id" value="<?php echo $user_id ?>">

<?php
MC_Tabs_With_Panes::mcTabsRender( $tabs_info, [ 'user_id' => $user_id ] );
