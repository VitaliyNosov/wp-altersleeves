<?php

namespace Mythic_Core\Objects\Store;

use DateTime;
use Exception;
use Mythic_Core\Display\MC_Template_Parts;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Functions\MC_Woo_Cart_Functions;
use Mythic_Core\System\MC_Access;
use Mythic_Core\Users\MC_Affiliates;
use Mythic_Core\Utils\MC_Server;
use Mythic_Core\Utils\MC_Vars;
use WC_Coupon;

/**
 * Class MC_Affiliate_Coupons
 *
 * @package Mythic_Core\Coupons
 */
class MC_Affiliate_Coupon {
    
    public static $affiliate_coupons_table_name = 'mc_affiliate_promotions';
    public static $affiliate_coupon_codes_table_name = 'mc_affiliate_promotion_codes';
    
    /**
     * Register new affiliate Promotion (wc_coupon with additional data)
     *
     * @param $coupon_data
     *
     * @return array
     */
    public static function registerNewAffiliatePromotion( $coupon_data ) {
        $result = [ 'status' => 0, 'message' => '' ];
        
        if( !MC_User_Functions::isAdmin() && !MC_Affiliates::is() ) {
            $result['message'] = 'You don\'t have access to register new promotion';
            
            return $result;
        }
        
        if(
            empty( $coupon_data['promotionTitle'] ) ||
            empty( $coupon_data['promotionTitle'] = preg_replace( "/\s+/", "", $coupon_data['promotionTitle'] ) )
        ) {
            $result['message'] .= 'You need to fill all required fields! ';
            
            return $result;
        }
        
        $fields = static::getRegisterNewAffiliateCouponFields();
        
        foreach( $fields as $field ) {
            if( !empty( $field['required'] ) && empty( $coupon_data[ $field['name'] ] ) ) {
                $result['message'] .= 'You need to fill all required fields! ';
                break;
            }
        }
        
        if( !empty( wc_get_coupon_id_by_code( $coupon_data['promotionTitle'] ) ) ) {
            $result['message'] .= 'This coupon code already used! ';
        }
        
        if( !empty( $result['message'] ) ) {
            return $result;
        }
        
        if( empty( $coupon_data['withDiscount'] ) ) {
            $coupon_data['couponType']    = '';
            $coupon_data['discountValue'] = 0;
        }
        
        // Hardcoded values for hidden fields for now
        $coupon_data['highlightedUsing']      = 1;
        $coupon_data['promotionExpiryDate']   = null;
        $coupon_data['freeUntrackedShipping'] = 1;
        
        $wc_register_result = MC_Coupon::createCoupon( $coupon_data );
        
        if( empty( $wc_register_result['status'] ) ) {
            $result['message'] = $wc_register_result['result'];
            
            return $result;
        }
        
        $coupon_data['couponId'] = $wc_register_result['result'];
        
        $promotion_approved = !empty( $coupon_data['promotionApproved'] ) && current_user_can( 'administrator' ) ? 1 : 0;
        static::changePromotionStatus( $coupon_data['couponId'], $promotion_approved );
        
        $mc_register_result = static::savePromotionData( $coupon_data, true );
        if( empty( $mc_register_result['status'] ) ) {
            $result['message'] = 'Something went wrong while mc saving coupon data';
            
            return $result;
        }
        
        $codes_generating_result = static::generateCouponCodes( $wc_register_result['result'], $coupon_data['promotionNumberOfCodes'] );
        if( empty( $codes_generating_result['status'] ) ) {
            $result['message'] = 'Something went wrong while mc generating coupon codes';
            
            return $result;
        }
        
        return [ 'status' => 1, 'new_coupon_id' => $coupon_data['couponId'] ];
    }
    
    /**
     * Update promotion status
     *
     * @param $promotion_id
     * @param $approved
     */
    public static function changePromotionStatus( $promotion_id, $approved ) {
        wp_update_post( [
                            'ID'          => $promotion_id,
                            'post_status' => $approved ? 'publish' : 'draft',
                        ] );
    }
    
    /**
     * Generates random codes for new promotion
     *
     * @param $wc_coupon_id
     * @param $number_of_codes
     *
     * @return array
     */
    public static function generateCouponCodes( $wc_coupon_id, $codes ) {
        $result = [ 'status' => 0, 'message' => 'Something went wrong' ];
        
        if( empty( $codes ) ) return $result;
        if( is_array( $codes ) ) {
            foreach( $codes as $code ) {
                static::generatePromotionCode( $wc_coupon_id, $code );
            }
        } else {
            if( $codes > 999 ) $codes = 999;
            $i = 1;
            while( $i <= $codes ) {
                if( !empty( static::generatePromotionCode( $wc_coupon_id ) ) ) $i++;
            }
        }
        
        $result['status'] = 1;
        
        return $result;
    }
    
    /**
     * Generates random code for new promotion
     *
     * @param int    $wc_coupon_id
     * @param string $code
     *
     * @return bool
     */
    public static function generatePromotionCode( $wc_coupon_id = 0, $code = '' ) {
        global $wpdb;
        $table_name = static::$affiliate_coupon_codes_table_name;
        $code       = !empty( $code ) ? $code : MC_Vars::generate( 10 );
        
        $data = [
            'wc_coupon_id' => $wc_coupon_id,
            'code'         => $code,
        ];
        
        $data_format = [ '%d', '%s' ];
        
        $query_result = $wpdb->insert( $table_name, $data, $data_format );
        
        if( $query_result === false ) return false;
        
        return true;
    }
    
    /**
     * Returns parent promotion ID by code
     *
     * @param $code
     *
     * @return string|null
     */
    public static function getPromotionIdByCode( $code ) {
        global $wpdb;
        $table_name = static::$affiliate_coupon_codes_table_name;
        $code       = esc_sql( $code );
        
        $query = "SELECT wc_coupon_id FROM $table_name WHERE code = '$code'";
        
        $result = $wpdb->get_var( $query );
        if( !empty( $result ) ) return $result;
        
        $coupon = get_page_by_title( $code, OBJECT, 'shop_coupon' );
        if( empty( $coupon ) ) return null;
        return $coupon->ID;
    }
    
