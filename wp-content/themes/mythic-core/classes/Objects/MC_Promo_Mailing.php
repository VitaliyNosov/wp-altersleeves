<?php

namespace Mythic_Core\Objects;

use Mythic_Core\Abstracts\MC_Onsite_Object;
use Mythic_Core\Display\MC_Render;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

class MC_Promo_Mailing extends MC_Onsite_Object {
    
    const TABLE_NAME = 'mc_promo_mailings';
    const DB_ID      = 'id';
    
    /**
     * @return string
     */
    public function tableQuery() : string {
        $tableName = $this->getTableName();
        
        return "CREATE TABLE $tableName (
                      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                      `owner_id` int(11) NOT NULL,
                      `user_id` int(11) NULL,
                      `email` VARCHAR (256),
                      `file_name` VARCHAR (256),
                      `file_hash` VARCHAR (256),
                      `date` datetime NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    }
    
    /**
     * Get promo mailing list
     *
     * @param      $list
     * @param bool $omit_already_sent
     *
     * @return array|object|null
     */
    public static function getEmailsFromList( $list, $omit_already_sent = true ) {
        global $wpdb;
        
        $tableName = ( new self() )->getTableName();
        
        $query = "SELECT email, user_id, attempts FROM $tableName WHERE  file_hash = '$list'";
        if( $omit_already_sent ) $query .= " AND (sent IS NULL OR sent = '')";
        
        $mid = $wpdb->get_results( $query );
        
        if( $mid != '' ) {
            return $mid;
        }
        
        return [];
    }
    
    /**
     * Get promo mailing list
     */
    public static function getPromoMailingList() {
        global $wpdb;
        
        $tableName       = ( new self() )->getTableName();
        $current_user_id = get_current_user_id();
        
        $mid = $wpdb->get_results( $wpdb->prepare(
            "SELECT DISTINCT file_name, file_hash FROM $tableName WHERE owner_id = %d", $current_user_id
        ) );
        
        if( $mid != '' ) {
            return $mid;
        }
        
        return [];
    }
    
    /**
     * @return array[]
     */
    public static function getSendEmailsFields() {
        $current_user_id  = get_current_user_id();
        $affiliateCoupons = MC_User::isAdmin() ? MC_Affiliate_Coupon::getAllAffiliatePromotions() : MC_Affiliate_Coupon::getAffiliatePromotions( $current_user_id );
        $affiliateCoupons = array_combine( array_column( $affiliateCoupons, 'wc_coupon_id' ), array_column( $affiliateCoupons, 'promotion_title' ) );
        
        $mailLists = self::getPromoMailingList();
        $mailLists = array_combine( array_column( $mailLists, 'file_hash' ), array_column( $mailLists, 'file_name' ) );
        
        return [
            [
                'label'    => 'Email list',
                'name'     => 'email_list',
                'type'     => 'select',
                'id_part'  => 'promo-mailing-email-list',
                'options'  => $mailLists,
                'default'  => null,
                'empty'    => true,
                'required' => 1,
            ],
            [
                'label'    => '<a href="/dashboard/affiliate-control#mc_promotions" target="_blanc">Promotion</a>',
                'name'     => 'promotion',
                'type'     => 'select',
                'id_part'  => 'promo-mailing-promotion',
                'options'  => $affiliateCoupons,
                'default'  => null,
                'empty'    => true,
                'required' => 1,
            ],
            /*
            [
                'label'    => 'Send Limit (0 = Unlimited)',
                'name'     => 'limit',
                'type'     => 'text',
                'id_part'  => 'promo-limit',
                'value' => 0
            ],
            [
                'label'    => 'Send Offset (ie skip over x emails before sending)',
                'name'     => 'offset',
                'type'     => 'text',
                'id_part'  => 'promo-offset',
                'value' => 0
            ],
            */
            [
                'label'   => 'Send to non-redeemers only',
                'name'    => 'non_redeemers',
                'type'    => 'select',
                'id_part' => 'promo-non-redeemers',
                'options' => [ 1 => 'Yes', 0 => 'No, send to everyone' ],
                'default' => 1,
            ],
            [
                'label'   => 'Send for real',
                'name'    => 'confirm_send',
                'type'    => 'select',
                'id_part' => 'promo-confirm-send',
                'options' => [ 1 => 'Yes', 0 => 'No, this is a test' ],
                'default' => 1
            ],
            [
                'label'    => 'Subject',
                'name'     => 'subject',
                'type'     => 'text',
                'id_part'  => 'promo-mailing-subject',
                'required' => 1,
            ],
            [
                'label'    => 'Email body',
                'name'     => 'email_text',
                'type'     => 'editor',
                'id_part'  => 'promo-mailing-email-text',
                'default'  => '',
                'required' => 1,
            ],
        ];
    }
    
