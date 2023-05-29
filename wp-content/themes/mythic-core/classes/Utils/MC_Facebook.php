<?php

namespace Mythic_Core\Utils;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;
use MC_Vars;
use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\Settings\MC_Data_Settings;
use Mythic_Core\System\MC_Crons;

/**
 * Class MC_Facebook
 *
 * @package Mythic_Core\Utils
 */
class MC_Facebook {
    
    /**
     * @param $order_id
     */
    public static function triggerConversionTracking( $order_id ) {
        MC_Crons::single( 'mc_facebook_conversion_tracking', [ $order_id ], time() + 10 );
    }
    
    /**
     * @param $order_id
     */
    public static function conversionTracking( $order_id ) {
        if( MC_User_Functions::isAdmin() ) return;
        $access_token = MC_Data_Settings::value( 'facebook_access_token' );
        $pixel_id     = MC_Data_Settings::value( 'facebook_pixel_id' );
        if( empty( $access_token ) || empty( $pixel_id ) || !function_exists( 'WC' ) ) return;
        if( is_array( $order_id ) ) $order_id = $order_id[0];
        if( is_array( $order_id ) ) $order_id = $order_id[0];
        
        $order = wc_get_order( $order_id );
        if( empty( $order ) ) return;
        
        $email      = $order->get_billing_email() ?? '';
        $first_name = $order->get_shipping_first_name() ?? '';
        $last_name  = $order->get_shipping_last_name() ?? '';
        $city       = $order->get_shipping_city() ?? '';
        $total      = $order->get_total() ?? 0;
        $currency   = $order->get_currency() ?? 'usd';
        $country    = $order->get_shipping_country() ?? '';
        $post_code  = $order->get_shipping_postcode() ?? '';
        $phone      = $order->get_billing_phone() ?? '';
        
        $api = Api::init( null, null, $access_token );
        $api->setLogger( new CurlLogger() );
        
        $user_data = new UserData();
        $user_data->setClientIpAddress( $_SERVER['REMOTE_ADDR'] );
        $user_data->setClientUserAgent( $_SERVER['HTTP_USER_AGENT'] );
        if( !empty( $email ) ) $user_data->setEmail( $email );
        if( !empty( $first_name ) ) $user_data->setFirstName( $first_name );
        if( !empty( $last_name ) ) $user_data->setLastName( $last_name );
        if( !empty( $city ) ) $user_data->setCity( $city );
        if( !empty( $country ) ) $user_data->setCountryCode( $country );
        if( !empty( $phone ) ) $user_data->setPhone( $phone );
        if( !empty( $post_code ) ) $user_data->setZipCode( $post_code );
        
        $custom_data = new CustomData();
        $custom_data->setCurrency( $currency );
        $custom_data->setValue( $total );
        
        $event = ( new Event() )->setEventName( 'Purchase' )->setEventTime( time() )->setEventSourceUrl( get_home_url() )->setUserData( $user_data )->setCustomData( $custom_data )->setEventId( $order->get_id() )->setActionSource( ActionSource::WEBSITE );
        
        $events = [];
        array_push( $events, $event );
        
        $request  = ( new EventRequest( $pixel_id ) )->setEvents( $events );
        $response = $request->execute();
        if( empty( $response->getEventsReceived() ) ) return;
        update_post_meta( $order_id, 'mc_facebook_conversion', 1 );
    }
    
    public static function pixel() {
        $script = MC_Data_Settings::value( 'facebook_pixel' );
        if( empty( $script ) || !is_string( $script ) ) return;
        if( !MC_Vars::stringContains( $script, 'script>' ) ) return;
        echo $script;
    }
    
}