    /**
     * @param $nicename
     *
     * @return int|string|null
     */
    public static function getAffiliateIdByNicenameCoupon( $nicename ) {
        if( is_admin() ) return 0;
        global $wpdb;
        $query = "SELECT user_id FROM wp_usermeta WHERE meta_key = '_mc_affiliate_coupon' and meta_value = '{$nicename}'";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Checks if promotion code already used
     *
     * @param $code
     *
     * @return string|null
     */
    public static function checkIfPromotionCodeUsed( $code ) {
        global $wpdb;
        $table_name = static::$affiliate_coupon_codes_table_name;
        $code       = esc_sql( $code );
        
        $query = "SELECT already_used FROM $table_name WHERE code = '$code'";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Marks promotion code as used
     *
     * @param     $coupon_code
     * @param int $already_used
     *
     * @return bool
     */
    public static function markPromotionCodeAsUsed( $coupon_code, $already_used = 1 ) {
        global $wpdb;
        $table_name = static::$affiliate_coupon_codes_table_name;
        
        $data         = [ 'already_used' => $already_used ];
        $where        = [ 'code' => $coupon_code ];
        $query_result = $wpdb->update( $table_name, $data, $where, [ '%d' ], [ '%s' ] );
        
        return $query_result !== false;
    }
    
    /**
     * Returns all codes for promotion
     *
     * @param        $promotion_id
     * @param string $search_term
     * @param int    $limit
     * @param int    $offset
     *
     * @return array|object|null
     */
    public static function getPromotionCodesForPromotion( $promotion_id, $search_term = '', $limit = 0, $offset = 0 ) {
        global $wpdb;
        $table_name = static::$affiliate_coupon_codes_table_name;
        
        $query = "SELECT * FROM $table_name WHERE wc_coupon_id = $promotion_id";
        if( !empty( $search_term ) ) {
            $query .= " AND code LIKE '%$search_term%'";
        }
        $query .= " ORDER BY id DESC";
        if( !empty( $limit ) ) {
            $query .= " LIMIT $offset, $limit";
        }
        
        return $wpdb->get_results( $query, ARRAY_A );
    }
    
    /**
     * Returns all promotion codes count
     *
     * @param        $promotion_id
     * @param string $search_term
     *
     * @return string|null
     */
    public static function getPromotionCodesForPromotionCount( $promotion_id, $search_term = '' ) {
        if( !is_numeric( $promotion_id ) ) {
            $post = get_page_by_title( $promotion_id, OBJECT, 'shop_coupon' );
            if( !empty( $post ) ) {
                $promotion_id = $post->ID;
            }
        }
        global $wpdb;
        $table_name = static::$affiliate_coupon_codes_table_name;
        
        $query = "SELECT COUNT(*) FROM $table_name WHERE wc_coupon_id = $promotion_id";
        if( !empty( $search_term ) ) {
            $query .= " AND code LIKE '%$search_term%'";
        }
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Saves promotion data
     *
     * @param      $coupon_data
     * @param bool $is_new
     *
     * @return array
     * @throws Exception
     */
    public static function savePromotionData( $coupon_data, $is_new = false ) {
        $result = [ 'status' => 0, 'message' => 'Something went wrong' ];
        
        if( empty( $coupon_data['userId'] ) || empty( $coupon_data['couponId'] ) ) {
            $result['message'] = 'Something went wrong';
            
            return $result;
        }
        
        $wc_coupon = new WC_Coupon( $coupon_data['couponId'] );
        if( empty( $wc_coupon->get_date_created() ) ) return $result;
        
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $redirectLink = MC_Vars::stringCleanForUrl( $coupon_data['redirectLink'] );
        
        $data = [
            'user_id'                        => $coupon_data['userId'] ?? 0,
            'promotion_title'                => $coupon_data['promotionTitle'] ?? '',
            'wc_coupon_id'                   => $coupon_data['couponId'] ?? 0,
            'with_discount'                  => $coupon_data['withDiscount'] ?? 1,
            'free_untracked_shipping'        => 1,
            'free_tracked_shipping'          => 0,
            'free_products'                  => $coupon_data['freeProducts'] ?? '',
            'free_products_list'             => $coupon_data['freeProductsList'] ?? '',
            'all_to_cart'                    => $coupon_data['allProductsToCart'] ?? '',
            'redirect_link'                  => $redirectLink,
            'always_add_coupon_and_tracking' => $coupon_data['alwaysAddCouponAndTracking'] ?? '',
            'highlighted_using'              => 1,
            'promotion_popup_message'        => !empty( $coupon_data['promotionPopupMessage'] ) ? $coupon_data['promotionPopupMessage'] : '',
            'product_for_purchase'           => 0
        ];
        
        $data_format = [ '%d', '%s', '%d', '%d', '%d', '%d', '%d', '%s', '%d', '%s', '%d', '%d', '%s', '%d' ];
        
        if( $is_new ) {
            $query_result = $wpdb->insert( $table_name, $data, $data_format );
        } else {
            $where        = [ 'wc_coupon_id' => $coupon_data['couponId'] ];
            $query_result = $wpdb->update( $table_name, $data, $where, $data_format, [ '%d' ] );
        }
        
        if( $query_result === false ) return $result;
        
        if( current_user_can( 'administrator' ) ) {
            $promotion_approved = !empty( $coupon_data['promotionApproved'] ) ? 1 : 0;
            static::changePromotionStatus( $coupon_data['couponId'], $promotion_approved );
        }
        
        if( !empty( $coupon_data['promotionExpiryDate'] ) ) {
            $expire_date = new DateTime( $coupon_data['promotionExpiryDate'] );
            $wc_coupon->set_date_expires( $expire_date->getTimestamp() );
        } else {
            $wc_coupon->set_date_expires( null );
        }
        
        if( empty( $coupon_data['withDiscount'] ) ) {
            $wc_coupon->set_amount( 0 );
        } else {
            $wc_coupon->set_amount( !empty( $coupon_data['discountValue'] ) ? floatval( $coupon_data['discountValue'] ) : 0 );
            $wc_coupon->set_discount_type( $coupon_data['couponType'] );
        }
        
        $free_shipping = !empty( $coupon_data['freeUntrackedShipping'] ) ? true : false;
        $wc_coupon->set_free_shipping( $free_shipping );
        
        $wc_coupon->save();
        
        return [ 'status' => 1 ];
    }
    
    /**
     * Returns fields for promotion registration form
     *
     * @return array
     */
    public static function getRegisterNewAffiliateCouponFields() {
        return [
            [
                'label'    => 'Promotion title',
                'name'     => 'promotionTitle',
                'type'     => 'text',
                'id_part'  => 'af-register-promotion-title',
                'required' => 1,
            ],
            [
                'label'    => 'Number of Codes',
                'name'     => 'promotionNumberOfCodes',
                'type'     => 'number',
                'id_part'  => 'af-register-number-of-codes',
                'required' => 1,
            ],
            //            [
            //                'label'   => 'Promotion expiry date',
            //                'name'    => 'promotionExpiryDate',
            //                'type'    => 'datepicker',
            //                'id_part' => 'af-register-expiry-date',
            //            ],
            [
                'label'       => 'Approved by administrator',
                'name'        => 'promotionApproved',
                'type'        => 'checkbox',
                'id_part'     => 'af-register-approved',
                'permissions' => 'administrator',
            ],
            [
                'label'   => 'Promotion has discount',
                'name'    => 'withDiscount',
                'type'    => 'checkbox',
                'id_part' => 'af-register-with-discount',
                'default' => 1,
            ],
            [
                'label'       => 'What kind of discount',
                'name'        => 'couponType',
                'type'        => 'select',
                'id_part'     => 'af-register-coupon-type',
                'conditional' => 'af-register-with-discount',
                'options'     => [ 'percent' => 'Percent', 'fixed_cart' => 'Fixed Cart', 'fixed_product' => 'Fixed Product' ],
                'default'     => 'fixed_product',
            ],
            [
                'label'       => 'Discount Value',
                'name'        => 'discountValue',
                'type'        => 'text',
                'id_part'     => 'af-register-coupon-value',
                'conditional' => 'af-register-with-discount',
                'default'     => 0.3,
            ],
            //            [
            //                'label'   => 'Promotion provides free untracked shipping',
            //                'name'    => 'freeUntrackedShipping',
            //                'type'    => 'checkbox',
            //                'id_part' => 'af-register-free-untracked-shipping',
            //            ],
            //            [
            //                'label'   => 'Promotion provides free tracked shipping',
            //                'name'    => 'freeTrackedShipping',
            //                'type'    => 'checkbox',
            //                'id_part' => 'af-register-free-tracked-shipping',
            //            ],
            [
                'label'   => 'Promotion adds product(s) to cart',
                'name'    => 'freeProducts',
                'type'    => 'checkbox',
                'id_part' => 'af-register-free-products',
            ],
            [
                'label'       => 'Add All Products to cart',
                'name'        => 'ProductsToCart',
                'type'        => 'checkbox',
                'conditional' => 'af-register-free-products',
                'id_part'     => 'af-all-products-to-cart',
            ],
            [
                'label'       => 'Products List (comma separated list of ids)',
                'name'        => 'freeProductsList',
                'type'        => 'text',
                'id_part'     => 'af-register-free-products-list',
                'conditional' => 'af-register-free-products',
            ],
            [
                'label'       => 'Free products quantity (how many products of added ids can be added to the cart)',
                'name'        => 'freeProductsQuantity',
                'type'        => 'number',
                'id_part'     => 'af-register-free-products-quantity',
                'conditional' => 'af-register-free-products',
                'disabled'    => 1,
            ],
            [
                'label'   => 'Landing Link (optional)',
                'name'    => 'redirectLink',
                'type'    => 'text',
                'id_part' => 'af-register-redirect-link',
            ],
            [
                'label'   => 'Using these links will also always add their affiliate coupon and tracking',
                'name'    => 'alwaysAddCouponAndTracking',
                'type'    => 'checkbox',
                'id_part' => 'af-register-add-their',
            ],
            //            [
            //                'label'   => 'Once a link is used up, this will be shown as highlighted in the control panel',
            //                'name'    => 'highlightedUsing',
            //                'type'    => 'checkbox',
            //                'id_part' => 'af-register-highlighted-using',
            //            ],
            //            [
            //                'label'   => 'Custom promotion popup message',
            //                'name'    => 'promotionPopupMessage',
            //                'type'    => 'editor',
            //                'id_part' => 'af-register-promotion-popup-message',
            //            ],
        ];
    }
    
    /**
     * Returns fields for promotion update form
     *
     * @param int $user_id
     * @param int $coupon_id
     *
     * @return array
     */
    public static function getExistingAffiliateCouponFields( $user_id = 0, $coupon_id = 0 ) {
        return [
            [
                'label'   => 'Current affiliate promotions',
                'name'    => 'currentAffiliateCoupons',
                'type'    => 'select',
                'id_part' => 'af-edit-existing-coupons-list',
                'options' => static::generateCouponsSelectOptions( $user_id, $coupon_id ),
            ],
            [
                'label'    => 'Promotion title',
                'name'     => 'promotionTitle',
                'type'     => 'text',
                'id_part'  => 'af-edit-existing-promotion-title',
                'required' => 1,
            ],
            //            [
            //                'label'   => 'Promotion expiry date',
            //                'name'    => 'promotionExpiryDate',
            //                'type'    => 'datepicker',
            //                'id_part' => 'af-edit-existing-expiry-date',
            //            ],
            [
                'label'       => 'Approved by administrator',
                'name'        => 'promotionApproved',
                'type'        => 'checkbox',
                'id_part'     => 'af-edit-existing-approved',
                'permissions' => 'administrator',
            ],
            [
                'label'   => 'Promotion has discount',
                'name'    => 'withDiscount',
                'type'    => 'checkbox',
                'id_part' => 'af-edit-existing-with-discount',
            ],
            [
                'label'       => 'What kind of discount',
                'name'        => 'couponType',
                'type'        => 'select',
                'id_part'     => 'af-edit-existing-coupon-type',
                'conditional' => 'af-edit-existing-with-discount',
                'options'     => [ 'percent' => 'Percent', 'fixed_cart' => 'Fixed Cart', 'fixed_product' => 'Fixed Product' ],
                'default'     => 'fixed_product',
            ],
            [
                'label'       => 'Discount Value',
                'name'        => 'discountValue',
                'type'        => 'number',
                'id_part'     => 'af-edit-existing-coupon-value',
                'conditional' => 'af-edit-existing-with-discount',
            ],
            //            [
            //                'label'   => 'Promotion provides free untracked shipping',
            //                'name'    => 'freeUntrackedShipping',
            //                'type'    => 'checkbox',
            //                'id_part' => 'af-edit-existing-free-untracked-shipping',
            //            ],
            //            [
            //                'label'   => 'Promotion provides free tracked shipping',
            //                'name'    => 'freeTrackedShipping',
            //                'type'    => 'checkbox',
            //                'id_part' => 'af-edit-existing-free-tracked-shipping',
            //            ],
            [
                'label'   => 'Promotion adds product(s) to cart',
                'name'    => 'freeProducts',
                'type'    => 'checkbox',
                'id_part' => 'af-edit-existing-free-products',
            ],
            [
                'label'       => 'Products List (comma separated list of ids)',
                'name'        => 'freeProductsList',
                'type'        => 'text',
                'id_part'     => 'af-edit-existing-free-products-list',
                'conditional' => 'af-edit-existing-free-products',
            ],
            [
                'label'       => 'Free products quantity (how many products of added ids can be added to the cart)',
                'name'        => 'freeProductsQuantity',
                'type'        => 'number',
                'id_part'     => 'af-edit-existing-free-products-quantity',
                'conditional' => 'af-edit-existing-free-products',
                'disabled'    => 1,
            ],
            [
                'label'   => 'Landing Link (optional)',
                'name'    => 'redirectLink',
                'type'    => 'text',
                'id_part' => 'af-edit-existing-redirect-link',
            ],
            [
                'label'   => 'Using these links will also always add their affiliate coupon and tracking',
                'name'    => 'alwaysAddCouponAndTracking',
                'type'    => 'checkbox',
                'id_part' => 'af-edit-existing-add-their',
            ],
            //            [
            //                'label'   => 'Once a link is used up, this will be shown as highlighted in the control panel',
            //                'name'    => 'highlightedUsing',
            //                'type'    => 'checkbox',
            //                'id_part' => 'af-edit-existing-highlighted-using',
            //            ],
            //            [
            //                'label'   => 'Custom promotion popup message',
            //                'name'    => 'promotionPopupMessage',
            //                'type'    => 'editor',
            //                'id_part' => 'af-edit-existing-promotion-popup-message',
            //            ],
        ];
    }
    
    /**
     * Generates all promotions list for user
     *
     * @param int $user_id
     * @param int $coupon_id
     *
     * @return array
     */
    public static function generateCouponsSelectOptions( $user_id = 0, $coupon_id = 0 ) {
        $coupon_options = [ 0 => 'You need create coupons first' ];
        if( empty( $user_id ) && !empty( $coupon_id ) ) {
            $user_id = static::getAffiliateIdByPromotionId( $coupon_id );
        }
        if( empty( $user_id ) ) return $coupon_options;
        
        $affiliate_coupons = static::getAffiliatePromotions( $user_id );
        if( empty( $affiliate_coupons ) ) {
            return $coupon_options;
        }
        
        $coupon_options = [];
        foreach( $affiliate_coupons as $affiliate_coupon ) {
            $coupon_options[ $affiliate_coupon['wc_coupon_id'] ] = $affiliate_coupon['promotion_title'];
        }
        
        return $coupon_options;
    }
    
    /**
     * Returns all promotions
     *
     * @param string $search_term
     * @param int    $limit
     * @param int    $offset
     *
     * @return array|object|null
     */
    public static function getAllAffiliatePromotions( $search_term = '', $limit = 0, $offset = 0 ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT * FROM $table_name";
        if( !empty( $search_term ) ) {
            $query .= " WHERE promotion_title LIKE '%$search_term%'";
        }
        $query .= " ORDER BY user_id DESC";
        if( !empty( $limit ) ) {
            $query .= " LIMIT $offset, $limit";
        }
        
        return $wpdb->get_results( $query, ARRAY_A );
    }
    
    /**
     * Returns all affiliate promotions count
     *
     * @param string $search_term
     *
     * @return string|null
     */
    public static function getAllAffiliatePromotionsCount( $search_term = '' ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT COUNT(*) FROM $table_name";
        if( !empty( $search_term ) ) {
            $query .= " WHERE promotion_title LIKE '%$search_term%'";
        }
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Returns all promotions for affiliate
     *
     * @param $user_id
     *
     * @return array|object|null
     */
    public static function getAffiliatePromotions( $user_id ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT * FROM $table_name WHERE user_id = $user_id ORDER BY id DESC";
        
        return $wpdb->get_results( $query, ARRAY_A );
    }
    
    /**
     * Returns affiliate ID by promotion ID
     *
     * @param $coupon_id
     *
     * @return string|null
     */
    public static function getAffiliateIdByPromotionId( $coupon_id ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT user_id FROM $table_name WHERE wc_coupon_id = $coupon_id";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Returns affiliate ID by promotion ID
     *
     * @param $coupon_id
     *
     * @return string|null
     */
    public static function getPromotionIdByCouponId( $coupon_id ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT id FROM $table_name WHERE wc_coupon_id = $coupon_id";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Checks if promotion exists by ID
     *
     * @param $coupon_id
     *
     * @return bool
     */
    public static function checkIfPromotionExistById( $coupon_id ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT wc_coupon_id FROM $table_name WHERE wc_coupon_id = $coupon_id";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Checks if promotion exists by wc code (coupon title)
     *
     * @param $coupon_id
     *
     * @return bool
     */
    public static function checkIfPromotionExistByWCCode( $coupon_title ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT wc_coupon_id FROM $table_name WHERE promotion_title = '$coupon_title'";
        
        return $wpdb->get_var( $query );
    }
    
    /**
     * Returns prepared promotion data
     *
     * @param $coupon_id
     *
     * @return array|object|void|null
     */
    public static function getAffiliatePromotionDataById( $coupon_id ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT * FROM $table_name WHERE wc_coupon_id = $coupon_id";
        
        $coupon_data = $wpdb->get_row( $query, ARRAY_A );
        
        if( empty( $coupon_data ) ) return $coupon_data;
        
        $redirectLink = !empty( $coupon_data['redirect_link'] ) ? MC_Vars::stringCleanForUrl( $coupon_data['redirect_link'] ) : '';
        $coupon_data  = [
            'currentAffiliateCoupons'    => $coupon_id,
            'promotionId'                => $coupon_id,
            'userId'                     => $coupon_data['user_id'],
            'withDiscount'               => !empty( $coupon_data['with_discount'] ) ? 1 : 0,
            'freeUntrackedShipping'      => !empty( $coupon_data['free_untracked_shipping'] ) ? 1 : 0,
            'freeProducts'               => !empty( $coupon_data['free_products'] ) ? $coupon_data['free_products'] : 0,
            'freeProductsList'           => $coupon_data['free_products_list'],
            'allProductsToCart'          => $coupon_data['all_to_cart'] ?? 0,
            'redirectLink'               => $redirectLink,
            'alwaysAddCouponAndTracking' => !empty( $coupon_data['always_add_coupon_and_tracking'] ) ? 1 : 0,
            'highlightedUsing'           => !empty( $coupon_data['highlighted_using'] ) ? 1 : 0,
            'promotionPopupMessage'      => !empty( $coupon_data['promotion_popup_message'] ) ? $coupon_data['promotion_popup_message'] : '',
        ];
        
        $wc_coupon = new WC_Coupon( $coupon_id );
        if( empty( $wc_coupon->get_date_created() ) ) return $coupon_data;
        
        $wp_post                            = get_post( $coupon_id );
        $coupon_data['promotionTitle']      = $wc_coupon->get_code();
        $coupon_data['promotionExpiryDate'] = $wc_coupon->get_date_expires();
        $coupon_data['promotionApproved']   = !empty( $wp_post->post_status ) && $wp_post->post_status == 'publish' ? 1 : 0;
        $coupon_data['promotionCodes']      = MC_Template_Parts::get( 'affiliates/parts/as-affiliates-coupons', 'codes',
                                                                      [ 'promotion_id' => $coupon_id ] );
        
        if( empty( $coupon_data['withDiscount'] ) ) return $coupon_data;
        
        $coupon_data['couponType']    = $wc_coupon->get_discount_type();
        $coupon_data['discountValue'] = $wc_coupon->get_amount();
        
        return $coupon_data;
    }
    
    /**
     * Returns promotion redirect link
     *
     * @param $coupon_id
     *
     * @return string|null
     */
    public static function getAffiliatePromotionRedirectLinkById( $coupon_id ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $query         = "SELECT redirect_link FROM $table_name WHERE wc_coupon_id = $coupon_id";
        $promotion_url = $wpdb->get_var( $query );
        
        if( empty( $promotion_url ) ) {
            $promotion_url = '/';
        } else {
            if( strpos( $promotion_url, '/' ) !== 0 ) {
                $promotion_url = '/'.$promotion_url;
            }
        }
        
        return $promotion_url;
    }
    
    /**
     * Returns products from link
     *
     * @param string $url
     *
     * @return array
     */
    public static function getAffiliatePromotionProductsFromUrl( $url = '' ) : array {
        global $wpdb;
        if( empty( $url ) ) $url = MC_Server::primaryPath();
        
        $table_name = static::$affiliate_coupons_table_name;
        
        $query       = "SELECT free_products_list FROM $table_name WHERE redirect_link = '$url'";
        $product_ids = $wpdb->get_var( $query );
        
        return !empty( $product_ids ) ? explode( ',', $product_ids ) : [];
    }
    
    /**
     * @param int $id
     *
     * @return array
     */
    public static function getAffiliatePromotionProductsFromId( $id = 0 ) : array {
        global $wpdb;
        
        $table_name  = static::$affiliate_coupons_table_name;
        $query       = "SELECT free_products_list FROM $table_name WHERE id = $id";
        $product_ids = $wpdb->get_var( $query );
        
        return !empty( $product_ids ) ? explode( ',', $product_ids ) : [];
    }
    
    /**
     * @param int $id
     *
     * @return bool
     */
    public static function promotionProductsBuyableById( $id = 0 ) {
        if( empty( $_GET['af_code'] ) && empty( $_COOKIE['mc_af_code'] ) && empty( $_COOKIE['coupon'] ) ) return false;
        global $wpdb;
        
        $table_name  = static::$affiliate_coupons_table_name;
        $query       = "SELECT product_for_purchase FROM $table_name WHERE id = $id";
        $purchasable = $wpdb->get_var( $query );
        return !empty( $purchasable );
    }
    
    /**
     * Returns products from link
     *
     * @param string $url
     *
     * @return int
     */
    public static function getAffiliatePromotionIdFromUrl( $url = '' ) : int {
        global $wpdb;
        if( empty( $url ) ) $url = MC_Server::primaryPath();
        
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT id FROM $table_name WHERE redirect_link = '$url'";
        $query = $wpdb->get_var( $query );
        return !empty( $query ) ? $query : 0;
    }
    
    /**
     * @param string $url
     *
     * @return int
     */
    public static function getAffiliateUserIdFromUrl( $url = '' ) : int {
        global $wpdb;
        if( empty( $url ) ) $url = MC_Server::primaryPath();
        
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT user_id FROM $table_name WHERE redirect_link = '$url'";
        $query = $wpdb->get_var( $query );
        
        return !empty( $query ) ? $query : 0;
    }
    
    /**
     * Returns promotion redirect link
     *
     * @return array
     */
    public static function getAllPromotionProductIds() {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT free_products_list FROM $table_name";
        
        $product_ids = $wpdb->get_results( $query );
        $results     = [];
        foreach( $product_ids as $product_id ) {
            $product_id = $product_id->free_products_list ?? '';
            if( empty( $product_id ) ) continue;
            $product_id = explode( ',', $product_id );
            $results    = array_merge( $results, $product_id );
        }
        
        return $results;
    }
    
    /**
     * Returns promotion redirect link
     *
     * @param $coupon_id
     *
     * @return array
     */
    public static function getAffiliatePromotionFreeProductsById( $coupon_id ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $query = "SELECT free_products_list FROM $table_name WHERE wc_coupon_id = $coupon_id";
        
        $product_ids = $wpdb->get_var( $query );
        
        return !empty( $product_ids ) ? explode( ',', $product_ids ) : [];
    }
    
    /**
     * Returns promotion redirect link
     *
     * @param $coupon_id
     *
     * @return int
     */
    public static function getProductsToCartPolicyById( $coupon_id ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
    
        $query = "SELECT all_to_cart FROM $table_name WHERE wc_coupon_id = $coupon_id";
    
        $policy = $wpdb->get_var( $query );
        if( empty($policy) ) $policy = 0;
        return $policy;
    }
    
    /**
     * Check affiliate functionality and clear cookies after order is created
     *
     * @param $order
     */
    public static function checkAffiliateFunctionality( $order ) {
        $promotion_mc_codes = static::getAffiliateCouponsDataFromCookie();
        $applied_coupons    = $order->get_coupons();
        
        if( !empty( $applied_coupons ) ) {
            $applied_coupons_data = [];
            
            foreach( $applied_coupons as $applied_coupon ) {
                $coupon_code                        = $applied_coupon->get_code();
                $coupon_id                          = wc_get_coupon_id_by_code( $coupon_code );
                $applied_coupons_data[ $coupon_id ] = $coupon_code;
                $affiliate_user_id                  = MC_Affiliates::getAffiliateIdByCouponCode( $coupon_code );
                if( !empty( $affiliate_user_id ) ) {
                    update_post_meta( $order->get_id(), '_mc_order_affiliate_user_id', $affiliate_user_id );
                }
            }
            
            foreach( $promotion_mc_codes as $promotion_mc_code ) {
                if( !empty( $promotion_id = static::getPromotionIdByCode( $promotion_mc_code ) ) ) {
                    if( !empty( $applied_coupons_data[ $promotion_id ] ) ) static::markPromotionCodeAsUsed( $promotion_mc_code );
                }
            }
        }
        
        $cookies = [ 'mc_af_code', 'coupon' ];
        foreach( $cookies as $cookie ) {
            unset( $_COOKIE[ $cookie ] );
            setcookie( $cookie, null, -1, '/' );
        }
    }
    
    /**
     * Check if affiliate promotion applied in correct way
     *
     */
    public static function checkAffiliatesPromotions() {
        $cart_object = $coupons = WC()->cart;
        $coupons     = $cart_object->get_applied_coupons();
        if( empty( $coupons ) ) return;
        
        $promotion_mc_codes = static::getAffiliateCouponsDataFromCookie();
        $used_promotions    = [];
        
        foreach( $coupons as $coupon ) {
            if( !empty( $coupon_id = static::checkIfPromotionExistByWCCode( $coupon ) ) ) {
                $used_promotions[ $coupon_id ] = $coupon;
            }
        }
        
        if( !empty( $promotion_mc_codes ) ) {
            foreach( $promotion_mc_codes as $promotion_mc_code_single ) {
                if( empty( $promotion_id = static::getPromotionIdByCode( $promotion_mc_code_single ) ) ) continue;
                
                if( empty( $_COOKIE['mc_af_code'] ) ) {
                    $cart_object->remove_coupon( $coupon );
                    
                    if( !empty( $used_promotions[ $promotion_id ] ) ) unset( $used_promotions[ $promotion_id ] );
                }
            }
            
            if( !empty( $used_promotions ) ) {
                foreach( $used_promotions as $used_promotion ) {
                    $cart_object->remove_coupon( $used_promotion );
                }
            }
        }
    }
    
    /**
     * @return array|mixed
     */
    public static function getAffiliateCouponsDataFromCookie() {
        $promotion_mc_codes = json_decode( stripslashes( $_COOKIE['mc_af_code'] ?? '' ), true );
        if( empty( $promotion_mc_codes ) ) {
            $promotion_mc_codes = $_COOKIE['coupon'] ?? '';
            $promotion_mc_codes = [ $promotion_mc_codes ];
        }
        return is_array( $promotion_mc_codes ) ? $promotion_mc_codes : [];
    }
    
    /**
     * @return array
     */
    public static function getCurrentFreeAffiliateProducts() : array {
        $free_products = [];
        $mc_af_codes   = static::getAffiliateCouponsDataFromCookie();
        if( empty( $mc_af_codes ) ) return $free_products;
        
        foreach( $mc_af_codes as $mc_af_code_single ) {
            if( empty( $promotion_id = static::getPromotionIdByCode( $mc_af_code_single ) ) ) continue;
            
            $free_products = array_merge( $free_products, static::getAffiliatePromotionFreeProductsById( $promotion_id ) );
        }
        
        return $free_products;
    }
    
    /**
     * @return array|false
     * @throws Exception
     */
    public static function checkAndApplyAffiliatesCoupons( $with_cookies = true ) {
        if( is_admin() ) return false;
        if( !MC_Access::primarySite() ) return false;
        if( !function_exists( 'WC' ) ) return false;
        
        $code = !empty( $_GET['af_code'] ) ? $_GET['af_code'] : '';
        if( empty( $code ) && !empty( $_GET['coupon'] ) ) {
            $code = $_GET['coupon'];
        }
        if( empty( $code ) && !empty( $_COOKIE['coupon'] ) ) {
            $code = $_COOKIE['coupon'];
        }
        if( empty( $_COOKIE['coupon'] ) ) {
            setcookie( 'coupon', $code, time() + ( 86400 * 30 ), "/" );
        }
        
        if( isset( $_COOKIE['coupon'] ) && $_COOKIE['coupon'] == $code ) {
            $get_applied_coupons = WC()->cart->get_applied_coupons() ?? [];
            if( !in_array( $code, $get_applied_coupons ) ) {
                WC()->cart->apply_coupon( $code );
            }
        }
        
        if( !empty( $affiliate_id = $_COOKIE['mc_affiliate_id'] ?? '' ) ) {
            $coupon_code         = MC_Affiliates::userIdToCoupon( $affiliate_id );
            $get_applied_coupons = WC()->cart->get_applied_coupons() ?? [];
            if( empty( $get_applied_coupons ) || !in_array( $coupon_code, $get_applied_coupons ) ) {
                WC()->cart->apply_coupon( $coupon_code );
            }
        }
        
        if( empty( $code ) ) return false;
        
        return static::applyPromotionByCode( $code, $with_cookies );
    }
    
    /**
     * @param      $promotion_mc_codes
     *
     * @return array
     * @throws Exception
     */
    public static function applyPromotionByCode( $promotion_mc_codes, $with_cookies = true ) : array {
        $result = [ 'status' => 0, 'message' => '' ];
        
        $promotion_mc_codes      = is_array( $promotion_mc_codes ) ? $promotion_mc_codes : [ $promotion_mc_codes ];
        $promotion_mc_used_codes = static::getAffiliateCouponsDataFromCookie();
        $get_applied_coupons     = WC()->cart->get_applied_coupons();
        
        foreach( $promotion_mc_codes as $promotion_mc_code_single ) {
            if( empty( $promotion_id = static::getPromotionIdByCode( $promotion_mc_code_single ) ) ) continue;
            
            if( empty( $promotion_wc_code = wc_get_coupon_code_by_id( $promotion_id ) ) ) continue;
            
            if( !empty( static::checkIfPromotionCodeUsed( $promotion_mc_code_single ) ) ) continue;
            
            // We need to decide how we will check url if we want to use several affiliate coupons
            //        if( $checkUrl ) {
            //            if( empty( static::checkIfCorrectPromotionUrlBeforeApply( $promotion_id ) ) ) return $result;
            //        }
            
            if( static::checkIfAffiliateCouponMustBeUsed( $promotion_id ) ) {
                static::applyAffiliateCoupon( $promotion_id );
            }
            
            if( empty( $get_applied_coupons ) || !in_array( $promotion_wc_code, $get_applied_coupons ) ) {
                WC()->cart->apply_coupon( $promotion_wc_code );
            }
            
            $free_product_res = static::checkAndMaybeAddPromotionFreeProducts( $promotion_id, $promotion_wc_code );
            if( !empty( $free_product_res['status'] ) && !empty( $free_product_res['message'] ) ) {
                $result['message'] = $free_product_res['message'];
            }
            
            if( !in_array( $promotion_mc_code_single, $promotion_mc_used_codes ) ) $promotion_mc_used_codes[] = $promotion_mc_code_single;
        }
        
        if( $with_cookies )
            setcookie( 'mc_af_code', json_encode( $promotion_mc_used_codes, JSON_UNESCAPED_SLASHES ), time() + ( 86400 * 30 ), "/" );
        
        $result['status'] = 1;
        
        return $result;
    }
    
    /**
     * @param $coupon
     *
     * @return string
     */
    public static function generateNoticeMessage( $coupon ) {
        switch( $coupon ) {
            case MC_Vars::stringContains( $coupon, 'playtowin' ):
                return 'We have added your free* Play To Win Alter Sleeve to your cart - additionally please enjoy a further 10% on your order<br><small>*Discount will be applied at checkout</small>';
            case MC_Vars::stringContains( $coupon, 'taaliavess' ):
                return 'We have added your free* Taalia Vess Alter Sleeve to your cart - additionally please enjoy a further 10% on your order<br><small>*Discount will be applied at checkout</small>';
            case MC_Vars::stringContains( $coupon, 'marchesa' ):
                return 'We have added your free* Marchesa Tournament Alter Sleeve to your cart - additionally please enjoy a further 10% on your order<br><small>*Discount will be applied at checkout</small>';
            case MC_Vars::stringContains( $coupon, 'magicmics' ):
                return 'You have been granted a free Alter Sleeve*: courtesy of Magic Mics. Please select one of their three custom designs featured on their profile below! You will also receive a 10% discount on additional sleeves, and free shipping!<br><small>*Accurate total including free sleeve will only be shown at checkout.</small>';
            default:
                return '';
        }
    }
    
    /**
     * @param $coupon_id
     *
     * @return bool
     */
    public static function checkIfAffiliateCouponMustBeUsed( $coupon_id ) {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        $query      = "SELECT always_add_coupon_and_tracking FROM $table_name WHERE wc_coupon_id = $coupon_id";
        
        return !empty( $wpdb->get_var( $query ) );
    }
    
    /**
     * @param $promotion_id
     */
    public static function applyAffiliateCoupon( $promotion_id ) {
        if( empty( $affiliate_id = static::getAffiliateIdByPromotionId( $promotion_id ) ) ) return;
        
        if( empty( $coupon_code = MC_Affiliates::userIdToCoupon( $affiliate_id ) ) ) return;
        
        $get_applied_coupons = WC()->cart->get_applied_coupons();
        
        if( empty( $get_applied_coupons ) || !in_array( $coupon_code, $get_applied_coupons ) ) {
            WC()->cart->apply_coupon( $coupon_code );
        }
    }
    
    /**
     * @param $promotion_id
     *
     * @return bool
     */
    public static function checkIfCorrectPromotionUrlBeforeApply( $promotion_id ) {
        $promotion_url   = static::getAffiliatePromotionRedirectLinkById( $promotion_id );
        $uri_without_get = explode( '?', $_SERVER['REQUEST_URI'], 2 );
        $uri             = '/';
        if( !empty( $uri_without_get[0] ) ) {
            if( strpos( $uri_without_get[0], '/' ) !== 0 ) {
                $uri = '/'.$uri_without_get[0];
            } else {
                $uri = $uri_without_get[0];
            }
        }
        
        if( strtolower( $uri ) != strtolower( $promotion_url ) ) return false;
        
        return true;
    }
    
    /**
     * @param $promotion_id
     * @param $promotion_wc_code
     *
     * @return array
     */
    public static function checkAndMaybeAddPromotionFreeProducts( $promotion_id, $promotion_wc_code ) {
        $result      = [ 'status' => 0, 'message' => '' ];
        $product_ids = static::getAffiliatePromotionFreeProductsById( $promotion_id );
        if( !is_array( $product_ids ) ) return $result;
        $all_to_cart = static::getProductsToCartPolicyById( $promotion_id );
        $cart        = WC()->cart;
        if( count( $product_ids ) > 1 && empty( $all_to_cart ) ) return $result;
        foreach( $product_ids as $product_id ) {
            if( empty( wc_get_product( $product_id ) ) ) continue;
            $cart_item_key = $cart->generate_cart_id( $product_id );
            $in_cart       = $cart->find_product_in_cart( $cart_item_key );
            if( !$in_cart ) {
                try {
                    $cart_item_key = $cart->add_to_cart( $product_id, 1, 0, [], [ 'mc_free_product' => 1 ] );
                    if( !empty( $cart_item_key ) ) {
                        $cart_items = $cart->get_cart();
                        if( !empty( $cart_items[ $cart_item_key ]['data'] ) ) {
                            $cart_items[ $cart_item_key ]['data']->set_price( 0 );
                            $cart->set_quantity( $cart_item_key, 1 );
                            $result = [
                                'status'  => 1,
                                'message' => self::generateNoticeMessage( $promotion_wc_code ),
                            ];
                        }
                    }
                } catch( Exception $e ) {
                }
            }
        }
        
        return $result;
        
        // TODO: check if we need manual price changing for other products
        //        foreach( $cart_items as $key => $cart_item ) {
        //            if( empty( $cart_item['mc_free_product'] ) ) {
        //                $price = $cart_item['data']->get_price();
        //                $cart_item['data']->set_price( $price * 0.9 );
        //            }
        //        }
    }
    
    /**
     * Creates additional affiliate promotions table
     */
    public static function addAffiliatePromotionsTable() {
        global $wpdb;
        $table_name = static::$affiliate_coupons_table_name;
        
        $create_table_query = "CREATE TABLE IF NOT EXISTS `$table_name` (
              `id` BIGINT(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
              `user_id` BIGINT(20) UNSIGNED NOT NULL,
              `promotion_title` LONGTEXT NOT NULL,
              `wc_coupon_id` BIGINT(20) UNSIGNED NOT NULL,
              `with_discount` TINYINT (1) DEFAULT NULL,
              `free_untracked_shipping` TINYINT (1) DEFAULT NULL,
              `free_tracked_shipping` TINYINT (1) DEFAULT NULL,
              `free_products` TINYINT (1) DEFAULT NULL,
              `free_products_list` LONGTEXT,
              `redirect_link` VARCHAR (256),
              `always_add_coupon_and_tracking` TINYINT (1) DEFAULT NULL,
              `highlighted_using` TINYINT (1) DEFAULT NULL,
              `promotion_popup_message` LONGTEXT DEFAULT NULL
            ) {$wpdb -> get_charset_collate()};";
        require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
        
        dbDelta( $create_table_query );
    }
    
    /**
     * Creates additional affiliate promotion codes table
     */
    public static function addAffiliateCouponsCodeTable() {
        global $wpdb;
        $table_name = static::$affiliate_coupon_codes_table_name;
        
        $create_table_query = "CREATE IF NOT EXISTS TABLE $table_name (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `wc_coupon_id` bigint(20) unsigned NOT NULL,
        `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
        `already_used` tinyint(1) DEFAULT NULL,
        `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `code` (`code`),
        KEY `wc_coupon_id` (`wc_coupon_id`),
        KEY `already_used` (`already_used`),
        KEY `email` (`email`)
        ) {$wpdb -> get_charset_collate()};";
        require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
        
        dbDelta( $create_table_query );
    }
    
    /**
     * @param $coupon_code
     */
    public static function clearCouponFromCookies( $coupon_code ) {
        $coupon_id = wc_get_coupon_id_by_code( $coupon_code );
        if( empty( $coupon_id ) ) return;
        
        $cookies = [ 'mc_affiliate_id', 'mc_af_code', 'coupon' ];
        foreach( $cookies as $cookie ) {
            $promotion    = $_COOKIE[ $cookie ] ?? '';
            $promotion_id = self::getPromotionIdByCode( $promotion );
            if( $coupon_id == $promotion_id ) {
                unset( $_COOKIE[ $cookie ] );
                setcookie( $cookie, null, -1, '/' );
            }
        }
    }
    
    /**
     * @param int $promotion_id
     *
     * @return bool
     */
    public static function promotionalProductsInCart( $promotion_id = 0 ) : bool {
        if( empty( $promotion_id ) ) return false;
        $promotion_products = self::getAffiliatePromotionProductsFromId( $promotion_id );
        foreach( $promotion_products as $promotion_product ) {
            if( !empty( MC_Woo_Cart_Functions::productInCart( $promotion_product ) ) ) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @param array $args
     *
     * @throws Exception
     */
    public static function addPromotionalProductToCart( $args = [] ) {
        extract( $args );
        if( empty( $product_id ) || empty( $promotion_id ) ) return;
        $promotion_products = self::getAffiliatePromotionProductsFromId( $promotion_id );
        
        $published     = true;
        $already_added = false;
        foreach( $promotion_products as $promotion_product ) {
            $status = get_post_status( $promotion_product );
            if( $status != 'publish' ) $published = false;
            if( !empty( MC_Woo_Cart_Functions::productInCart( $promotion_product ) ) ) {
                $already_added = true;
                break;
            }
        }
        if( empty( $published ) && empty( $already_added ) ) $published = true;
        if( !$published || empty( wc_get_product( $product_id ) ) ) return;
        
        $cart = WC()->cart;
        $cart->add_to_cart( $product_id, 1, 0, [], [ 'mc_free_product' => 1 ] );
    }
    
}