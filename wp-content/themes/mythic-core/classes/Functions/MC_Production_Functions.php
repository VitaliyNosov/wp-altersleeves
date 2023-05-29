<?php

namespace Mythic_Core\Functions;

use MC_Data_Settings;
use Mythic_Core\System\MC_Access;
use Mythic_Core\Users\MC_Wp_User;
use Mythic_Core\Utils\MC_Dates;
use wrapi\slack\slack;

/**
 * Class MC_Production_Functions
 *
 * @package Mythic_Core\Production
 */
class MC_Production_Functions {
    
    const DEFAULT = 'US';
    
    /**
     * @param int $user_id
     *
     * @return string
     */
    public static function user( $user_id = 0 ) : string {
        if( empty( $user_id ) ) $user_id = MC_User_Functions::id();
        $user = new MC_WP_User( $user_id );
        if( empty( $user ) ) return self::DEFAULT;
        $user_id = $user->getUser()->ID;
        
        return in_array( $user_id, self::getUSUsers() ) ? 'US' : 'NL';
    }
    
    /**
     * @return int[]
     */
    public static function getUSUsers() : array {
        return [ 939, 2661, 6967 ];
    }
    
    /**
     * @param int $site_id
     */
    public static function dailyOrderReports( $site_id = 1 ) {
        $yesterday_approvals = get_option( 'mc_approvals', [] );
        $args                = [
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => [ 'verify', 'internal_verify' ],
            'author__not_in' => [ 1341 ],
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'fields'         => 'ids',
        ];
        $args['meta_query']  =
            [
                'key'     => 'mc_lo_res_combined_jpg',
                'compare' => '!=',
                'value'   => '',
            ];
        $todays_approvals    = get_posts( $args );
        
        foreach( $yesterday_approvals as $key => $yesterday_approval ) {
            if( !in_array( $yesterday_approval, $todays_approvals ) ) continue;
            unset( $yesterday_approvals[ $key ] );
        }
        $approvals        = count( $yesterday_approvals );
        $todays_approvals = get_posts( $args );
        update_option( 'mc_approvals', $todays_approvals );
        
        $site_id = ( empty( $site_id ) && is_multisite() ) || !is_int( $site_id ) ? get_current_blog_id() : $site_id;
        if( !MC_Access::live() || $site_id != 1 ) return;
        if( date( 'D' ) == 'Mon' || date( 'D' ) == 'Sun' ) return;
        $date      = MC_Dates::yesterday();
        $us_orders = MC_Woo_Order_Functions::getOrderCountPrintedForPeriod( [ 'start_date' => $date, 'office' => 'US' ] );
        $nl_orders = MC_Woo_Order_Functions::getOrderCountPrintedForPeriod( [ 'start_date' => $date, 'office' => 'NL' ] );

	    $date      = MC_Dates::currentDate();
	    $us_orders_today = MC_Woo_Order_Functions::getOrderCountPrintedForPeriod( [ 'start_date' => $date, 'office' => 'US' ] );
	    $nl_orders_today = MC_Woo_Order_Functions::getOrderCountPrintedForPeriod( [ 'start_date' => $date, 'office' => 'NL' ] );

	    $message = '
        *Production Report - *'.$date.'*
        
        *US OFFICE:* '.$us_orders['orders'].' - '.$us_orders['sleeves'].' sleeves
        *NL OFFICE:* '.$nl_orders['orders'].' - '.$nl_orders['sleeves'].' sleeves
        
        *US OFFICE Today:* '.$us_orders_today['orders'].' - '.$us_orders_today['sleeves'].' sleeves
        *NL OFFICE TodayL* '.$nl_orders_today['orders'].' - '.$nl_orders_today['sleeves'].' sleeves
        
        Approvals: '.$approvals.' with '.count( $todays_approvals ).' remaining';

        $slack = new slack( MC_Data_Settings::value( 'slack_token', '' ) );
        $slack->chat->postMessage( [
                                       "channel" => "#production",
                                       "text"    => $message,
                                   ]
        );
    }
    
}