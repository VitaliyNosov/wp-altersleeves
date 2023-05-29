<?php

namespace Mythic_Core\Ajax\Acceptance;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\System\MC_Access;
use Mythic_Core\System\MC_WP;

/**
 * Class ApproveDesign
 *
 * @package Mythic_Core\Ajax\Acceptance\Approval
 */
class MC_Reject_Alter extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'alter-reject';
    }
    
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
        $idAlter                   = $_POST['alter_id'] ?? '';
        $issueResolution           = $_POST['issue_resolution'] ?? '';
        $issueCopyright            = $_POST['issue_copyright'] ?? '';
        $issueCroppingTight        = $_POST['issue_cropping_tight'] ?? '';
        $issueCroppingInsufficient = $_POST['issue_cropping_insufficient'] ?? '';
        $issueOpacity              = $_POST['issue_opacity'] ?? '';
        $issueWhiteTransparent     = $_POST['issue_white_transparent'] ?? '';
        $message                   = !empty( $_POST['message'] ) ? stripslashes( $_POST['message'] ) : '';
        
        $reasons = '';
        if( empty( $issueResolution ) ) {
            wp_set_post_terms( $idAlter, 3022, 'alter_status', true );
            $reasons .= '<li>Incorrect file dimensions</li>';
        }
        
        if( empty( $issueCopyright ) ) {
            wp_set_post_terms( $idAlter, 3036, 'alter_status', true );
            $reasons .= '<li>Using copyrighted material</li>';
        }
        
        if( empty( $issueCroppingTight ) ) {
            wp_set_post_terms( $idAlter, 3026, 'alter_status', true );
            $reasons .= '<li>Cropping is too tight on some elements of the alter</li>';
        }
        
        if( empty( $issueCroppingInsufficient ) ) {
            wp_set_post_terms( $idAlter, 3023, 'alter_status', true );
            $reasons .= '<li>Cropping is insufficient (doesn\'t cover corners etc)</li>';
        }
        
        if( empty( $issueOpacity ) ) {
            wp_set_post_terms( $idAlter, 3039, 'alter_status', true );
            $reasons .= '<li>Low Opacity elements are present and printing as white</li>';
        }
        
        if( empty( $issueWhiteTransparent ) ) {
            wp_set_post_terms( $idAlter, 3024, 'alter_status', true );
            $reasons .= '<li>Pure white is printing as transparent</li>';
        }
        
        if( !empty( $message ) > 0 ) update_post_meta( $idAlter, 'mc_violation_text', $message );
        
        wp_update_post( [
                            'ID'          => $idAlter,
                            'post_status' => 'action',
                        ] );
        $idUser       = MC_WP::authorId( $idAlter );
        $creatorEmail = get_userdata( $idUser )->user_email;
        
        $content = '';
        if( !empty( $message ) ) $content .= '<h3>Message from admin</h3>'.wpautop( $message );
        if( !empty( $reasons ) ) $content .= '<h3>Reasons for rejection</h3><ul>'.$reasons.'</ul>';
        ob_start();
        include( DIR_THEME_TEMPLATE_PARTS.'/emails/plain-text/alter-flagged.php' );
        $emailContent = ob_get_clean();
        
        $path       = ABSPATH.'/files/alters/'.$idAlter.'/acceptance/1.jpg';
        $attachment = file_exists( $path ) ? $path : '';
        
        if( MC_Access::live() ) {
            wp_mail( $creatorEmail, 'Alter '.$idAlter.' requires further action', $emailContent, $attachment );
        }
        
        $idAdmin   = wp_get_current_user()->ID;
        $dataAdmin = get_userdata( $idAdmin );
        $nameAdmin = $dataAdmin->first_name;
        
        $message = "<p>Alter '$idAlter' was rejected by $nameAdmin ($idAdmin) for the following reasons</p><ul>$reasons</ul><p>$message</p>";
        /*
                $log = new MC_Log();
                $log->setIdAdmin($idAdmin);
                $log->setIdUser(MC_WP::authorId($idAlter));
                $log->setIdItem($idAlter);
                $log->setCategory(102);
                $log->setMessage($message);
                $log->create();
        */
        $this->success( [
                            'response' => 1,
                            'message'  => $message,
                            'design'   => $idAlter,
                        ] );
    }
    
    /**
     * @return bool
     */
    protected function is_public() : bool {
        return false;
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'as-acceptance-data';
    }
    
}
