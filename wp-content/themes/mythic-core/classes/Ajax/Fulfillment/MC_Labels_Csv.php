<?php

namespace Mythic_Core\Ajax\Fulfillment;

use MC_Vars;
use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_User_Functions;
use WC_Order_Query;

/**
 * Class MC_Labels_Csv
 *
 * @package Mythic_Core\Ajax\Fulfillment
 */
class MC_Labels_Csv extends MC_Ajax {
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'as-orders-label-csv';
    }
    
    /**
     * @return string
     */
    protected static function get_nonce_name() : string {
        return 'as-order-data';
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $data   = 'PRODUCT;SERVICE_LEVEL;CUST_EKP;AWB;REGISTERED_BARCODE;CUST_REF;NAME;RECIPIENT_PHONE;RECIPIENT_PHONE_2;RECIPIENT_EMAIL;ADDRESS_LINE_1;ADDRESS_LINE_2;ADDRESS_LINE_3;CITY;STATE;POSTAL_CODE;DESTINATION_COUNTRY;WEIGHT;CURRENCY;CONTENT_TYPE;DECLARED_CONTENT_AMOUNT_1;DETAILED_CONTENT_DESCRIPTIONS_1;DECLARED_NETWEIGHT_1;DECLARED_VALUE_1;DECLARED_HS_CODE_1;DECLARED_ORIGIN_COUNTRY_1;DECLARED_CONTENT_AMOUNT_2;DETAILED_CONTENT_DESCRIPTIONS_2;DECLARED_NETWEIGHT_2;DECLARED_VALUE_2;DECLARED_HS_CODE_2;DECLARED_ORIGIN_COUNTRY_2;DECLARED_CONTENT_AMOUNT_3;DETAILED_CONTENT_DESCRIPTIONS_3;DECLARED_NETWEIGHT_3;DECLARED_VALUE_3;DECLARED_HS_CODE_3;DECLARED_ORIGIN_COUNTRY_3;DECLARED_CONTENT_AMOUNT_4;DETAILED_CONTENT_DESCRIPTIONS_4;DECLARED_NETWEIGHT_4;DECLARED_VALUE_4;DECLARED_HS_CODE_4;DECLARED_ORIGIN_COUNTRY_4;DECLARED_CONTENT_AMOUNT_5;DETAILED_CONTENT_DESCRIPTIONS_5;DECLARED_NETWEIGHT_5;DECLARED_VALUE_5;DECLARED_HS_CODE_5;DECLARED_ORIGIN_COUNTRY_5;TOTAL_VALUE;RETURN_LABEL';
        $query  = new WC_Order_Query( [
                                          'limit'        => -1,
                                          'status'       => 'processing',
                                          'order'        => 'ASC',
                                          'meta_key'     => 'mc_label_printed', // The postmeta key field
                                          'meta_compare' => 'NOT EXISTS', // The comparison argument
                                      ] );
        $orders = $query->get_orders();
        $lines  = [];
        
        foreach( $orders as $order ) {
            $order_id = $order->get_id();
            
            //if( !in_array($order_id, $reship) ) continue;
            $company = $order->get_shipping_company() ?? '';
            $country = $order->get_shipping_country();
            if( $country == 'US' ) continue;
            // if( AS_Woo_Shipping_Methods::isDisallowedCountry( $country ) ) continue;
            $state = $order->get_shipping_state() ?? '';
            if( strtolower( $state ) == 'please select region, state or province' ) $state = '';
            
            $numberOfSleeves = 0;
            foreach( $order->get_items() as $itemId => $itemData ) {
                $quantity        = $itemData->get_quantity(); // Get the item quantity
                $numberOfSleeves = $numberOfSleeves + $quantity;
            }
            
            $whole  = (int) ( $numberOfSleeves / 13 );
            $modulo = $numberOfSleeves % 13;
            
            $weight = ( $whole * 40 );
            if( !empty( $modulo ) ) $weight = $weight + 40;
            
            $total = $order->get_total() > 0 ? $order->get_total() : 6;
            if( !empty( $order->get_shipping_total() ) ) $total = $total - $order->get_shipping_total();
            
            $email    = '';
            $type     = 'GMP';
            $method   = $order->get_shipping_method() ?? '';
            $tracking = false;
            $method = strtolower($method);
            if( strpos( $method, 'tracked' ) !== false || $order->get_user_id( MC_User_Functions::isAdmin() ) || $total >= 60 ) {
                if( strpos( $method, 'untracked' ) === false ) $tracking = true;
            }
            if( $tracking ) {
                $type  = 'GPT';
                $email = $order->get_billing_email();
            }
            
            $key = sanitize_title( $order->get_billing_email().$order->get_shipping_postcode() );
            
            if( isset( $lines[ $key ] ) ) {
                // Weight
                $old_weight              = $lines[ $key ]['weight'];
                $new_weight              = $old_weight + $weight;
                $lines[ $key ]['weight'] = $new_weight;
                // Value
                $old_total = $lines[ $key ]['total'];
                $new_total = $old_total + $total;
                $total     = $lines[ $key ]['total'] = $new_total;
                if( $total >= 60 ) {
                    $tracking = true;
                }
                if( $tracking ) {
                    $type                   = 'GPT';
                    $lines[ $key ]['email'] = $order->get_billing_email();
                }
                $lines[ $key ]['type']     = $type;
                $lines[ $key ]['orders'][] = $order_id;
                continue;
            }
            
            $first_name = $order->get_shipping_first_name() ?? $order->get_billing_first_name();
            $last_name  = $order->get_shipping_last_name() ?? $order->get_billing_last_name();
            $address_1  = $order->get_shipping_address_1() ?? $order->get_billing_address_1();
            $address_2  = $order->get_shipping_address_2() ?? $order->get_billing_address_2();
            $city       = $order->get_shipping_city() ?? $order->get_billing_city();
            
            if( !isset( $lines[ $key ] ) ) $lines[ $key ] = [];
            
            $lines[ $key ]['type']      = $type;
            $lines[ $key ]['id']        = $order->get_id();
            $lines[ $key ]['name']      = $first_name.' '.$last_name;
            $lines[ $key ]['email']     = $email;
            $lines[ $key ]['phone']     = $order->get_billing_phone() ?? '';
            $lines[ $key ]['address_1'] = $address_1;
            $lines[ $key ]['address_2'] = $address_2;
            $lines[ $key ]['company']   = $company;
            $lines[ $key ]['city']      = $city;
            $lines[ $key ]['state']     = $state;
            $lines[ $key ]['country']   = $country;
            $lines[ $key ]['weight']    = $weight;
            $lines[ $key ]['post_code'] = $order->get_shipping_postcode();
            $lines[ $key ]['currency']  = $order->get_currency();
            $lines[ $key ]['total']     = $total;
            $lines[ $key ]['orders']    = [ $order_id ];
        }
        
        $count = get_option( 'mc_order_labels_printed', 0 );
        $box   = get_option( 'mc_dhl_box', MC_Vars::generate( 4 ) );
        foreach( $lines as $line ) {
            $customer_orders = $line['orders'];
            if( empty( $customer_orders ) ) continue;
            
            foreach( $customer_orders as $order_id ) {
                update_post_meta( $order_id, 'mc_label_printed', time() );
            }
            
            $data .= '
            ';
            $data .= $line['type'];
            $data .= ';PRIORITY;';
            // CUST_EKP
            $data .= '195974788;';
            // AWB
            $data .= ';';
            // Registered barcode
            $data .= ';';
            // Customer Ref
            $data .= $line['id'].'-'.$box.';';
            // Name
            $data .= utf8_encode( $line['name'] ).';'; // HERE
            // Phone
            $data .= $line['phone'].';;';
            // Email
            $data .= $line['email'].';';
            // Address Line 1
            $data .= utf8_encode( $line['address_1'] ).';';
            // Address Line 2
            $data .= utf8_encode( $line['address_2'] ).';';
            // Company (Address Line 3)
            $data .= utf8_encode( $line['company'] ).';';
            // Address City
            $data .= utf8_encode( $line['city'] ).';';
            // Address State
            $data .= utf8_encode( $line['state'] ).';';
            // Address Post Code
            $data .= utf8_encode( $line['post_code'] ).';';
            // Address Country
            $data .= $line['country'].';';
            // Weight
            $data .= $line['weight'].';';
            // Currency
            $data .= $line['currency'].';';
            // Content Type
            $data .= 'SALE_GOODS;';
            // Declared Content Amount
            $data .= '1;';
            // Declared Content Amount
            $data .= 'SLEEVED CARDS;';
            // Declared Content Weight
            $data .= $line['weight'].';';
            // Declared Content Value
            $data .= $line['total'].';';
            // Declared Content HS CODE
            $data .= ';';
            // Declared Content Value
            $data .= 'NL;';
            $data .= ';;;;;;;;;;;;;;;;;;;;;;;;';
            // TOTAL VALUE
            $data .= $line['total'].';';
            // Return Label
            $data .= '1';
            $count++;
        }
        
        $this->success( [
                            'success' => 1,
                            'data'    => $data,
                        ] );
    }
    
}