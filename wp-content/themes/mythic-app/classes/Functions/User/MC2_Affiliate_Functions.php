<?php

namespace Mythic\Functions\User;

use Mythic\Abstracts\MC2_Class;
use Mythic\Functions\Finance\MC2_Transaction_Functions;
use Mythic\Functions\Display\MC2_Template_Parts;
use Mythic\Functions\Creator\MC2_Affiliate_Coupon_Functions;
use Mythic\Objects\Store\MC2_Coupon;
use Mythic\Helpers\MC2_Vars;
use Mythic\Functions\Finance\MC2_Wise_Functions;

/**
 * Class MC2_Affiliate_Functions
 *
 * @package Mythic\Users
 */
class MC2_Affiliate_Functions extends MC2_Class {

    function actions() {
        add_action( 'init', [ $this, 'partners' ] );
    }

    /**
     * @param int $affiliate_id
     *
     * @return float
     */
    public static function getAffiliateBalance( $affiliate_id = 0 ) : float {
        global $wpdb;
        $table_name = MC2_Transaction_Functions::$table_name;
        $query      = "SELECT SUM(value)";
        $query      .= " - COALESCE(( SELECT SUM(value) FROM mc_transactions WHERE type = 'withdrawal' AND user_id = $affiliate_id ), 0 )";
        $query      .= " + COALESCE(( SELECT SUM(value) FROM mc_transactions WHERE type = 'royalty' AND user_id = $affiliate_id ), 0 )";
        $query      .= " FROM $table_name WHERE type IN ('referral_fee', 'contracted_fee' ) AND user_id = $affiliate_id ";
        if( in_array( $affiliate_id, [ 5184, 6447, 3128, 2947 ] ) ) $query .= "and date > '2021-02-28'";

        $query = $wpdb->get_var( $query );

        return !empty( $query ) ? $query : 0;
    }

    public static function getContentCreators( $args = [] ) : array {
        $default_args = [
            'role'    => 'content_creator',
            'orderby' => 'user_nicename',
            'order'   => 'ASC',
            'offset'  => 0,
            'fields'  => 'ids',
        ];
        foreach( $args as $key => $arg ) {
            $default_args[ $key ] = $arg;
        }

        return get_users( $default_args );
    }


    public function partners() {
        if( !empty( $_GET['coupon'] ) ) setcookie( 'coupon', $_GET['coupon'], time() + ( 86400 * 30 ), "/" );

        $url        = $_SERVER['REQUEST_URI'];
        $breakables = [ 'dashboard', 'product', 'browse' ];
        foreach( $breakables as $breakable ) {
            if( strpos( $url, $breakable ) !== false ) return;
        }

        if( strpos( $url, '/' ) === 0 ) $url = substr( $url, 1 );
        $path         = preg_replace( '/\?.*/', '', $url );
        $path         = strtolower( $path );
        $affiliate_id = self::userCouponToId( $path );

        if( empty( $affiliate_id ) ) return;
        $cookies = json_decode( stripslashes( $_COOKIE['content_creator'] ?? '' ), true );
        $cookies = is_array( $cookies ) ? $cookies : [];
        if( !in_array( $affiliate_id, $cookies ) ) {
            $affiliations         = get_option( 'acquisition_'.$affiliate_id, [] );
            $month                = date( 'm' );
            $year                 = date( 'Y' );
            $key                  = $year.'-'.$month;
            $monthlyCount         = isset( $affiliations[ $key ] ) ? $affiliations[ $key ] : 0;
            $affiliations[ $key ] = $monthlyCount + 1;
            update_option( 'acquisition_'.$affiliate_id, $affiliations );
        }
        $cookies[ time() ] = $affiliate_id;
        setcookie( 'content_creator', json_encode( $cookies, JSON_UNESCAPED_SLASHES ), time() + ( 86400 * 30 ), "/" );
    }
    
    
    
    /**
     * @param int $user
     *
     * @return bool
     */
    public static function is( $user = null ) : bool {
        $user_id = self::id( $user );
        
        return user_can( $user_id, 'affiliate' ) || user_can( $user_id, 'content_creator' );
    }
    
