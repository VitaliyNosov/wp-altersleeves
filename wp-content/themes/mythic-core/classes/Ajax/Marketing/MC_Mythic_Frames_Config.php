<?php

namespace Mythic_Core\Ajax\Marketing;

use MC_Alter;
use MC_Mythic_Frames_Functions;
use Mythic_Core\Abstracts\MC_Ajax;

/**
 * Class MC_Mythic_Frames_Config
 *
 * @package Mythic_Core\Ajax\Marketing
 */
class MC_Mythic_Frames_Config extends MC_Ajax {
    
    /**
     * Handles POST request
     */
    public function execute() {
        $teams            = MC_Mythic_Frames_Functions::sorted_teams();
        $team_id          = $_POST['team'];
        $response['team'] = $team_id;
        
        foreach( $_POST as $key => $data ) {
            $key = strtolower( $key );
            if( $key == 'commander_1' || $key == 'commander_2' || $key == 'commander_3' ) {
                $data = str_replace( ' ', '', $data );
                $data = explode( ',', $data );
                if( empty( $data ) ) $data = [];
            }
            $teams[ $team_id ][ $key ] = $data;
            if( $key == 'commander_1' ) {
                $teams[ $team_id ]['Commander 1'] = $data;
            }
            if( $key == 'commander_2' ) {
                $teams[ $team_id ]['Commander 2'] = $data;
            }
            
            if( $key == 'commander_3' ) {
                $teams[ $team_id ]['Commander 3'] = $data;
            }
            
            switch( $key ) {
                case 'creature' :
                case 'non_creature' :
                case 'land' :
                    $teams[ $team_id ][ $key.'_data' ][] = (array) new MC_Alter( $data );
                    break;
                case 'commander_1' :
                case 'commander_2' :
                case 'commander_3' :
                    $commanders = [];
                    foreach( $data as $commander ) {
                        $commanders[] = (array) new MC_Alter( $commander );
                    }
                    $teams[ $team_id ][ $key.'_data' ] = $commanders;
                    break;
            }
        }
        
        $response['update_status'] = update_option( 'mc_mf_teams', $teams );
        wp_schedule_single_event( time(), 'mf_update_ks_image', [ $team_id ] );
        $response['posted']  = $_POST;
        $response['updated'] = $teams[ $_POST['team'] ];
        
        $response['stored'] = $teams[ $team_id ];
        
        $this->success( $response );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mythic-frames-update';
    }
    
}