    /**
     * @return array[]
     */
    public static function getAddEmailsFields() {
        return [
            [
                'label'    => 'Mailing List Name',
                'name'     => 'name',
                'type'     => 'text',
                'id_part'  => 'promo-mailing-name',
                'required' => 1,
            ],
            [
                'label'    => 'CSV File',
                'name'     => 'csv_file',
                'type'     => 'file',
                'id_part'  => 'promo-mailing-csv-file',
                'default'  => '',
                'required' => 1,
                'formats'  => '.csv'
            ],
        ];
    }
    
    /**
     * Send promotion to mail list
     *
     * @param $data
     *
     * @return array
     */
    public static function send( $data ) {
        global $wpdb;
        
        $result = [ 'status' => 0, 'message' => '' ];
        
        if( !MC_User_Functions::isContentCreator() ) {
            $result['message'] = 'You don\'t have access to register new affiliate';
            
            return $result;
        }
        
        $fields = static::getSendEmailsFields();
        
        foreach( $fields as $field ) {
            if( !empty( $field['required'] ) && empty( $data[ $field['name'] ] ) ) {
                $result['message'] .= 'You need to fill all required fields! ';
                break;
            }
        }
        
        if( !empty( $result['message'] ) ) {
            return $result;
        }
        
        $limit                   = (int) $data['limit'] ?? 0;
        $offset                  = (int) $data['offset'] ?? 0;
        $list                    = $data['email_list'] ?? [];
        $promotion_id            = $data['promotion'] ?? 0;
        $subject                 = $data['subject'] ?? '';
        $test                    = empty( $data['confirm_send'] );
        $emailText               = stripslashes( $data['email_text'] );
        $promotion_codes         = MC_Affiliate_Coupon::getPromotionCodesForPromotion( $promotion_id );
        $promotion_redirect_link = MC_Affiliate_Coupon::getAffiliatePromotionRedirectLinkById( $promotion_id );
        $code_link_body          = home_url( $promotion_redirect_link.'?af_code=' );
        $url_ext                 = MC_Affiliate_Coupon::getProductsToCartPolicyById( $promotion_id ) == 0 ? '&mod=1' : '';
        
        $emails               = self::getEmailsFromList( $list, false );
        $count                = 0;
        $emails_sent          = 0;
        $promotion_code_break = '';
        foreach( $emails as $email ) {
            //if( $emails_sent == $limit && !empty($limit) ) break;
            $promotion_code = $promotion_codes[ $count ] ?? '';
            if( empty( $promotion_code ) ) break;
            
            $promotion_code_email = $promotion_code['email'] ?? '';
            while( !empty( $promotion_code_email ) ) {
                if( $promotion_code['email'] == $email->email ) {
                    $promotion_code_email = $promotion_code['email'];
                    break;
                }
                $count++;
                $promotion_code = $promotion_codes[ $count ] ?? '';
                if( empty( $promotion_code ) ) {
                    $promotion_code_break = $count;
                    $emails_sent          = 999999;
                    break 2;
                }
                $promotion_code_email = $promotion_code['email'] ?? '';
            }
            $promotion_code = $promotion_code['code'] ?? '';
            if( empty( $promotion_code ) ) continue;
            
            if( !empty( $data['non_redeemers'] ) ) {
                $args   = [
                    'billing_email' => $email->email,
                    'limit'         => 1,
                    'date_created'  => '>'.( time() - ( DAY_IN_SECONDS * 30 ) )
                ];
                $orders = wc_get_orders( $args );
                if( !empty( $orders ) ) continue;
            }
            
            // Mark Email against promotion code and get code
            $wpdb->update(
                MC_Affiliate_Coupon::$affiliate_coupon_codes_table_name,
                [ 'email' => $email->email ],
                [ 'code' => $promotion_code ]
            );
            
            // Get User info and parse message
            $user = get_user_by( 'ID', $email->user_id );
            
            if( $user ) {
                $first_name = get_user_meta( $email->user_id, 'first_name', true );
                $last_name  = get_user_meta( $email->user_id, 'last_name', true );
                
                $emailContent = str_replace( [
                                                 '{{first_name}}',
                                                 '{{last_name}}',
                                                 '{{user_login}}',
                                                 '{{user_email}}',
                                                 '{{user_nicename}}',
                                             ], [
                                                 $first_name,
                                                 $last_name,
                                                 $user->get( 'user_login' ),
                                                 $user->get( 'user_email' ),
                                                 $user->get( 'user_nicename' ),
                                             ], $emailText );
            } else {
                $emailContent = str_replace( [
                                                 '{{first_name}}',
                                                 '{{last_name}}',
                                                 '{{user_login}}',
                                                 '{{user_email}}',
                                                 '{{user_nicename}}',
                                             ], [
                                                 '',
                                                 '',
                                                 '',
                                                 '',
                                                 '',
                                             ], $emailText );
            }
            
            $code_as_html = $code_link_body.$promotion_code.$url_ext;
            $code_as_html = '<a href="'.$code_as_html.'">'.$code_as_html.'</a>';
            $emailContent = str_replace( '{{promotion_code}}', $code_as_html, $emailContent );
            
            if( $test ) {
                $count++;
                $emails_sent++;
                continue;
            }
            
            ob_start();
            MC_Render::templatePart( 'emails', 'header' );
            echo wpautop( $emailContent );
            MC_Render::templatePart( 'emails', 'footer' );
            $emailContent = ob_get_clean();
            
            wp_mail( $email->email, $subject, $emailContent );
            if( $emails_sent == 0 ) wp_mail( EMAIL_JAMES, $subject, $emailContent );
            
            // Mark Sent + coupon/code details etc
            $wpdb->update( $wpdb->prefix.self::TABLE_NAME, [
                'file_hash'    => $list,
                'wc_coupon_id' => $promotion_id,
                'code'         => $promotion_code,
                'sent'         => date( 'Y-m-d H:i:s', time() ),
                'attempts'     => ( $email->attempts ?? 0 ) + 1
            ],
                           [
                               'email' => $email->email
                           ] );
            
            $count++;
            $emails_sent++;
        }
        
        $message = "Successfully sent promotion for $emails_sent emails!";
        if( $test ) $message .= ' - TEST';
        
        return [
            'cart_policy'     => MC_Affiliate_Coupon::getProductsToCartPolicyById( 211007 ),
            'status'          => 1,
            'emails'          => $emails,
            'records'         => $emails_sent,
            'data'            => $data,
            'promotion_break' => $promotion_code_break,
            'count'           => $count,
            'promotions'      => $promotion_codes,
            'message'         => $message,
            'test'            => $test,
            'test_data'       => $data['confirm_send']
        ];
    }
    