    /**
     * @param int $idUser
     * @param int $startDate
     * @param int $endDate
     *
     * @return int
     * @throws Exception
     */
    public static function getAffiliateRedirects( $idUser = 0, $startDate = 0, $endDate = 0 ) {
        $path = self::userIdToPath( $idUser );
        if( empty( $idUser ) || $idUser == 99 ) return 0;
        $analytics = Analytics::initializeAnalytics();
        $profile   = Analytics::getFirstProfileId( $analytics );
        if( empty( $profile ) ) return 0;
        $results = Analytics::getPageViews( $path, $analytics, $profile, $startDate, $endDate );
        if( empty( $results ) || !is_countable( $results->getRows() ) ) return 0;
        if( count( $results->getRows() ) > 0 ) {
            // Get the profile name.
            $profileName = $results->getProfileInfo()->getProfileName();
            
            // Get the entry for the first entry in the first row.
            $rows = $results->getRows();
            
            return $rows[0][0];
        } else {
            return 0;
        }
    }
    
    /**
     * @param $idUser
     *
     * @return string
     */
    public static function userIdToPath( $idUser ) {
        return self::userIdToCoupon( $idUser );
    }
    
    /**
     * @param $idUser
     *
     * @return string
     */
    public static function userIdToCoupon( $idUser ) {
        return get_user_meta( $idUser, '_mc_affiliate_coupon', 1 );
    }
    
    /**
     * @param int $user_id
     *
     * @return string
     */
    public static function preferredCurrency( $user_id = 0 ) : string {
        if( empty( $user_id ) ) {
            if( !is_user_logged_in() ) return 'USD';
            $user_id = get_current_user_id();
        }
        
        $meta_data = get_user_meta( $user_id, '_mc_affiliate_monthly_fee_currency', 1 );
        
        return !empty( $meta_data ) ? $meta_data : 'USD';
    }
    
    /**
     * @param $idUser
     *
     * @return int
     */
    public static function getFee( $idUser ) : int {
        return intval( get_user_meta( $idUser, '_mc_affiliate_monthly_fee', 1 ) );
    }
    
    /**
     * TODO: maybe save mail sending data and affiliate author
     *
     * @param $affiliate_data
     *
     * @return array
     */
    public static function registerNewAffiliate( $affiliate_data ) {
        $result = [ 'status' => 0, 'message' => '' ];
        
        if( !MC2_Admin::is() ) {
            $result['message'] = 'You don\'t have access to register new affiliate';
            
            return $result;
        }
        
        $fields = static::getRegisterNewAffiliateFields();
        
        foreach( $fields as $field ) {
            if( !empty( $field['required'] ) && empty( $affiliate_data[ $field['name'] ] ) ) {
                $result['message'] .= 'You need to fill all required fields! ';
                break;
            }
        }
        
        if( empty( is_email( $affiliate_data['email'] ) ) ) {
            $result['message'] .= 'You need to provide correct email! ';
        }
        
        if( !empty( static::userCouponToId( $affiliate_data['affiliateUrl'] ) ) ) {
            $result['message'] .= 'This affiliate url already used! ';
        }
        
        if( !empty( $affiliate_data['sendEmail'] ) && empty( $affiliate_data['sendEmailText'] ) ) {
            $result['message'] .= 'You want to send email to user but didn\'t provide a text! ';
        }
        
        if( !empty( $result['message'] ) ) {
            return $result;
        }
        
        $affiliate_data['role'] = MC2_Content_Creator::PERMISSION_CONTENT_CREATOR;
        
        $register_result = MC2_Wp_User::registerNewUser( $affiliate_data );
        
        if( empty( $register_result['status'] ) ) {
            $result['message'] = $register_result['result'];
            
            return $result;
        }
        
        $new_user_id   = $register_result['result'];
        $new_user_data = get_user_by( 'ID', $new_user_id );
        if( !empty( $new_user_data->user_nicename ) ) {
            $wc_register_coupon_result = MC2_Coupon::createCoupon( [ 'promotionTitle' => $new_user_data->user_nicename ] );
            
            if( empty( $wc_register_coupon_result['status'] ) ) {
                $result['message'] = $wc_register_coupon_result['result'];
                
                return $result;
            } else {
                // commented because we don't need to save coupon id for now
                // update_user_meta( $new_user_id, '_mc_affiliate_coupon_id', $wc_register_coupon_result['result'] );
                update_user_meta( $new_user_id, '_mc_affiliate_coupon', $new_user_data->user_nicename );
            }
        } else {
            $result['message'] = 'Something went wrong!';
            
            return $result;
        }
        
        $affiliateUrl = MC2_Vars::stringCleanForUrl( $affiliate_data['affiliateUrl'] );
        update_user_meta( $new_user_id, '_mc_affiliate_url', $affiliateUrl );
        update_user_meta( $new_user_id, '_mc_affiliate_monthly_fee', $affiliate_data['monthlyFee'] );
        update_user_meta( $new_user_id, '_mc_affiliate_monthly_fee_currency', $affiliate_data['monthlyFeeCurrency'] );
        update_user_meta( $new_user_id, '_mc_affiliate_date_of_comes_in', $affiliate_data['dateOfComesIn'] );
        
        if( !empty( $affiliate_data['sendEmail'] ) ) {
            $wp_get_current_user             = wp_get_current_user();
            $admin_name                      = !empty( $wp_get_current_user->first_name ) ? $wp_get_current_user->first_name : 'Admin';
            $affiliate_data['sendEmailText'] = str_replace( '{{password}}', $affiliate_data['password'], $affiliate_data['sendEmailText'] );
            $affiliate_data['sendEmailText'] = str_replace( '{{First Name Last Name}}', $affiliate_data['firstName'].' '.$affiliate_data['lastName'],
                                                            $affiliate_data['sendEmailText'] );
            $affiliate_data['sendEmailText'] = str_replace( '{{Admin First Name}}', $admin_name, $affiliate_data['sendEmailText'] );
            $affiliate_data['sendEmailText'] = str_replace( '{{username}}', $affiliate_data['username'], $affiliate_data['sendEmailText'] );
            $affiliate_data['sendEmailText'] = str_replace( '{{slug}}', $affiliate_data['affiliateUrl'], $affiliate_data['sendEmailText'] );
            wp_mail( $affiliate_data['email'], 'Registration Data', $affiliate_data['sendEmailText'] );
        }
        
        $result = [ 'status' => 1, 'new_user_id' => $new_user_id ];
        
        return $result;
    }
    
