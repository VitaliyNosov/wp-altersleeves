<?php

namespace Mythic_Core\Users;

use MC_User;
use Mythic_Core\Functions\MC_Production_Functions;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Utils\MC_Vars;
use WP_User;

/**
 * Class MC_Wp_User
 *
 * @package Mythic_Core\Objects
 */
class MC_Wp_User {
    
    public $location = MC_Production_Functions::DEFAULT;
    public $meta = [];
    public $roles = [];
    public $user;
    
    /**
     * MC_Wp_User constructor.
     *
     * @param null $term
     */
    function __construct( $term = null ) {
        if( empty( $term ) ) {
            if( is_author() ) {
                $term = MC_WP::currentId();
            } else {
                if( is_user_logged_in() ) {
                    $user = wp_get_current_user();
                }
            }
        }
        if( !empty( $term ) ) {
            switch( $term ) {
                case MC_Vars::stringContains( $term, '@' ) :
                    $type = 'email';
                    break;
                case is_string( $term ) && !is_numeric( $term ) :
                    $type = 'login';
                    break;
                default :
                    $type = 'id';
                    break;
            }
            $user = get_user_by( $type, $term );
        }
        if( empty( $user ) ) return;
        $user_id = $user->ID;
        $this->setUser( $user );
        $meta = get_user_meta( $user_id );
        $this->setMeta( $meta );
        $this->setLocation( $meta['MC_Production_Functions'] ?? $this->location );
        $this->setRoles( MC_User::roles( $user_id ) );
    }
    
    /**
     * @return mixed
     */
    public function getUser() {
        return $this->user;
    }
    
    /**
     * @param mixed $user
     */
    public function setUser( $user ) {
        $this->user = $user;
    }
    
    /**
     * @return array
     */
    public function getMeta() : array {
        return $this->meta;
    }
    
    /**
     * @param array $meta
     */
    public function setMeta( array $meta ) {
        $this->meta = $meta;
    }
    
    /**
     * @return string
     */
    public function getLocation() : string {
        return $this->location;
    }
    
    /**
     * @param string $location
     */
    public function setLocation( string $location ) : void {
        $this->location = $location;
    }
    
    /**
     * @return array
     */
    public function getRoles() : array {
        return $this->roles;
    }
    
    /**
     * @param array $roles
     */
    public function setRoles( array $roles ) : void {
        $this->roles = $roles;
    }
    
    /**
     * @return string
     */
    public function getNiceName() : string {
        return $this->user->user_nicename;
    }
    
    /**
     * @return string
     */
    public function getEmail() : string {
        return $this->user->user_email;
    }
    
    /**
     * @return string
     */
    public function getDisplayName() : string {
        return $this->user->display_name;
    }
    
    /**
     * Registers new user
     *
     * @param $user_data
     *
     * @return array
     */
    public static function registerNewUser( $user_data ) {
        $result        = [ 'status' => 0, 'result' => '' ];
        $new_user_data = [
            'user_pass'  => $user_data['password'],
            'user_login' => static::sanitizeNewData( $user_data['username'] ),
            'user_email' => static::sanitizeNewData( $user_data['email'] ),
            'first_name' => static::sanitizeNewData( $user_data['firstName'] ),
            'last_name'  => static::sanitizeNewData( $user_data['lastName'] ),
            'role'       => $user_data['role'],
        ];
        
        $user_id = wp_insert_user( $new_user_data );
        if( is_wp_error( $user_id ) ) {
            $result['result'] = $user_id->get_error_message();
        } else {
            $result = [ 'status' => 1, 'result' => $user_id ];
        }
        
        return $result;
    }
    