    /**
     * Add new mail list
     *
     * @param $data
     *
     * @return array
     */
    public static function add( $data ) {
        global $wpdb;
        
        $current_user_id = get_current_user_id();
        
        $result = [ 'status' => 0, 'message' => '' ];
        
        if( !MC_User_Functions::isContentCreator() ) {
            $result['message'] = 'You don\'t have access to do this!';
            
            return $result;
        }
        
        $fields = static::getAddEmailsFields();
        
        foreach( $fields as $field ) {
            if( !empty( $field['required'] ) && empty( $data[ $field['name'] ] ) && $field['type'] != 'file' ) {
                $result['message'] .= 'You need to fill all required fields! ';
                break;
            }
        }
        
        $filename = $data['csv_file']['tmp_name'] ?? null;
        
        if( !( $handle = fopen( $filename, "r" ) ) ) {
            $result['message'] .= 'Please upload CSV file!';
        }
        
        if( !empty( $result['message'] ) ) {
            return $result;
        }
        
        $date      = date( 'Y-m-d H:i:s', time() );
        $tableName = $wpdb->prefix.self::TABLE_NAME;
        $records   = 0;
        $hash      = uniqid();
        $filename  = $data['name'];
        
        while( ( $line = fgetcsv( $handle ) ) !== false ) {
            $email = $line[0];
            
            if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                $checkIfExists = $wpdb->get_var( "SELECT id FROM $tableName WHERE email = '$email' AND file_name = '$filename'" );
                
                if( $checkIfExists === null ) {
                    $wpdb->insert( $tableName, [
                        'owner_id'  => $current_user_id,
                        'user_id'   => email_exists( $email ),
                        'email'     => $email,
                        'file_hash' => md5( $hash ),
                        'file_name' => $filename,
                        'date'      => $date
                    ] );
                    
                    $records++;
                }
            }
        }
        
        fclose( $handle );
        
        $result = [ 'status' => 1, 'records' => $records, 'message' => "Successfully processed $records emails!" ];
        
        return $result;
    }
    
}