    /**
     * Returns fields for affiliate registration form
     *
     * @return array
     */
    public static function getRegisterNewAffiliateFields() {
        return [
            [
                'label'    => 'E-mail',
                'name'     => 'email',
                'type'     => 'email',
                'id_part'  => 'af-register-email',
                'required' => 1,
            ],
            [
                'label'    => 'Username',
                'name'     => 'username',
                'type'     => 'text',
                'id_part'  => 'af-register-username',
                'required' => 1,
            ],
            [
                'label'    => 'Affiliate Url',
                'name'     => 'affiliateUrl',
                'type'     => 'text',
                'id_part'  => 'af-register-affiliate-url',
                'required' => 1,
            ],
            [
                'label'    => 'First Name',
                'name'     => 'firstName',
                'type'     => 'text',
                'id_part'  => 'af-register-f-name',
                'required' => 1,
            ],
            [
                'label'    => 'Last Name',
                'name'     => 'lastName',
                'type'     => 'text',
                'id_part'  => 'af-register-l-name',
                'required' => 1,
            ],
            [
                'label'    => 'Password',
                'name'     => 'password',
                'type'     => 'password',
                'id_part'  => 'af-register-password',
                'required' => 1,
            ],
            [
                'label'   => 'Monthly Fee',
                'name'    => 'monthlyFee',
                'type'    => 'number',
                'id_part' => 'af-register-monthly-fee',
            ],
            [
                'label'   => 'Monthly Fee Currency',
                'name'    => 'monthlyFeeCurrency',
                'type'    => 'select',
                'id_part' => 'af-register-monthly-fee-currency',
                'options' => MC2_Wise::availableCurrencies(),
                'default' => 'USD',
            ],
            [
                'label'   => 'Contracted State date',
                'name'    => 'dateOfComesIn',
                'type'    => 'datepicker',
                'id_part' => 'af-register-date-come',
                'default' => date( 'Y-m-d' ),
            ],
            [
                'label'   => 'Send Email to new user',
                'name'    => 'sendEmail',
                'type'    => 'checkbox',
                'id_part' => 'af-register-send-email',
                'default' => 1,
            ],
            [
                'label'   => 'Email body (use {{password}} tag for insert a user password in text)',
                'name'    => 'sendEmailText',
                'type'    => 'editor',
                'id_part' => 'af-register-send-email-text',
                'default' => static::getEmailDefaultText(),
            ],
        ];
    }
    
    /**
     * Returns default text for affiliate registration email
     *
     * @return string
     */
    public static function getEmailDefaultText() {
        return MC2_Template_Parts::get( 'emails', 'registration-affiliate' );
    }
    
    /**
     * @param $coupon
     *
     * @return int|string
     */
    public static function userCouponToId( $coupon ) {
        if( empty( $coupon ) ) return 0;
        if( $coupon == 'pleasentkenobi' ) return 2947;
        if( $coupon == 'marchesa' ) return 2;
        
        return static::getAffiliateIdByNicenameCoupon( $coupon );
    }
    
