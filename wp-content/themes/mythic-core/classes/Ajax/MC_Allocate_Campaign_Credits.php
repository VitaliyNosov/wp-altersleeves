<?php

namespace Mythic_Core\Ajax;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_Mythic_Frames_Functions;

class MC_Allocate_Campaign_Credits extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $response    = [ 'status' => 0 ];
        $backer_id   = $_POST['backer_id'];
        $backer_data = MC_Mythic_Frames_Functions::backerCreditData( $backer_id );
        if( empty( $backer_data ) ) $this->success( $response );
        
        $team_id      = $_POST['team_id'];
        $set          = $_POST['set'] ?? 0;
        $non_creature = $_POST['non-creature'] ?? 0;
        $creature     = $_POST['creature'] ?? 0;
        $land         = $_POST['land'] ?? 0;
        $super_land   = $_POST['super-land'] ?? 0;
        
        $spent_set = 0;
        if( !empty( $set ) ) {
            $backer_data['spent'][ $team_id ]['set'] = $set;
            $spent_set                               = MC_Mythic_Frames_Functions::addTeamCreditSpend( $team_id, 'set', $set, $backer_id );
            $response['spent_set']                   = $spent_set;
        }
        $spent_pack = 0;
        if( !empty( $non_creature ) ) {
            $backer_data['spent'][ $team_id ]['non-creature'] = $non_creature;
            $non_creature                                     = MC_Mythic_Frames_Functions::addTeamCreditSpend( $team_id, 'non-creature',
                                                                                                                $non_creature,
                                                                                                                $backer_id );
            if( !empty( $non_creature ) ) $spent_pack = $spent_pack + $non_creature;
        }
        if( !empty( $creature ) ) {
            $backer_data['spent'][ $team_id ]['creature'] = $creature;
            $creature                                     = MC_Mythic_Frames_Functions::addTeamCreditSpend( $team_id, 'creature', $creature,
                                                                                                            $backer_id );
            if( !empty( $creature ) ) $spent_pack = $spent_pack + $creature;
        }
        if( !empty( $land ) ) {
            $backer_data['spent'][ $team_id ]['land'] = $land;
            $land                                     = MC_Mythic_Frames_Functions::addTeamCreditSpend( $team_id, 'land', $land, $backer_id );
            if( !empty( $land ) ) $spent_pack = $spent_pack + $land;
        }
        $spent_land = 0;
        if( !empty( $super_land ) ) {
            $backer_data['spent'][ $team_id ]['super-land'] = $super_land;
            $spent_land                                     = MC_Mythic_Frames_Functions::addTeamCreditSpend( $team_id, 'super-land', $super_land,
                                                                                                              $backer_id );
        }
        
        $response['status'] = 1;
        $response['spent']  = [
            'set'  => $spent_set,
            'pack' => $spent_pack,
            'land' => $spent_land,
        ];
        
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'allocate-campaign-credits';
    }
    
}
