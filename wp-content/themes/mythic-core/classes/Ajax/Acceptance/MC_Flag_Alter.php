<?php

namespace Mythic_Core\Ajax\Acceptance;

use MC_Alter_Functions;
use MC_Log;
use MC_Mtg_Printing_Functions;
use MC_WP;
use Mythic_Core\Abstracts\MC_Ajax;

class MC_Flag_Alter extends MC_Ajax {
    
    /**
     * @return array|string[]
     */
    public function required_values() : array {
        return [ 'alter_id' ];
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $idAlter   = $_POST['alter_id'];
        $action    = $_POST['flag_action'];
        $idCreator = MC_WP::authorId( $idAlter );
        $idDesign  = !empty( MC_Alter_Functions::design( $idAlter ) ) ? MC_Alter_Functions::design( $idAlter ) : 0;
        $message   = isset( $_POST['message'] ) ? $_POST['message'] : '';
        
        ob_start(); ?>
        <hr><h2>Alter <a href="<?= get_the_permalink( $idAlter ); ?>"><?= $idAlter ?></a></h2>
        <p><img src="<?= MC_Alter_Functions::getCombinedImage( $idAlter ); ?>" style="max-width:100px;"></p>
        <?php if( !empty( $message ) ) : ?>
            <p><strong>Message from admin:</strong></p>
            <?php echo wpautop( $message ); ?><?php endif; ?>
        <h4>Reasons for flagging</h4>
        <ul>
        <?php
        $message = ob_get_clean();
        
        $pull       = $action == 'pull';
        $production = false;
        
        /* Alter Issues */
        if( !empty( $_REQUEST['incorrect_resolution'] ) ) {
            wp_set_post_terms( $idAlter, 3022, 'alter_status', true );
            $message    = $message.'<li>Incorrect file dimensions - should be 2980px by 4160px</li>';
            $pull       = true;
            $production = true;
        }
        
        if( !empty( $_REQUEST['clone_tool'] ) ) {
            wp_set_post_terms( $idAlter, 26307, 'alter_status', true );
            $message    = $message.'<li>Use of Clone Tool - seek advice from Discord mod to correct if needed</li>';
            $pull       = true;
            $production = true;
        }
        
        if( !empty( $_REQUEST['copyright'] ) ) {
            wp_set_post_terms( $idAlter, 3036, 'alter_status', true );
            $message    = $message.'<li class="text-danger">Uses copyrighted material</li>';
            $pull       = true;
            $production = true;
        }
        
        if( !empty( $_REQUEST['cropping_tight'] ) ) {
            wp_set_post_terms( $idAlter, 3026, 'alter_status', true );
            $message    = $message.'<li>Cropping too tight</li>';
            $pull       = true;
            $production = true;
        }
        
        if( !empty( $_REQUEST['cropping_insufficient'] ) ) {
            wp_set_post_terms( $idAlter, 3023, 'alter_status', true );
            $message    = $message.'<li>Cropping is insufficient - ie corners missed</li>';
            $pull       = true;
            $production = true;
        }
        
        if( !empty( $_REQUEST['white_blobs'] ) ) {
            wp_set_post_terms( $idAlter, 3039, 'alter_status', true );
            $message    = $message.'<li>Low opacity elements are showing as white blobs when printed. Contact us for image.</li>';
            $pull       = true;
            $production = true;
        }
        
        if( !empty( $_REQUEST['transparent'] ) ) {
            wp_set_post_terms( $idAlter, 3024, 'alter_status', true );
            $message    = $message.'<li>Pure white used: this prints as transaprent.</li>';
            $pull       = true;
            $production = true;
        }
        
        /* Design Issues */
        if( !empty( $_REQUEST['incorrect_design_type'] ) ) {
            wp_set_post_terms( $idAlter, 26203, 'alter_status', true );
            $message = $message.'<li>Not using the ideal design type. Please consider changing in the <a href="/dashboard/edit-design?design_id'.$idDesign.'">design editor</a>.</li>';
        }
        
        if( !empty( $_REQUEST['incorrect_alter_type'] ) ) {
            wp_set_post_terms( $idAlter, 26204, 'alter_status', true );
            $message = $message.'<li>Not using ideal crop type. Please consider re-assigning your alter <a href="/dashboard/edit-alter?alter_id'.$idAlter.'">here</a></li>';
        }
        
        if( !empty( $_REQUEST['incorrect_tags'] ) ) {
            wp_set_post_terms( $idAlter, 26205, 'alter_status', true );
            $message = $message.'<li>Inappropraite, excessive or irrelevant tags used for the linked design. Please update them here: <a href="/dashboard/edit-design?design_id'.$idDesign.'">design editor</a></li>';
        }
        
        if( !empty( $_REQUEST['incorrect_printing'] ) ) {
            wp_set_post_terms( $idAlter, 26206, 'alter_status', true );
            $message = $message.'<li>Incorrect printing or printings selected. Update printings here: <a href="/dashboard/edit-alter?alter_id'.$idAlter.'">here</a></li>';
            $pull    = true;
        }
        
        // @Todo group designs - 161536/161538
        
        $message .= '</ul>';
        if( $production ) $message .= '<p>Fix image and <a href="/dashboard/edit-alter?alter_id'.$idAlter.'">resubmit</a></p>';
        
        $currentMessageBody = get_user_meta( $idCreator, 'mc_alter_flag_email', true );
        if( empty( $currentMessageBody ) ) {
            wp_clear_scheduled_hook( 'mc_flagging_alter_email', [ $idCreator ] );
            wp_schedule_single_event( time() + 900, 'mc_flagging_alter_email', [ $idCreator ] );
            $currentMessageBody = '';
        }
        $currentMessageBody = $currentMessageBody.$message;
        update_user_meta( $idCreator, 'mc_alter_flag_email', $currentMessageBody );
        
        $idAdmin   = wp_get_current_user()->ID;
        $dataAdmin = get_userdata( $idAdmin );
        $nameAdmin = $dataAdmin->first_name;
        
        $flagged = $pull ? 'pulled' : 'flagged';
        
        $message = "<p>Alter '$idAlter' was $flagged by $nameAdmin ($idAdmin) for the following details</p>".wpautop( $message );
        
        $log = new MC_Log();
        $log->setIdAdmin( $idAdmin );
        $log->setIdUser( MC_WP::authorId( $idAlter ) );
        $log->setIdItem( $idAlter );
        $log->setCategory( 102 );
        $log->setMessage( $message );
        $log->create();
        
        if( $pull ) {
            wp_update_post( [
                                'ID'          => $idAlter,
                                'post_status' => 'action',
                            ] );
            wp_update_post( [
                                'ID'          => $idDesign,
                                'post_status' => 'pending',
                            ] );
        }
        update_post_meta( $idAlter, 'mc_flag_date', time() );
        update_post_meta( $idAlter, 'mc_flag_message', $message );
        
        $printings = MC_Alter_Functions::printings( $idAlter );
        $cards     = [];
        foreach( $printings as $printing ) {
            $cards[] = MC_Mtg_Printing_Functions::getCardId( $printing );
        }
        
        $this->success( [ 'alter_id' => $idAlter ] );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-alter-flag';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'as-acceptance-data';
    }
    
}
