<?php

namespace Mythic\Functions\Moderation;

use Mythic\Abstracts\MC2_Class;
use Mythic\Objects\System\MC2_Action;

class MC2_User_Approval_Functions extends MC2_Class {

    public function actions() {
        MC2_Action::new( 'gform_after_submission_36', [ $this, 'discordApproval' ], 10, 2 );
    }

    /**
     * @param $entry
     * @param $form
     */
    public function discord_approval( $entry, $form ) {
        $submitted       = $discordUsername = rgar( $entry, '1' );
        $discordUsername = utf8_encode( $discordUsername );
        $discordUsername = str_replace( ' #', '', $discordUsername );
        $discordUsername = str_replace( '#', '', $discordUsername );

        $approved = get_option( 'approved_discord_usernames' );
        if( empty( $approved ) ) $approved = [];
        $approved[] = $discordUsername;
        $approved   = array_unique( $approved );
        update_option( 'approved_discord_usernames', $approved );

        $creators = get_users( [
                                   'meta_key'    => 'discord_username',
                                   'meta_value'  => $discordUsername,
                                   'number'      => 1,
                                   'count_total' => false,
                               ] );
        if( !empty( $creators ) ) {
            $idCreator = $creators[0]->ID;
            update_user_meta( $idCreator, 'permission_submit_alter', 1 );
        }

        echo 'We have approved any alterists that have <strong>'.$submitted.'</strong> as their discord username';
    }

}