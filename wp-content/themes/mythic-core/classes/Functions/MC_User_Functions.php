<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Display\MC_Render;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Users\MC_Wp_User;
use WP_Query;
use WP_User;

/**
 * Class MC_User_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_User_Functions {
    
    /**
     * MC_User_Functions constructor.
     */
    public function __construct() {
        add_action( 'edit_user_profile_update', [ self::class, 'addCapabilitiesOnReg' ] );
        add_action( 'personal_options_update', [ self::class, 'addCapabilitiesOnReg' ] );
        add_action( 'user_register', [ self::class, 'addCapabilitiesOnReg' ] );
    }
    
    const DEFAULT_AVATAR = MC_URI.'/src/img/user/profile.png';
    public static $role_name = '';
    
    public static function validateUserName( $valid, $username ) {
        $lower_username = strtolower( $username );
        if( stripos( $username, $lower_username ) !== false ) return true;
        
        return $valid;
    }
    
    /**
     * @param null $user
     *
     * @return bool
     */
    public static function isAdmin( $user = null ) : bool {
        $user_id = self::id( $user );
        return user_can( $user_id, 'administrator' );
    }
    
    /**
     * @param int $user
     *
     * @return bool
     */
    public static function isArtist( $user = null ) : bool {
        if( self::isAdmin() ) return true;
        $user_id = self::id( $user );
        if( user_can( $user_id, 'wp_alterist' ) ) return true;
        return user_can( $user_id, 'artist' );
    }
    
    /**
     * @param int $user
     *
     * @return bool
     */
    public static function isContentCreator( $user = null ) : bool {
        $user_id = is_numeric( $user ) ? $user : self::id( $user );
        
        return user_can( $user_id, 'content_creator' );
    }
    
    /**
     * @param null $user
     *
     * @return bool
     */
    public static function isMod( $user = null ) : bool {
        if( self::isAdmin() ) return true;
        $user_id = self::id( $user );
        return user_can( $user_id, 'moderator' );
    }
    
    /**
     * @param null $user
     *
     * @return bool
     */
    public static function isPublisher( $user = null ) : bool {
        if( self::isAdmin() ) return true;
        $user_id = self::id( $user );
        return user_can( $user_id, 'publisher' );
    }
    
    /**
     * @param null $user
     *
     * @return bool
     */
    public static function isMarketer( $user = null ) : bool {
        if( self::isAdmin() ) return true;
        $user_id = self::id( $user );
        return user_can( $user_id, 'publisher' );
    }
    
    /**
     * @param null $user
     *
     * @return bool
     */
    public static function isRetailer( $user = null ) {
        if( self::isAdmin() ) return true;
        if( !function_exists( 'WC' ) ) return false;
        $user_id = self::id( $user );
        return wc_user_has_role( $user_id, 'retailer' );
    }
    
    /**
     * @param null $user
     *
     * @return int
     */
    public static function id( $user = null ) : int {
        if( !empty( $user ) ) {
            switch( $user ) {
                case is_object( $user ) :
                    $user_id = $user->ID;
                    break;
                case is_numeric( $user );
                    $user_id = $user;
                    break;
                default :
                    $user_id = 0;
                    break;
            }
            if( !empty( $user_id ) ) return $user_id;
        }
        if( !is_user_logged_in() ) return 0;
        
        return wp_get_current_user()->ID;
    }
    
    /**
     * @param int $idUser
     *
     * @return string
     */
    public static function email( $idUser = 0 ) : string {
        if( empty( $idUser ) ) return '';
        $data = get_userdata( $idUser );
        if( empty( $data ) ) return '';
        
        return get_userdata( $idUser )->user_email;
    }
    
    /**
     * @param $user_id
     *
     * @return mixed|string
     */
    public static function avatar( $user_id ) : string {
        if( empty( $user_id ) ) return static::DEFAULT_AVATAR;
        $avatar = get_the_author_meta( 'profile_image', $user_id );
        $avatar = wp_get_attachment_image_src( $avatar, 'shop_catalog' );
        
        if( !empty( $avatar ) ) {
            $avatar = $avatar[0];
            
            return $avatar;
        } else {
            $avatar = wp_get_attachment_image_src( $avatar );
            if( !empty( $avatar ) ) {
                $avatar = $avatar[0];
                
                return $avatar;
            }
        }
        
        return static::DEFAULT_AVATAR;
    }
    
    /**
     * @param $user_id
     *
     * @return array
     */
    public static function charity( $user_id = 0 ) : array {
        $status = time() < 1638162982 ? true : false;
        if( !$status ) return [];
        $result           = [ 'status' => $status ];
        $result['name']   = get_user_meta( $user_id, 'mc_charity_name', true );
        $result['url']    = get_user_meta( $user_id, 'mc_charity_url', true );
        $result['image']  = get_user_meta( $user_id, 'mc_charity_image', true );
        $result['reason'] = get_user_meta( $user_id, 'mc_charity_reason', true );
        if( empty( $result['name'] ) ) $result['status'] = false;
        return $result;
    }
    
    /**
     * @param string $key
     * @param int    $user_id
     *
     * @return mixed
     */
    public static function meta( $key = '', $user_id = 0 ) {
        if( empty( $user_id ) && !is_user_logged_in() ) return [];
        if( empty( $user_id ) ) {
            $user_id = wp_get_current_user()->ID;
        }
        
        return MC_WP::meta( $key, $user_id );
    }
    
    /**
     * @return string
     */
    public static function role() {
        $roles = self::roles();
        if( in_array( 'writer', $roles ) ) return 'writer';
        if( in_array( 'content-creator', $roles ) ) return 'content-creator';
        if( in_array( 'alterist', $roles ) ) return 'alterist';
        
        return 'customer';
    }
    
    /**
     * @param int $user_id
     *
     * @return array
     */
    public static function roles( int $user_id = 0 ) : array {
        $user = new WP_User( $user_id );
        if( empty( $user->roles ) || !is_array( $user->roles ) ) return [];
        
        return $user->roles;
    }
    
    /**
     * @param string $permission
     * @param int    $idUser
     *
     * @return bool
     */
    public static function can( $permission = '', $idUser = 0 ) : bool {
        if( empty( $permission ) ) return false;
        if( !empty( $idUser ) ) return user_can( $idUser, $permission );
        
        return current_user_can( $permission );
    }
    
    /**
     * @param int $idUser
     *
     * @return string
     */
    public static function displayName( $idUser = 0 ) : string {
        if( empty( $idUser ) ) {
            if( empty( wp_get_current_user() ) ) return '';
            $idUser = wp_get_current_user()->ID;
        }
        $data = get_userdata( $idUser );
        if( empty( $data ) ) return '';
        
        return get_userdata( $idUser )->display_name;
    }
    
    /**
     * @param int $idUser
     *
     * @return string
     */
    public static function fullName( $idUser = 0 ) : string {
        if( empty( $idUser ) ) return '';
        $data = get_userdata( $idUser );
        if( empty( $data ) ) return '';
        
        return $data->user_firstname.' '.$data->user_lastname;
    }
    
    /**
     * @param int $idUser
     *
     * @return bool
     */
    public static function authorCurrentObject( $idUser = 0 ) : bool {
        if( MC_User_Functions::isAdmin() ) return true;
        if( !is_user_logged_in() ) return false;
        if( empty( $idUser ) ) $idUser = wp_get_current_user()->ID;
        $object = get_queried_object();
        if( empty( $object ) ) return false;
        $idObject = $object->ID;
        $idAuthor = MC_WP::authorId( $idObject );
        if( $idUser == $idAuthor ) return true;
        
        return false;
    }
    
    /**
     * @param     $search_term
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public static function userSearch( $search_term, $limit = 20, $offset = 0 ) {
        return MC_Wp_User::mcSearchUsers( $search_term, $limit, $offset, [ static::$role_name ] );
    }
    
    /**
     * @param $entry
     * @param $form
     */
    public static function discordUsername( $entry, $form ) {
        if( !is_user_logged_in() ) return;
        $idCreator       = wp_get_current_user()->ID;
        $discordUsername = rgar( $entry, '1' );
        $discordUsername = utf8_encode( $discordUsername );
        $discordUsername = str_replace( '#', '', $discordUsername );
        update_user_meta( $idCreator, 'discord_username', trim( $discordUsername ) );
        
        $approved = get_option( 'approved_discord_usernames' );
        if( empty( $approved ) ) $approved = [];
        
        if( in_array( $discordUsername, $approved ) ) {
            update_user_meta( $idCreator, 'permission_submit_alter', 1 );
            echo '<p>A moderator has already approved your Discord ID. Welcome aboard.</p>';
        }
    }
    
    public static function loginArgs( $args ) {
        $args['value_remember'] = true;
        $args['form_id']        = 'login-form';
        
        return $args;
    }
    
    /**
     * @param string $name
     *
     * @return string[]
     */
    public static function nametoFirstLast( $name = '' ) : array {
        $last_space = strrpos( $name, ' ' );
        $first_name = !empty( substr( $name, 0, $last_space ) ) ? trim( substr( $name, 0, $last_space ) ) : '';
        $last_name  = !empty( substr( $name, $last_space ) ) ? trim( substr( $name, $last_space ) ) : '';
        
        return [ 'first_name' => $first_name, 'last_name' => $last_name ];
    }
    
    /**
     * @return array
     */
    public static function localizeArgs() : array {
        $user_id = $_GET['affiliate_id'] ?? MC_User_Functions::id();
        $user    = is_user_logged_in() ? wp_get_current_user() : null;
        $args    = [
            'ajaxurl'    => admin_url( 'admin-ajax.php' ),
            'user'       => $user_id,
            'user_id'    => $user_id,
            'email'      => $user->user_email ?? '',
            'first_name' => $user->first_name ?? '',
            'last_name'  => $user->last_name ?? '',
        ];
        foreach( $_GET as $key => $value ) $args[ $key ] = $value;
        return $args;
    }
    
    /**
     * Renders the content before the login form
     */
    public static function renderLoginStart() {
        MC_Render::templatePart( 'user', 'login-start' );
    }
    
    public static function dashboardNav() {
        return [
            'orders' => [ 'order' => 5 ],
        ];
    }
    
    /**
     * @return string[]
     */
    public static function dashboardDefaultsToRemove() : array {
        $defaults = [ 'dashboard', 'downloads', 'payment-methods' ];
        if( !MC_Woo_Order_Functions::userHasPreviouslyPurchased() ) $defaults[] = 'orders';
        return $defaults;
    }
    
    public static function dashboardNavItems( $items ) {
        unset( $items['customer-logout'] );
        foreach( $items as $key => $item ) {
            if( in_array( $key, self::dashboardDefaultsToRemove() ) ) {
                unset( $items[ $key ] );
                continue;
            }
        }
        ksort( $items );
        
        $items['customer-logout'] = 'Logout';
        
        return $items;
    }
    
    public static function dashboardEndpoint( $url, $endpoint, $value, $permalink ) {
        return $url;
        if( $endpoint === 'anyuniquetext123' ) {
            // ok, here is the place for your custom URL, it could be external
            $url = site_url();
        }
        return $url;
    }
    
    public static function syncSites() {
        if( !is_multisite() ) return;
        $blog_id = get_current_blog_id();
        if( $blog_id != 1 ) return;
        $users = [];
        $sites = get_sites( [ 'fields' => 'ids' ] );
        
        foreach( $sites as $site ) {
            $users = array_merge( $users, get_users( [
                                                         'blog_id' => $site,
                                                         'fields'  => 'ids'
                                                     ] ) );
            $users = array_unique( $users );
        }
        
        foreach( $users as $user_id ) {
            foreach( $sites as $site_id ) {
                if( $site_id < 3 ) continue;
                $meta = get_user_meta( $user_id, 'wp_'.$site_id.'_capabilities', true );
                if( !empty( $meta ) ) continue;
                update_user_meta( $user_id, 'wp_'.$site_id.'_capabilities', 's:25:"a:1:{s:8:"customer";b:1;}";' );
            }
            $capabilities_1 = MC_WP::meta( 'wp_capabilities', $user_id, 'user' );
            $capabilities_2 = MC_WP::meta( 'wp_2_capabilities', $user_id, 'user' );
            if( empty( $capabilities_1 ) && empty( $capabilities_2 ) ) continue;
            if( empty( $capabilities_1 ) ) {
                update_user_meta( $user_id, 'wp_capabilities', $capabilities_2 );
            }
            if( empty( $capabilities_2 ) ) {
                update_user_meta( $user_id, 'wp_2_capabilities', $capabilities_1 );
            }
        }
    }
    
    /**
     * @return array
     */
    public static function getAllUsers() {
        $args     = [ 'blog_id' => 1 ];
        $as_users = get_users( $args );
        $args     = [ 'blog_id' => 1 ];
        $mg_users = get_users( $args );
        return array_merge( $as_users, $mg_users );
    }
    
    public static function addCapabilitiesOnReg( $user_id ) {
        $sites = get_sites( [ 'fields' => 'ids' ] );
        foreach( $sites as $site_id ) {
            if( $site_id < 3 ) continue;
            $meta = get_user_meta( $user_id, 'wp_'.$site_id.'_capabilities', true );
            if( !empty( $meta ) ) continue;
            update_user_meta( $user_id, 'wp_'.$site_id.'_capabilities', 's:25:"a:1:{s:8:"customer";b:1;}";' );
        }
        
        $capabilities_1 = MC_WP::meta( 'wp_capabilities', $user_id, 'user' );
        $capabilities_2 = MC_WP::meta( 'wp_2_capabilities', $user_id, 'user' );
        
        if( empty( $capabilities_1 ) && !empty( $capabilities_2 ) ) {
            update_user_meta( $user_id, 'wp_capabilities', $capabilities_2 );
        }
        if( empty( $capabilities_2 ) && !empty( $capabilities_1 ) ) {
            update_user_meta( $user_id, 'wp_2_capabilities', $capabilities_1 );
        }
        if( empty( $capabilities_2 ) && empty( $capabilities_1 ) ) {
            update_user_meta( $user_id, 'wp_2_capabilities', 's:25:"a:1:{s:8:"customer";b:1;}";' );
            update_user_meta( $user_id, 'wp_capabilities', 's:25:"a:1:{s:8:"customer";b:1;}";' );
        }
        
        $user    = get_user_by( 'ID', $user_id );
        $email   = $user->user_email;
        $email   = strtolower( $email );
        $email   = str_replace( '@', '_at_', $email );
        $details = get_blog_option( 6, $email );
        if( empty( $details ) ) return;
        update_user_meta( $user_id, 'business_details', $details );
        delete_blog_option( 6, $email );
    }
    
    /**
     * @param $entry
     * @param $form
     */
    public static function discordApproval( $entry, $form ) {
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
    
    /**
     * @return WP_User[]
     */
    public static function get_active_creators() : array {
        return get_users( [ 'has_published_posts' => true, 'role__not_in' => 'administrator' ] );
    }
    
    /**
     * @param array $data
     *
     * @return array
     */
    public static function update_user_charity( $data ) : array {
        $user_id = get_current_user_id();
        
        if( isset( $data['user_id'] ) && $data['user_id'] && current_user_can( 'administrator' ) ) {
            $user_id = $data['user_id'];
        }
        
        //update_user_meta( $user_id, 'mc_charity_status', $data['charity_status'] );
        update_user_meta( $user_id, 'mc_charity_name', $data['charity_name'] );
        update_user_meta( $user_id, 'mc_charity_url', $data['charity_url'] );
        update_user_meta( $user_id, 'mc_charity_image', $data['charity_image'] );
        update_user_meta( $user_id, 'mc_charity_reason', $data['charity_reason'] );
        
        return [
            'status' => 1,
        ];
    }
    
    /**
     * @param $meta_key
     * @param $meta_value
     * @param $sale_category
     *
     * @return array
     */
    public static function update_user_sale( $meta_key, $meta_value, $sale_category ) {
        $user_id = get_current_user_id();
        
        $args = [
            'post_type'   => 'product',
            'post_author' => $user_id,
        ];
        
        $query             = new WP_Query( $args );
        $user_products     = $query->posts;
        $user_products_ids = array_map( function( $user_product ) {
            return $user_product->ID;
        }, $user_products );
        
        foreach( $user_products_ids as $user_product_id ) {
            if( $meta_value ) {
                wp_set_object_terms( $user_product_id, $sale_category->term_id, 'product_cat' );
            } else {
                wp_remove_object_terms( $user_product_id, $sale_category->term_id, 'product_cat' );
            }
        }
        update_user_meta( $user_id, $meta_key, $meta_value );
        
        $result = [
            'status'  => 1,
            'value'   => get_user_meta( $user_id, $meta_key, true ),
            'message' => "User meta successfully updated"
        ];
        return $result;
    }
    
    /**
     * One time function /wp-admin/?set_global_sale_on=1
     *
     * @param $sale_category
     *
     * @return bool
     */
    public static function set_users_sale_on_as_default( $sale_category ) : bool {
        $args  = [
            'role' => 'customer',
        ];
        $users = get_users( $args );
        
        if( !count( $users ) ) {
            echo '<div>You have no customers to update.</div>';
            return false;
        }
        
        foreach( $users as $customer ) {
            update_user_meta( $customer->ID, 'sale_agreement', 1 );
        }
        
        echo '<div>Customers updated.</div>';
        return true;
    }
    
}