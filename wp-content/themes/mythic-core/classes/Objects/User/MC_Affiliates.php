<?php

namespace Mythic_Core\Users;

use Mythic_Core\Display\MC_Template_Parts;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;
use Mythic_Core\Objects\Store\MC_Coupon;
use Mythic_Core\Utils\MC_Vars;
use Mythic_Core\Utils\MC_Wise;

/**
 * Class MC_Affiliates
 *
 * @package Mythic_Core\Users
 */
class MC_Affiliates extends MC_User_Functions {
    /**
     * @param null $user
     *
     * @return bool
     */
    public static function is( $user = null ) : bool {
        $user_id = self::id( $user );
        
        return !empty(get_user_meta($user_id, '_mc_affiliate_is', true));
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
     * @param $idUser
     *
     * @return int
     */
    public static function getFee( $idUser ) : int {
        return intval( get_user_meta( $idUser, '_mc_affiliate_monthly_fee', 1 ) );
    }
    
    /**
     * @return string[]
     */
    public static function micCoupons() : array {
        return [
            // Month 1
            'bar8ztaq',
            'utu64g7q',
            'hynrzmaf',
            'f5q6577d',
            '7kfk3zm2',
            'nmnuwq5u',
            'anryfwae',
            'vc9xkpsg',
            'xmkgjpm3',
            'd9gtvhet',
            'vt478esy',
            '3uhkyb68',
            'qw4mzg6z',
            'sbfgq347',
            'h92ysyqs',
            'r22k5p6q',
            'euwqczws',
            'vuhvs7gj',
            
            //Month 2
            'wkdqflpb',
            'cfmycafg',
            'dislykwx',
            'zmpgddmi',
            'kwjqgqic',
            'bftpcytu',
            'lcyvmwpy',
            'jaghpuyz',
            'djoaxmnx',
            'dijaraxa',
            'jdktkajt',
            'kbxznszy',
            'kypdnond',
            'yrxxlzpe',
            'ilojgmvz',
            'isxltbqt',
            'wsqsdkal',
            'rhucaqrx',
            'fgswcxvb',
            'lyfxuwbf',
            
            // Month 3
            'egxuhj2r',
            '1gifkn4s',
            'zlanfjkj',
            'bhkmbd9m',
            'ydoahp2s',
            'ghptjcpv',
            'igqekzb5',
            '5yp4cvjw',
            'tymt7yxv',
            'lmgm4ash',
            '3xvg5zbq',
            'u4p0owzr',
            'jts0sduh',
            'nwu1g5uc',
            'p5fmv0rm',
            'ifzeevcw',
            '9ovyxqea',
            'zduq6fkh',
            '0ehbz7et',
            'upo3jrku',
        ];
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
        
        if( !MC_User_Functions::isAdmin() ) {
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
        
        $affiliate_data['role'] = $affiliate_data['affiliateUserRole'];
        
        $register_result = MC_Wp_User::registerNewUser( $affiliate_data );
        
        if( empty( $register_result['status'] ) ) {
            $result['message'] = $register_result['result'];
            
            return $result;
        }
        
        $new_user_id   = $register_result['result'];
        
        if(!empty($affiliate_data['isAffiliate'])){
            update_user_meta($new_user_id, '_mc_affiliate_is', 1);
            $affiliate_coupon_res = static::checkAndMayBeCreateAffiliateUserCoupon($new_user_id, $affiliate_data['username']);
            if( empty( $affiliate_coupon_res['status'] ) ) return $affiliate_coupon_res;
        }
        
        $affiliateUrl = MC_Vars::stringCleanForUrl( $affiliate_data['affiliateUrl'] );
        update_user_meta( $new_user_id, '_mc_affiliate_url', $affiliateUrl );
        update_user_meta( $new_user_id, '_mc_affiliate_monthly_fee', $affiliate_data['monthlyFee'] );
        update_user_meta( $new_user_id, '_mc_affiliate_monthly_fee_currency', $affiliate_data['monthlyFeeCurrency'] );
        update_user_meta( $new_user_id, '_mc_affiliate_date_of_comes_in', $affiliate_data['dateOfComesIn'] );
        update_user_meta( $new_user_id, '_mc_affiliate_is', 1 );
        
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
     * @param        $user_id
     * @param string $coupon_code
     *
     * @return array
     */
    public static function createAffiliateUserCouponByUserId( $user_id, $coupon_code = '' ) {
        $result = [ 'status' => 0, 'message' => '' ];
        
        if(empty($coupon_code)) {
            $new_user_data = get_user_by( 'ID', $user_id );
            if( empty( $new_user_data->user_nicename ) ) {
                $result['message'] = 'Something went wrong with user data!';
        
                return $result;
            }
            
            $coupon_code = $new_user_data->user_nicename;
        }
        
        $affiliate_coupon_res = static::createAffiliateUserCoupon( $user_id, $coupon_code );
        
        if( empty( $affiliate_coupon_res['status'] ) ) return $affiliate_coupon_res;
        
        return [ 'status' => 1 ];
    }
    
    /**
     * @param $user_id
     * @param $coupon_code
     *
     * @return array
     */
    public static function createAffiliateUserCoupon( $user_id, $coupon_code ) {
        $result = [ 'status' => 0, 'message' => '' ];
        
        if( !is_array( $coupon_code ) ) {
            $coupon_code_temp = $coupon_code;
            $coupon_code = [];
            $coupon_code['promotionTitle'] = $coupon_code_temp;
        }
    
        if(MC_User_Functions::isRetailer($user_id)) {
            $coupon_code['discountValue'] = 0.1;
            $coupon_code['freeUntrackedShipping'] = 1;
        }
        
        $wc_register_coupon_result = MC_Coupon::createCoupon( $coupon_code );
        
        if( empty( $wc_register_coupon_result['status'] ) ) {
            $result['message'] = $wc_register_coupon_result['result'];
            
            return $result;
        }
        // commented because we don't need to save coupon id for now
        // update_user_meta( $new_user_id, '_mc_affiliate_coupon_id', $wc_register_coupon_result['result'] );
        update_user_meta( $user_id, '_mc_affiliate_coupon', $coupon_code['promotionTitle'] );
        
        return [ 'status' => 1 ];
    }
    
    /**
     * @param        $user_id
     * @param string $user_nicename
     *
     * @return array
     */
    public static function checkAndMayBeCreateAffiliateUserCoupon($user_id, $user_nicename = ''){
        if(!empty(static::userIdToCoupon($user_id))) return [ 'status' => 1 ];
    
        return static::createAffiliateUserCouponByUserId($user_id, $user_nicename);
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
                'label'   => 'Is affiliate',
                'name'    => 'isAffiliate',
                'type'    => 'checkbox',
                'id_part' => 'af-register-is-affiliate',
            ],
            [
                'label'       => 'Affiliate User Role',
                'name'        => 'affiliateUserRole',
                'type'        => 'select',
                'id_part'     => 'af-register-affiliate-user-role',
                'options'     => static::getAllRolesForSelectField(),
                'permissions' => 'administrator'
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
                'options' => MC_Wise::availableCurrencies(),
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
        return MC_Template_Parts::get( 'emails', 'registration-affiliate' );
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
        
        return static::getAffiliateIdByCouponCode( $coupon );
    }
    
    public static function getAffiliateIdByCouponCode( $nicename ) {
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
        
        if( !MC_User_Functions::isAdmin() && $affiliate_data['userId'] != get_current_user_id() ) {
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
    
        $user_data = MC_Wp_User::getUserData( $affiliate_data['userId'] );
        if(empty($user_data)){
            $result['message'] = 'Something went wrong with existing user data!';
    
            return $result;
        }
        
        if(
            empty($user_data->roles) || !in_array($affiliate_data['affiliateUserRole'], $user_data->roles)
        ) $affiliate_data['role'] = $affiliate_data['affiliateUserRole'];
        
        if(empty($affiliate_data['isAffiliate'])){
            delete_user_meta($affiliate_data['userId'], '_mc_affiliate_is');
        } elseif(!static::is($affiliate_data['userId'])) {
            update_user_meta($affiliate_data['userId'], '_mc_affiliate_is', 1);
            $check_result = static::checkAndMayBeCreateAffiliateUserCoupon($affiliate_data['userId'], $user_data->user_nicename);
    
            if(empty($check_result['status'])) return $check_result;
        }
        
        $update_result = MC_Wp_User::updateUserData( $affiliate_data );
        
        if( empty( $update_result['status'] ) ) {
            $result['message'] = $update_result['result'];
            
            return $result;
        }
        
        $affiliateUrl = MC_Vars::stringCleanForUrl( $affiliate_data['affiliateUrl'] );
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
                'id_part' => 'af-edit-existing-is-affiliate',
            ],
            [
                'label'       => 'Affiliate coupon code',
                'name'        => 'affiliateCouponCode',
                'type'        => 'text',
                'id_part'     => 'af-edit-existing-affiliate-coupon-code',
                'permissions' => 'administrator',
                'disabled'    => true
            ],
            [
                'label'       => 'Affiliate User Role',
                'name'        => 'affiliateUserRole',
                'type'        => 'select',
                'id_part'     => 'af-edit-existing-affiliate-user-role',
                'options'     => static::getAllRolesForSelectField(),
                'permissions' => 'administrator'
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
                'options' => MC_Wise::availableCurrencies(),
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
     * @return array
     */
    public static function getAllRolesForSelectField(){
        $roles_data = [];
        $wp_roles = wp_roles();
        if(empty($wp_roles->roles)) return $roles_data;
        
        $excluded_roles_from_list = [
          'administrator'
        ];
        
        foreach($wp_roles->roles as $wp_role_key => $wp_role){
            if(in_array($wp_role_key, $excluded_roles_from_list)) continue;
            $roles_data[$wp_role_key] = $wp_role['name'];
        }
        
        return $roles_data;
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
        $user_data = MC_Wp_User::getUserData( $user_id );
        if( empty( $user_data ) ) return $result;
    
        $result['userData'] = [
            'userId'              => $user_id,
            'email'               => $user_data->user_email,
            'username'            => $user_data->user_login,
            'affiliateUrl'        => get_user_meta( $user_id, '_mc_affiliate_url', 1 ),
            'affiliateCouponCode' => get_user_meta( $user_id, '_mc_affiliate_coupon', 1 ),
            'affiliateUserRole'   => !empty( $user_data->roles[0] ) ? $user_data->roles[0] : '',
            'firstName'           => $user_data->first_name,
            'lastName'            => $user_data->last_name,
            'displayName'         => $user_data->display_name,
            'monthlyFee'          => get_user_meta( $user_id, '_mc_affiliate_monthly_fee', 1 ),
            'monthlyFeeCurrency'  => get_user_meta( $user_id, '_mc_affiliate_monthly_fee_currency', 1 ),
            'dateOfComesIn'       => get_user_meta( $user_id, '_mc_affiliate_date_of_comes_in', 1 ),
            'isAffiliate'         => get_user_meta( $user_id, '_mc_affiliate_is', 1 ),
        ];

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
        return MC_Wp_User::mcSearchUsers( $search_term, $limit, $offset, [ 'content_creator' ] );
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
        return MC_Wp_User::mcSearchUsersCount( $search_term, [ 'content_creator' ] );
    }
    
    /**
     * @return string
     */
    public static function getAffiliateRedirectFromCurrentUrl() : string {
        $url = $_SERVER['REQUEST_URI'];
        if( strpos( $url, '/' ) === 0 ) $url = substr( $url, 1 );
        $path              = preg_replace( '/\?.*/', '', $url );
        $path              = strtolower( $path );
        $affiliate_user_id = MC_Affiliate_Coupon::getAffiliateUserIdFromUrl( $path );
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
        if( empty( $user_id = static::getAffiliateIdByCouponCode( $nicename ) ) ) return false;
        if( !MC_User_Functions::isContentCreator( $user_id ) ) return false;
        
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
    
    public static function checkAffiliateCouponInCart($affiliate_coupon = '', $affiliate_id = 0, $applied_coupons = []){
        if(empty($applied_coupons)) {
            $cart_object = $coupons = WC()->cart;
            $applied_coupons = $cart_object->get_applied_coupons();
        }
        if( empty( $applied_coupons ) ) return false;
    
        if(!empty($affiliate_id)) $affiliate_coupon = static::userIdToCoupon($affiliate_id);
    
        return !empty($affiliate_coupon) && in_array($affiliate_coupon, $applied_coupons);
    }
}