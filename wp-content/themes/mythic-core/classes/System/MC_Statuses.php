<?php

namespace Mythic_Core\System;

/**
 * Class MC_Statuses
 *
 * @package Mythic_Core\System
 */
class MC_Statuses {
    
    /**
     * MC_Statuses constructor.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'registerStatuses' ], 999 );
    }
    
    public function registerStatuses() {
        foreach( self::statuses() as $status ) $this->register( $status );
    }
    
    /**
     * @return array[]
     */
    public static function statuses() : array {
        $statuses = [
            [
                'key'     => 'action',
                'label'   => 'Action Required',
                'public'  => true,
                'exclude' => false,
            ],
            [
                'key'     => 'verify',
                'label'   => 'Awaiting Approval',
                'public'  => true,
                'exclude' => false,
            ],
            [
                'key'     => 'internal_verify',
                'label'   => 'Internal Pending',
                'public'  => true,
                'exclude' => true,
            ],
            [
                'key'     => 'internal_action',
                'label'   => 'Action Required (Hidden)',
                'public'  => false,
                'exclude' => true,
            ],
            [
                'key'     => 'internal_approved',
                'label'   => 'Internal Approved',
                'public'  => true,
                'exclude' => true,
            ],
            [
                'key'     => 'influencer',
                'label'   => 'Influencer',
                'public'  => false,
                'exclude' => true,
            ],
            [
                'key'     => 'removed',
                'label'   => 'Removed',
                'public'  => false,
                'exclude' => true,
            ],
        ];
        return apply_filters( 'mc_statuses', $statuses );
    }
    
    /**
     * @param array $status
     *
     * @return bool
     */
    public function register( $status = [] ) : bool {
        $key     = $status['key'];
        $label   = $status['label'];
        $public  = $status['public'];
        $exclude = $status['exclude'];
        
        register_post_status( $key, [
            'label'                     => _x( $label, 'product' ),
            'public'                    => $public,
            'exclude_from_search'       => $exclude,
            'show_in_admin_all_list'    => $status['show_in_admin_all_list'] ?? true,
            'show_in_admin_status_list' => $status['show_in_admin_status_list'] ?? true,
            'post_type'                 => $status['post_type'] ?? 'product',
            'label_count'               => _n_noop( $label.' <span class="count">(%s)</span>', $label.' <span class="count">(%s)</span>' ),
        ] );
        
        return true;
    }
    
    public static function init() {
        new self();
    }
    
    /**
     * @return string[]
     */
    public static function keys() : array {
        $statuses = self::statuses();
        $keys     = [ 'publish', 'pending' ];
        foreach( $statuses as $key => $status ) {
            if( $status['key'] == 'removed' ) {
                unset( $statuses[ $key ] );
            } else if( isset( $status['key'] ) ) $keys[] = $status['key'];
        }
        
        return $keys;
    }
    
    public static function getStatusesSimple() {
        $statuses   = self::statuses();
        $statuses[] = [
            'key'   => 'publish',
            'label' => 'Approved',
        ];
        $statuses[] = [
            'key'   => 'pending',
            'label' => 'Pending',
        ];
        foreach( $statuses as $status ) {
            unset( $status['public'] );
            unset( $status['exclude'] );
        }
        
        return $statuses;
    }
    
    public static function quickEdit() {
        echo 'blort';
        $script = "<script>jQuery(document).ready(function(){jQuery('select[name=\"_status\"]')".self::options().";})</script>";
        echo $script;
    }
    
    /**
     * @return string
     */
    public static function options() : string {
        $statuses = self::statuses();
        $append   = '';
        foreach( $statuses as $status ) {
            $key    = $status['key'];
            $label  = $status['label'];
            $append .= ".append( '<option value=\"".$key."\">".$label."</option>')";
        }
        
        return $append;
    }
    
    public static function edit() {
        $script = "<script>jQuery(document).ready(function(){jQuery( 'select[name=\"post_status\"]' )".self::options().";})</script>";
        echo $script;
    }
    
}