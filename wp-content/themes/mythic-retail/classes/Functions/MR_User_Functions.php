<?php

namespace Mythic_Retail\Functions;

use MC_User_Functions;
use MC_Woo_Order_Functions;

/**
 * Class MR_User_Functions
 *
 * @package Mythic_Retail\Functions
 */
class MR_User_Functions {

    /**
     * MR_User_Functions constructor.
     */
    public function __construct() {
        add_filter( 'woocommerce_account_menu_items', [ self::class, 'dashboardNavItems' ] );
        add_filter( 'woocommerce_get_endpoint_url', [ self::class, 'dashboardEndpoint' ], 10, 4 );

        add_action( 'gform_after_submission_2', [ self::class, 'storeBusinessDetails' ], 10, 2 );
    }

    /**
     * @return string[]
     */
    public static function dashboardDefaultsToRemove() : array {
        $defaults = [];
        if( !MC_Woo_Order_Functions::userHasPreviouslyPurchased() ) $defaults[] = 'orders';
        return $defaults;
    }

    /**
     * @param $items
     *
     * @return mixed
     */
    public static function dashboardNavItems( $items ) {
        unset( $items['customer-logout'] );
        foreach( $items as $key => $item ) {
            if( in_array( $key, self::dashboardDefaultsToRemove() ) ) {
                unset( $items[ $key ] );
                continue;
            }
        }
        $items['customer-logout'] = 'Logout';

        return $items;
    }

    /**
     * @param $url
     * @param $endpoint
     * @param $value
     * @param $permalink
     *
     * @return mixed|string|void
     */
    public static function dashboardEndpoint( $url, $endpoint, $value, $permalink ) {
        if( $endpoint === 'anyuniquetext124' ) {
            // ok, here is the place for your custom URL, it could be external
            $url = site_url();
        }
        return $url;
    }

    public static function storeBusinessDetails( $entry, $form ) {
        $user_id          = MC_User_Functions::id();
        $details          = [];
        $processed_emails = [];
        $emails           = [];
        foreach( $form['fields'] as $field ) {
            $inputs = $field->get_entry_inputs();
            if( is_array( $inputs ) ) {
                foreach( $inputs as $key => $input ) {
                    $value                   = rgar( $entry, $input['id'] );
                    $details[ $input['id'] ] = $value;
                }
            } else {
                $value                 = rgar( $entry, (string) $field->id );
                $details[ $field->id ] = $value;

                if( $field->id != 7 ) continue;

                $emails = explode( PHP_EOL, $value );
                if( empty( $emails ) ) continue;
                foreach( $emails as $email ) {
                    if( strpos( $email, '@' ) === false ) continue;
                    $email              = strtolower( $email );
                    $email              = str_replace( '@', '_at_', $email );
                    $processed_emails[] = $email;
                }
            }
        }

        update_user_meta( $user_id, 'business_details', $details );
        foreach( $processed_emails as $processed_email ) {
            update_blog_option( 6, $processed_email, $details );
        }

        foreach( $emails as $email ) {
            $user = get_user_by( 'email', $email );
            if( empty( $user ) ) continue;
            update_user_meta( $user->ID, 'business_details', $details );
        }
    }

}