    public static function getAffiliateIdByNicenameCoupon( $nicename ) {
        if( is_admin() ) return 0;
        global $wpdb;
        $query = "SELECT user_id FROM wp_usermeta WHERE meta_key = '_mc_affiliate_coupon' and meta_value = '{$nicename}'";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * TODO: maybe save mail sending data and affiliate author
     *
     * @param $affiliate_data
     *
     * @return array
     */
    public static function updateAffiliateData( $affiliate_data ) {
        $result = [ 'status' => 0, 'message' => '' ];
        
        if( empty( $affiliate_data['userId'] ) ) {
            $result['message'] = 'Something went wrong';
            
            return $result;
        }
        
        if( !MC2_Admin::is() && $affiliate_data['userId'] != get_current_user_id() ) {
            $result['message'] = 'You don\'t have access to update current affiliate';
            
            return $result;
        }
        
        $fields = static::getUpdateAffiliateFields();
        
        foreach( $fields as $field ) {
            if( !empty( $field['required'] ) && empty( $affiliate_data[ $field['name'] ] ) ) {
                $result['message'] .= 'You need to fill all required fields! ';
                break;
            }
        }
        
        if( empty( is_email( $affiliate_data['email'] ) ) ) {
            $result['message'] .= 'You need to provide correct email! ';
        }
        
        $userCouponToId  = static::userCouponToId( $affiliate_data['affiliateUrl'] );
        $current_user_id = $affiliate_data['userId'];
        
        if( !empty( $userCouponToId ) && $userCouponToId != $current_user_id ) {
            $result['message'] .= 'This affiliate url already used! ';
        }
        
        if( !empty( $result['message'] ) ) {
            return $result;
        }
        
        $affiliate_data['role'] = !empty( $affiliate_data['isAffiliate'] ) ? MC2_Content_Creator::PERMISSION_CONTENT_CREATOR : '';
        
        $update_result = MC2_Wp_User::updateUserData( $affiliate_data );
        
        if( empty( $update_result['status'] ) ) {
            $result['message'] = $update_result['result'];
            
            return $result;
        }
        
        $affiliateUrl = MC2_Vars::stringCleanForUrl( $affiliate_data['affiliateUrl'] );
        update_user_meta( $current_user_id, '_mc_affiliate_url', $affiliateUrl );
        update_user_meta( $current_user_id, '_mc_affiliate_monthly_fee', $affiliate_data['monthlyFee'] );
        update_user_meta( $current_user_id, '_mc_affiliate_monthly_fee_currency', $affiliate_data['monthlyFeeCurrency'] );
        update_user_meta( $current_user_id, '_mc_affiliate_date_of_comes_in', $affiliate_data['dateOfComesIn'] );
        
        $result = [ 'status' => 1 ];
        
        return $result;
    }
    
    /**
     * Returns fields for affiliate update form
     *
     * @return array
     */
    public static function getUpdateAffiliateFields() {
        return [
            [
                'label'    => 'E-mail',
                'name'     => 'email',
                'type'     => 'email',
                'id_part'  => 'af-edit-existing-email',
                'required' => 1,
            ],
            [
                'label'    => 'Username',
                'name'     => 'username',
                'type'     => 'text',
                'id_part'  => 'af-edit-existing-username',
                'required' => 1,
            ],
            [
                'label'   => 'Is affiliate',
                'name'    => 'isAffiliate',
                'type'    => 'checkbox',
                'id_part' => 'af-affiliate-role',
            ],
            [
                'label'    => 'Affiliate Slug',
                'name'     => 'affiliateUrl',
                'type'     => 'text',
                'id_part'  => 'af-edit-existing-affiliate-url',
                'required' => 1,
            ],
            [
                'label'    => 'First Name',
                'name'     => 'firstName',
                'type'     => 'text',
                'id_part'  => 'af-edit-existing-f-name',
                'required' => 1,
            ],
            [
                'label'    => 'Last Name',
                'name'     => 'lastName',
                'type'     => 'text',
                'id_part'  => 'af-edit-existing-l-name',
                'required' => 1,
            ],
            [
                'label'   => 'Monthly Fee',
                'name'    => 'monthlyFee',
                'type'    => 'number',
                'id_part' => 'af-edit-existing-monthly-fee',
            ],
            [
                'label'   => 'Monthly Fee Currency',
                'name'    => 'monthlyFeeCurrency',
                'type'    => 'select',
                'id_part' => 'af-edit-existing-monthly-fee-currency',
                'options' => MC2_Wise::availableCurrencies(),
                'default' => 'USD',
            ],
            [
                'label'    => 'Date of comes in on',
                'name'     => 'dateOfComesIn',
                'type'     => 'datepicker',
                'id_part'  => 'af-edit-existing-date-come',
                'required' => 1,
            ],
        ];
    }
    
    /**
     * Gets and prepares affiliate data
     *
     * @param $user_id
     *
     * @return array
     */
    public static function getAffiliateData( $user_id ) {
        $result    = [ 'status' => 0, 'message' => 'Something went wrong' ];
        $user_data = MC2_Wp_User::getUserData( $user_id );
        if( empty( $user_data ) ) return $result;
        
        $result['userData'] = [
            'userId'             => $user_id,
            'email'              => $user_data->user_email,
            'username'           => $user_data->user_login,
            'affiliateUrl'       => get_user_meta( $user_id, '_mc_affiliate_url', 1 ),
            'firstName'          => $user_data->first_name,
            'lastName'           => $user_data->last_name,
            'displayName'        => $user_data->display_name,
            'monthlyFee'         => get_user_meta( $user_id, '_mc_affiliate_monthly_fee', 1 ),
            'monthlyFeeCurrency' => get_user_meta( $user_id, '_mc_affiliate_monthly_fee_currency', 1 ),
            'dateOfComesIn'      => get_user_meta( $user_id, '_mc_affiliate_date_of_comes_in', 1 ),
        ];
        if( !empty( $user_data->roles ) && in_array( MC2_Content_Creator::PERMISSION_CONTENT_CREATOR, $user_data->roles ) ) {
            $result['userData']['isAffiliate'] = 1;
        }
        $result['status'] = 1;
        
        return $result;
    }
    
    /**
     * Search for affiliates
     *
     * @param     $search_term
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public static function searchAffiliates( $search_term, $limit = 20, $offset = 0 ) {
        return MC2_Wp_User::mcSearchUsers( $search_term, $limit, $offset, [ MC2_Content_Creator::PERMISSION_CONTENT_CREATOR ] );
    }
    
    /**
     * Returns affiliates search count
     *
     * @param     $search_term
     * @param int $limit
     * @param int $offset
     *
     * @return int
     */
    public static function searchAffiliatesCount( $search_term ) {
        return MC2_Wp_User::mcSearchUsersCount( $search_term, [ MC2_Content_Creator::PERMISSION_CONTENT_CREATOR ] );
    }
    
    /**
     * @return string
     */
    public static function getAffiliateRedirectFromCurrentUrl() : string {
        $url = $_SERVER['REQUEST_URI'];
        if( strpos( $url, '/' ) === 0 ) $url = substr( $url, 1 );
        $path              = preg_replace( '/\?.*/', '', $url );
        $path              = strtolower( $path );
        $affiliate_user_id = MC2_Affiliate_Coupon_Functions::getAffiliateUserIdFromUrl( $path );
        if( empty( $affiliate_user_id ) ) return '';
        $redirect = static::getAffiliateSlugById( $affiliate_user_id, $path );
        setcookie( 'mc_affiliate_id', $affiliate_user_id, time() + ( 86400 * 30 ), "/" );
        return $redirect;
    }
    
    /**
     * @param $nicename
     *
     * @return bool|mixed|string
     */
    public static function getAffiliateSlugByNicename( $nicename ) {
        if( empty( $nicename ) ) return false;
        if( empty( $user_id = static::getAffiliateIdByNicenameCoupon( $nicename ) ) ) return false;
        if( !MC2_Content_Creator::is( $user_id ) ) return false;
        
        return static::getAffiliateSlugById( $user_id, $nicename );
    }
    
    /**
     * @param        $user_id
     * @param string $nicename
     *
     * @return mixed|string
     */
    public static function getAffiliateSlugById( $user_id, $nicename = '' ) {
        $slug_in_meta = get_user_meta( $user_id, '_mc_affiliate_url', 1 );
        
        return !empty( $nicename ) && $slug_in_meta == $nicename ? '/content-creator/'.$slug_in_meta : $slug_in_meta;
    }
    
    /**
     * @return array
     */
    public static function getAllAffiliateCoupons() {
        global $wpdb;
        $query  = "SELECT meta_value FROM wp_usermeta WHERE meta_key = '_mc_affiliate_coupon'";
        $result = $wpdb->get_col( $query );
        
        return $result;
    }
    
    
    
}