<?php

namespace Mythic\Functions\User;

use Mythic\Functions\Wordpress\MC2_Event_Functions;
use Mythic\Helpers\MC2_Vars;
use WP_User;

/**
 * Class MC2_User_Functions
 *
 * @package Mythic\Functions
 */
class MC2_User_Functions {

    const DEFAULT_AVATAR = SITE_URL.'/src/img/user/profile.png';
    public static $role_name = '';

    /**
     * MC2_User_Functions constructor.
     */
    public function __construct() {
        $this->add_actions();
        $this->add_crons();
    }

    public function add_actions() {
        // WP Hooks
        add_action( 'edit_user_profile_update', [ self::class, 'addCapabilitiesOnReg' ] );
        add_action( 'personal_options_update', [ self::class, 'addCapabilitiesOnReg' ] );
        add_action( 'user_register', [ self::class, 'addCapabilitiesOnReg' ] );

        // MC
        add_action( 'mc_generate_creator_tags', [ self::class, 'generate_creator_product_tags' ] );
        add_action( 'mc_sync_users', [ MC2_User_Functions::class, 'syncSites' ] );
        // Woocommerce
    }

    public function add_crons() {
        MC2_Event_Functions::recurring( 'mc_sync_users', 1623884400 );
        MC2_Event_Functions::recurring( 'mc_generate_creator_tags', 1623884400 );
    }

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
     * @param int $user_id
     *
     * @return string
     */
    public static function email( $user_id = 0 ) : string {
        if( empty( $user_id ) ) return '';
        $data = get_userdata( $user_id );
        if( empty( $data ) ) return '';

        return get_userdata( $user_id )->user_email;
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

        return MC2_WP::meta( $key, $user_id );
    }