    /**
     * Updates user data
     *
     * @param $user_data
     *
     * @return array
     */
    public static function updateUserData( $user_data ) {
        $result    = [ 'status' => 0, 'result' => '' ];
        $user_role = !empty( $user_data['role'] ) ? $user_data['role'] : 'subscriber';
        $user_id   = $user_data['userId'];
        
        $first_name  = static::sanitizeNewData( $user_data['firstName'] );
        $last_name   = static::sanitizeNewData( $user_data['lastName'] );
        $update_data = [
            'ID'           => $user_id,
            'user_email'   => static::sanitizeNewData( $user_data['email'] ),
            'first_name'   => $first_name,
            'last_name'    => static::sanitizeNewData( $last_name ),
            'role'         => $user_role,
            'display_name' => $first_name.' '.$last_name,
        ];
        
        $user_id = wp_update_user( $update_data );
        if( is_wp_error( $user_id ) ) {
            $result['result'] = $user_id->get_error_message();
        } else {
            $result = [ 'status' => 1 ];
        }
        
        if( !empty( $user_data['username'] ) ) {
            $username  = static::sanitizeNewData( $user_data['username'] );
            $user_data = get_user_by( 'login', $username );
            if( empty( $user_data ) ) {
                global $wpdb;
                $query = "UPDATE $wpdb->users SET user_login = '$username' WHERE ID = $user_id";
                if( empty( $wpdb->query( $query ) ) ) {
                    $result['result'] = 'An error has occurred while update username';
                    
                    return $result;
                }
            } else if( $user_data->ID != $user_id ) {
                $result['result'] = 'This username already used by another user';
                
                return $result;
            }
        }
        
        return $result;
    }
    
    /**
     * Search for users
     *
     * @param       $search_term
     * @param int   $limit
     * @param int   $offset
     * @param array $role
     *
     * @return array
     */
    public static function mcSearchUsers( $search_term, $limit = 20, $offset = 0, $role = [] ) {
        $search_term = trim( strtolower( MC_Vars::alphanumericOnly( $search_term ) ) );
        
        $args           = static::getStandardQueryArgs();
        $args['number'] = $limit;
        $args['offset'] = $offset;
        $args['search'] = "*".$search_term."*";
        
        if( !empty( $role ) ) {
            $args['role__in'] = $role;
        }
        
        $users = get_users( $args );
        
        if( empty( $users ) ) return [];
        
        foreach( $users as $user_key => $user ) {
            $details = [
                'id'    => $user->ID,
                'name'  => $user->display_name,
                'email' => $user->user_email,
            ];
            if( !empty( $roles ) ) {
                $pre = 'user';
                if( in_array( 'content-creator', $roles ) ) {
                    $pre = 'content-creator';
                } else if( in_array( 'alterist', $roles ) ) {
                    $pre = 'alterist';
                }
                
                $link = '/'.$pre.'/'.$user->user_nicename;
            } else {
                $link = '';
            }
            $details['link']      = $link;
            $results[ $user_key ] = $details;
        }
        ksort( $results );
        
        return array_values( $results );
    }
    
    /**
     * Returns users search count
     *
     * @param       $search_term
     * @param array $role
     *
     * @return int
     */
    public static function mcSearchUsersCount( $search_term, $role = [] ) {
        $search_term = trim( strtolower( MC_Vars::alphanumericOnly( $search_term ) ) );
        
        $args           = static::getStandardQueryArgs();
        $args['search'] = "*".$search_term."*";
        
        if( !empty( $role ) ) {
            $args['role__in'] = $role;
        }
        
        return count( get_users( $args ) );
    }
    
    /**
     * @return array
     */
    public static function getStandardQueryArgs() {
        return [
            'orderby' => 'display_name',
            'order'   => 'ASC',
            'fields'  => 'all',
        ];
    }
    
    /**
     * Returns user data by ID
     *
     * @param $user_id
     *
     * @return bool|WP_User
     */
    public static function getUserData( $user_id ) {
        return get_user_by( 'ID', $user_id );
    }
    
    /**
     * @param $data
     *
     * @return string
     */
    public static function sanitizeNewData( $data ) {
        return trim( wp_strip_all_tags( wp_unslash( $data ) ) );
    }
    
}