    /**
     * @return string
     */
    public static function role() {
        $roles = self::roles();
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
     * @param int    $user_id
     *
     * @return bool
     */
    public static function can( $permission = '', $user_id = 0 ) : bool {
        if( empty( $permission ) ) return false;
        if( !empty( $user_id ) ) return user_can( $user_id, $permission );

        return current_user_can( $permission );
    }

    /**
     * @param int $user_id
     *
     * @return string
     */
    public static function displayName( $user_id = 0 ) : string {
        if( empty( $user_id ) ) {
            if( empty( wp_get_current_user() ) ) return '';
            $user_id = wp_get_current_user()->ID;
        }
        $data = get_userdata( $user_id );
        if( empty( $data ) ) return '';

        return get_userdata( $user_id )->display_name;
    }

    /**
     * @param int $user_id
     *
     * @return string
     */
    public static function fullName( $user_id = 0 ) : string {
        if( empty( $user_id ) ) return '';
        $data = get_userdata( $user_id );
        if( empty( $data ) ) return '';

        return $data->user_firstname.' '.$data->user_lastname;
    }

    /**
     * @param int $user_id
     *
     * @return bool
     */
    public static function authorCurrentObject( $user_id = 0 ) : bool {
        if( MC2_User_Functions::isAdmin() ) return true;
        if( !is_user_logged_in() ) return false;
        if( empty( $user_id ) ) $user_id = wp_get_current_user()->ID;
        $object = get_queried_object();
        if( empty( $object ) ) return false;
        $idObject = $object->ID;
        $idAuthor = MC2_WP::authorId( $idObject );
        if( $user_id == $idAuthor ) return true;

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
        return MC2_Wp_User::mcSearchUsers( $search_term, $limit, $offset, [ static::$role_name ] );
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
     * @return array
     */
    public static function localizeArgs() : array {
        $user_id = $_GET['affiliate_id'] ?? MC2_User_Functions::id();
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
        if( !MC2_Order_Functions::userHasPreviouslyPurchased() ) $defaults[] = 'orders';
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

    public static function syncSites() {
        if( !is_multisite() ) return;
        $blog_id = get_current_blog_id();
        if( $blog_id != 1 ) return;
        $users = [];
        $sites = get_sites( [ 'fields' => 'ids' ] );

        foreach( $sites as $site ) {
            $users = array_merge( $users, get_users( [
                                                         'blog_id' => $site,
                                                         'fields'  => 'ids',
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
            $capabilities_1 = MC2_WP::meta( 'wp_capabilities', $user_id, 'user' );
            $capabilities_2 = MC2_WP::meta( 'wp_2_capabilities', $user_id, 'user' );
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
        $args     = [ 'blog_id' => 2 ];
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

        $capabilities_1 = MC2_WP::meta( 'wp_capabilities', $user_id, 'user' );
        $capabilities_2 = MC2_WP::meta( 'wp_2_capabilities', $user_id, 'user' );

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
     * Update all products etc with their creator tags
     */
    public static function generate_creator_product_tags() {
        // first clean up logins and nicenames before locking those in!
        global $wpdb;
        $args  = [
            'fields'         => 'ids', 'search' => 'gmail',
            'search_columns' => [ 'user_login', 'user_nicename' ],
        ];
        $users = get_users( $args );
        foreach( $users as $user ) {
            if( is_numeric( $user ) ) $user = get_user_by( 'ID', $user );
            $continue = false;
            foreach( [ $user->user_login, $user->user_nicename ] as $field ) {
                if( strpos( $field, 'gmail' ) !== false ) {
                    $continue = true;
                    break;
                }
            }
            if( !$continue ) continue;
            $new_username = sanitize_user( $user->display_name );
            $table        = 'users';
            $data         = [ 'user_login' => $new_username, 'user_nicename' => $new_username ];
            $where        = [ 'ID' => $user->ID ];
            $wpdb->update( $table, $data, $where );
        }

        // Now lets create tags for users that have created things!
        $table_name = $wpdb->prefix.'posts';

        $prepared_statement = $wpdb->prepare( "SELECT DISTINCT post_author FROM {$table_name} WHERE  post_type = 'product' AND post_author NOT IN (0,1,2)" );
        $creators           = $wpdb->get_col( $prepared_statement );
        foreach( $creators as $creator_id ) {
            $creator = get_user_by( 'ID', $creator_id );
            if( empty( $creator ) ) continue;

            $name = 'Creator: '.$creator->user_nicename;

            $tag_id = term_exists( $name );
            if( empty( $tag_id ) ) {
                $tag = wp_insert_term(
                    $name,         // the term
                    'product_tag', // the taxonomy
                    [
                        'slug' => sanitize_title_with_dashes( $name ),
                    ]
                );
            }
        }
    }

    /**
     * @param int $idCreator
     *
     * @return string
     */
    public static function profile_url( $idCreator = 0 ) {
        if( empty( $idCreator ) ) return '';

        return '/alterist/'.get_the_author_meta( 'user_nicename', $idCreator );
    }



    /**
     * @return string
     */
    public static function get_social_icons() {
        $idCreator = self::getAlteristId();
        $socials   = [
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_twitch',
            'social_artstation',
            'social_youtube',
        ];
        $output    = '';
        foreach( $socials as $social ) {
            if( get_the_author_meta( $social, $idCreator ) == '' ) continue;
            $url = get_the_author_meta( $social, $idCreator );
            switch( $social ) {
                case 'social_facebook' :
                    $icon = '<i class="fab fa-facebook-f p-2" style="font-size:21px;"></i>';
                    break;
                case 'social_instagram' :
                    $icon = '<i class="fab fa-instagram p-2" style="font-size:21px;"></i>';
                    if( strpos( $url, 'https://www.instagram.com' ) === false ) $url = 'https://www.instagram.com/'.$url;
                    break;
                case 'social_twitter' :
                    $icon = '<i class="fab fa-twitter p-2" style="font-size:21px;"></i>';
                    if( strpos( $url, 'https://www.twitter.com' ) === false ) $url = 'https://www.twitter.com/'.$url;
                    break;
                case 'social_twitch' :
                    $icon = '<i class="fab fa-twitch p-2" style="font-size:21px;"></i>';
                    if( strpos( $url, 'https://www.twitch.tv' ) === false ) $url = 'https://www.twitch.tv/'.$url;
                    break;
                case 'social_artstation' :
                    $icon = '<i class="fab fa-artstation p-2" style="font-size:21px;"></i>';
                    if( strpos( $url, 'https://www.artstation.com' ) === false ) $url = 'https://www.artstation.com/'.$url;
                    break;
                case 'social_youtube' :
                    $icon = '<i class="fab fa-youtube p-2" style="font-size:21px;"></i>';
                    break;
                default :
                    return '';
            }
            $output .= '<a href="'.$url.'" target="_blank">'.$icon.'</a>';
        }
        $output = '<div class="social text-center py-3" >'.$output.'</div>';

        return $output;
    }

    /**
     * @return string
     */
    public static function getAlteristId() {
        if( is_author() ) {
            $author   = get_queried_object();
            $authorId = $author->ID;
        } else {
            $authorId = get_the_author_meta( 'ID' );
        }

        return $authorId;
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
        $search_term = trim( strtolower( MC2_Vars::alphanumericOnly( $search_term ) ) );

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
        $search_term = trim( strtolower( MC2_Vars::alphanumericOnly( $search_term ) ) );

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
    
    /**
     * @return array
     */
    public static function get_header_data() : array {
        $logged_in = is_user_logged_in();
        if( $logged_in ) {
            $user = wp_get_current_user();
            $name = $user->first_name.$user->last_name;
        }
        
        return [
            'logged in' => $logged_in,
            'name'      => $name ?? __('Guest', MC2_TEXT_DOMAIN )
        ];
    }



}