<?php

namespace Mythic\Objects\Kickstarter;

use MC2_WP;
use MC2_Order_Functions;
use Mythic\Abstracts\MC2_Object;

/**
 * Class MC2_Backer
 * @package Mythic\Objects
 */
class MC2_MC_Backer extends MC2_Object {

    const TABLE_NAME                  = 'as_backers';
    const GLOBAL_OPTION               = 'backer_table';
    const ROYALTY_INDIVIDUAL          = 1.2;
    const ROYALTY_3BL                 = 6;
    const ROYALTY_4S                  = 8;
    const ROYALTY_5PAN                = 12.5;
    const ROYALTY_6COM                = 15;
    const ROYALTY_COMMISSION_1COMDDX4 = 40;
    const ROYALTY_COMMISSION_1COMPX4  = 60;
    const ROYALTY_COMMISSION_4COMPX4  = 240;
    const DB_WALL_OF_HEROES_VISIBLE   = 'wall_of_heroes_visible';
    const DB_WALL_OF_HEROES_POSITION  = 'wall_of_heroes_position';
    const DB_WALL_OF_HEROES_NAME      = 'wall_of_heroes_name';

    protected $id;
    protected $email;
    protected $access;
    protected $name;
    protected $addressLine1;
    protected $addressLine2;
    protected $addressCity;
    protected $addressState;
    protected $addressCountry;
    protected $phone;
    protected $individualSleeves;
    protected $remainingSleeves;
    protected $redeemedSleeves;
    protected $commissionType;
    protected $set3bl;
    protected $set4s;
    protected $set5pan;
    protected $set6com;
    protected $setSnapbolt;
    protected $setsRedeemed;
    protected $orders;
    protected $ordersSleevesTotal;
    protected $ordersSleevesIndividual;
    protected $ordersSleevesSets;
    protected $royaltiesIndividuals;
    protected $royaltiesSets;
    protected $royaltiesCommissions;
    protected $royaltiesTotal;
    protected $wallVisible;
    protected $wallPosition;
    protected $wallName;

    /**
     * Backer constructor.
     */
    public function __construct() {

        global $wpdb;
        $table_name = $wpdb->base_prefix.self::TABLE_NAME;
        $query      = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
        if( !$wpdb->get_var( $query ) == $table_name ) {
            $this->createTable();
        }
    }

    private function createTable() {
        global $wpdb;
        require_once( ABSPATH.'wp-admin/includes/upgrade.php' );

        $tableName = $wpdb->prefix.self::TABLE_NAME;  //get the database table prefix to create my new table

        $sql = "CREATE TABLE $tableName (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `access` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `address_line_1` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `address_line_2` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `address_city` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `address_state` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `address_country` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `phone` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `individual_sleeves` int(11) NOT NULL,
          `remaining_sleeves` int(11) NOT NULL,
          `redeemed_sleeves` int(11) NOT NULL,
          `commission_type` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `set_3bl` int(11) DEFAULT NULL,
          `set_4s` int(11) DEFAULT NULL,
          `set_5pan` int(11) DEFAULT NULL,
          `set_6com` int(11) DEFAULT NULL,
          `set_snapbolt` int(11) DEFAULT NULL,
          `sets_redeemed` int(11) DEFAULT NULL,
          `orders` longtext COLLATE utf8_unicode_ci NOT NULL,
          `orders_sleeves_total` int(11) NOT NULL,
          `orders_sleeves_individual` int(11) NOT NULL,
          `orders_sleeves_sets` int(11) NOT NULL,
          `royalties_sleeves` float NOT NULL DEFAULT '0',
          `royalties_sets` float NOT NULL DEFAULT '0',
          `royalties_commissions` float NOT NULL DEFAULT '0',
          `royalties_total` float NOT NULL DEFAULT '0',
          `wall_of_heroes_visible` int(1) NOT NULL,
          `wall_of_heroes_position` int(1) NOT NULL,
          `wall_of_heroes_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1305 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

        dbDelta( $sql );
        update_option( self::GLOBAL_OPTION, 1 );

        $this->updateBackerData();
    }

    public function updateBackerData() {
        global $wpdb;

        /** @var $tableName */
        $tableName = $wpdb->prefix.self::TABLE_NAME;

        /** Get all from table */
        $query   = "SELECT * FROM $tableName";
        $results = $wpdb->get_results( $query );
        $emails  = [];
        // Create array of emails with IDs as key
        foreach( $results as $result ) {
            $emails[ $result->id ] = $result->email;
        }

        $args           = [
            'post_type'      => 'backer',
            'posts_per_page' => -1,
        ];
        $query          = new WP_Query( $args );
        $queriedBackers = [];
        if( $query->have_posts() ) : while( $query->have_posts() ) : $query->the_post();
            global $wpdb;
            $idBacker         = get_the_ID();
            $queriedBackers[] = $email = trim( get_the_title( $idBacker ) );

            // Personal Info
            $name           = addslashes( MC2_WP::meta( 'name', $idBacker ) );
            $addressLine1   = addslashes( MC2_WP::meta( 'address_line_1', $idBacker ) );
            $addressLine2   = addslashes( MC2_WP::meta( 'address_line_2', $idBacker ) );
            $addressCity    = addslashes( MC2_WP::meta( 'address_city', $idBacker ) );
            $addressState   = addslashes( MC2_WP::meta( 'address_state', $idBacker ) );
            $addressCountry = addslashes( MC2_WP::meta( 'address_country', $idBacker ) );
            $phone          = addslashes( MC2_WP::meta( 'phone', $idBacker ) );

            /* Access */
            $access  = 'normal';
            $access1 = self::array_checker( MC2_WP::meta( 'access1', $idBacker ) );
            $access2 = self::array_checker( MC2_WP::meta( 'access2', $idBacker ) );
            if( $access1 == 1 || $access1 ) $access = 'super_haste';
            if( $access2 == 1 || $access2 ) $access = 'haste';

            /* Non calculating */
            $set3bl      = MC2_WP::meta( 'set3bl', $idBacker );
            $set3bl      = $set3bl != '' ? $set3bl : 0;
            $set4s       = MC2_WP::meta( 'set4s', $idBacker );
            $set4s       = $set4s != '' ? $set4s : 0;
            $set5pan     = MC2_WP::meta( 'set5pan', $idBacker );
            $set5pan     = $set4s != '' ? $set5pan : 0;
            $set6com     = MC2_WP::meta( 'set6com', $idBacker );
            $set6com     = $set6com != '' ? $set6com : 0;
            $setSnapbolt = self::array_checker( MC2_WP::meta( 'setsnapbolt', $idBacker ) );
            $setSnapbolt = $setSnapbolt != '' ? $setSnapbolt : 0;

            /* Store Stuff */
            $order_ids           = [];
            $royaltiesIndividual = 0;
            $royaltiesSets       = 0;
            $royaltiesTotal      = 0;

            $commission          = '';
            $royaltiesCommission = 0;
            $commissionTypes     = [
                '1comddx4',
                '1compx4',
                '4compx4',
            ];
            foreach( $commissionTypes as $commissionType ) {
                $type = self::array_checker( MC2_WP::meta( $commissionType, $idBacker ) );
                if( $type == 1 || $type ) {
                    $commission          = $commissionType;
                    $royaltiesCommission = $this->getRoyaltyCommissions( $commissionType );
                    break;
                }
            }

            $setsRedeemed      = self::array_checker( MC2_WP::meta( 'as_sets_redeemed', $idBacker ) );
            $setsRedeemed      = $setsRedeemed != '' ? $setsRedeemed : 0;
            $sleevesIndividual = 0;
            $quantities        = [ 1, 2, 4, 6, 8, 10, 12, 30, 40, 60, 75, 100 ];
            //Individual Sleeves
            foreach( $quantities as $quantity ) {
                $sleeves           = MC2_WP::meta( 'sleevesx'.$quantity, $idBacker );
                $sleevesIndividual = ( isset( $sleeves ) && $sleeves != '' ? $sleevesIndividual + ( $sleeves * $quantity ) : $sleevesIndividual );
            }
            $sleevesRemaining = $sleevesIndividual;
            $sleevesRedeemed  = 0;

            $orderString            = '';
            $orderSleevesTotal      = 0;
            $orderSleevesIndividual = 0;
            $orderSleevesSets       = 0;
            $user                   = get_user_by( 'email', $email );
            if( $user != NULL ) {
                $idUser = $user->ID;
                if( $setsRedeemed == 1 ) {
                    $orders     = get_posts( [
                        'numberposts' => -1,
                        'meta_key'    => '_customer_user',
                        'meta_value'  => $idUser,
                        'post_type'   => wc_get_order_types(),
                        'post_status' => array_keys( wc_get_order_statuses() ),
                    ] );
                    $last       = count( $orders );
                    $orderCount = 1;
                    foreach( $orders as $order ) {
                        $order_id    = $order->ID;
                        $order_ids[] = $order_id;
                        if( $orderCount != $last ) $orderString .= ', ';
                        $orderCount++;

                        $order = wc_get_order( $order_id );
                        if( $order->get_payment_method() != 'wdc_woo_credits' ) continue;

                        echo $order_id;
                        echo '<br>';
                        foreach( $order->get_items() as $item_id => $item ) {
                            $idProduct = $item['product_id'];

                            // If collection
                            if( MC2_Order_Functions::productCollection( $item ) ) {
                                $bto              = MC2_WP::meta( '_bto_data', $idBacker, 'user' );
                                $setNumbers       = count( $bto ) * $item['quantity'];
                                $orderSleevesSets = ( $setNumbers );
                                $royalty          = $this->getRoyaltyValue( $setNumbers ) * $item['quantity'];
                                $royaltiesSets    = $royaltiesSets + $royalty;
                            } // If item within collection
                            else {
                                if( MC2_Order_Functions::productCollectionChild( $item, $order ) ) {
                                    $royalty = 0;
                                } // If individual sleeve within collection
                                else {
                                    $orderSleevesIndividual = $orderSleevesIndividual + $item['quantity'];
                                    $royalty                = $this->getRoyaltyValue() * $item['quantity'];
                                    $royaltiesIndividual    = $royaltiesIndividual + $royalty;
                                }
                            }

                            $royaltiesTotal = $royaltiesTotal + $royalty;
                        }
                        $orderSleevesTotal = $orderSleevesIndividual + $orderSleevesSets;
                    }
                    $sleevesRemaining = MC2_WP::meta( '_download_credits', $idUser, 'user' );
                    $sleevesRedeemed  = $sleevesIndividual - $sleevesRemaining;
                }
            }
            $order_ids = !empty( $order_ids ) ? serialize( $order_ids ) : '';

            $command = "UPDATE";
            if( !in_array( $email, $emails ) ) $command = "INSERT INTO";

            $sql = $command;
            $sql .= " $tableName";
            $sql .= " SET email = '$email',";
            $sql .= " access = '$access',";
            $sql .= " name = '$name',";
            $sql .= " address_line_1 = '$addressLine1',";
            $sql .= " address_line_2 = '$addressLine2',";
            $sql .= " address_city = '$addressCity',";
            $sql .= " address_state = '$addressState',";
            $sql .= " address_country = '$addressCountry',";
            $sql .= " phone = '$phone',";
            $sql .= " individual_sleeves = '$sleevesIndividual',";
            $sql .= " remaining_sleeves = '$sleevesRemaining',";
            $sql .= " redeemed_sleeves = '$sleevesRedeemed',";
            $sql .= " commission_type = '$commission',";
            $sql .= " set_3bl = '$set3bl',";
            $sql .= " set_4s = '$set4s',";
            $sql .= " set_5pan = '$set5pan',";
            $sql .= " set_6com = '$set6com',";
            $sql .= " set_snapbolt = '$setSnapbolt',";
            $sql .= " sets_redeemed = '$setsRedeemed',";
            $sql .= " orders = '$order_ids',";
            $sql .= " orders_sleeves_total = '$orderSleevesTotal',";
            $sql .= " orders_sleeves_individual = '$orderSleevesIndividual',";
            $sql .= " orders_sleeves_sets = '$orderSleevesSets',";
            $sql .= " royalties_sleeves = '$royaltiesIndividual',";
            $sql .= " royalties_sets = '$royaltiesSets',";
            $sql .= " royalties_commissions = '$royaltiesCommission',";
            $sql .= " royalties_total = '$royaltiesTotal'";
            if( in_array( $email, $emails ) ) $sql .= " WHERE email='$email'";

            $wpdb->query( $sql );
        endwhile; endif;

        foreach( $emails as $id => $email ) {
            if( !in_array( $email, $queriedBackers ) ) {
                $wpdb->delete( $tableName, [ 'id' => $id ] );
            }
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function array_checker( $value ) {
        if( is_array( $value ) ) {
            $value = $value[0];
        }
        return $value;
    }

    /**
     * @param string $key
     * @return int
     */
    public function getRoyaltyCommissions( $key = '' ) {
        if( $key == '' ) return 0;
        switch( $key ) {
            case $key = '1comddx4';
                return self::ROYALTY_COMMISSION_1COMDDX4;
                break;
            case $key = '1compx4';
                return self::ROYALTY_COMMISSION_1COMPX4;
                break;
            case $key = '4compx4';
                return self::ROYALTY_COMMISSION_4COMPX4;
                break;
        }
        return 0;
    }

    /**
     * @param int $quantity
     * @return float|int
     */
    public function getRoyaltyValue( $quantity = 1 ) {
        switch( $quantity ) {
            case $quantity = 1;
                return self::ROYALTY_INDIVIDUAL;
                break;
            case $quantity = 3;
                return self::ROYALTY_3BL;
                break;
            case $quantity = 4;
                return self::ROYALTY_4S;
                break;
            case $quantity = 5;
                return self::ROYALTY_5PAN;
                break;
            case $quantity = 6;
                return self::ROYALTY_6COM;
                break;
        }
        return 0;
    }

    /**
     * @return bool
     */
    public static function canProgress() {

        if( !is_user_logged_in() ) return true;
        if( !self::roleBacker() ) return true;

        $idBacker     = self::getObjectIdFromActiveUser();
        $setsRedeemed = MC2_WP::meta( 'as_sets_redeemed', wp_get_current_user()->ID, 'user' );

        if( $setsRedeemed != 1 ) {

            $setChecks = [
                [ 'key' => 'set3bl', 'cat' => 26 ],
                [ 'key' => 'set4s', 'cat' => 27 ],
                [ 'key' => 'set5pan', 'cat' => 28 ],
                [ 'key' => 'set6com', 'cat' => 29 ],
                [ 'key' => 'setsnapbolt', 'cat' => 31 ],
            ];

            $userSets = [];
            foreach( $setChecks as $setCheck ) {
                $setCount = MC2_WP::meta( $setCheck['key'], $idBacker );
                if( is_array( $setCount ) ) {
                    $setCount = $setCount[0];
                }
                if( !is_numeric( $setCount ) ) $setCount = 0;
                $userSets[ $setCheck['cat'] ] = $setCount;
            }

            $cartItems = WC()->cart->get_cart_contents();
            foreach( $cartItems as $cartItem ) {
                $cartItemId  = $cartItem['product_id'];
                $productType = wp_get_post_terms( $cartItemId, 'product_type' )[0]->name;
                if( $productType != 'composite' ) {
                    continue;
                }
                $setType              = wp_get_post_terms( $cartItemId, 'set_type' )[0]->term_id;
                $quantity             = $cartItem['quantity'];
                $userSets[ $setType ] = $userSets[ $setType ] - $quantity;
            }
            foreach( $userSets as $userSet ) {
                if( $userSet != 0 || $userSet < 0 ) return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public static function roleBacker() {
        if( !is_user_logged_in() ) return false;
        if( MC2_User_Functions::isAdmin() ) return true;
        $userEmail = wp_get_current_user()->user_email;
        if( get_page_by_title( $userEmail, ARRAY_A, 'backer' ) != NULL ) return true;
        if( MC2_WP::meta( '_download_credits', wp_get_current_user()->ID, 'user' ) > 0 ) return true;
        return false;
    }

    /**
     * @return bool|int
     */
    public static function getObjectIdFromActiveUser() {
        if( !is_user_logged_in() ) return false;
        $userEmail = wp_get_current_user()->user_email;
        $backer    = get_page_by_title( $userEmail, OBJECT, 'backer' );
        if( $backer == NULL ) return false;
        return $backer->ID;
    }

    /**
     * @return int
     */
    public static function remainingSingleCredits(): int {
        if( !is_user_logged_in() ) return 0;
        if( !self::roleBacker() ) return 0;
        $remainingCredits = MC2_WP::meta( '_download_credits', wp_get_current_user()->ID, 'user' );
        if( $remainingCredits > 0 ) return $remainingCredits;
        return 0;
    }

    /**
     * @return array|object|null
     */
    public function getTopHeroes() {
        global $wpdb;

        /** @var $tableName */
        $tableName = $wpdb->prefix.self::TABLE_NAME;

        /** Get all from table */
        $query   = "SELECT * FROM $tableName WHERE access = 'super_haste' AND ".self::DB_WALL_OF_HEROES_VISIBLE." = 1";
        $results = $wpdb->get_results( $query );
        if( $results == NULL ) return [];
        return $results;
    }

    /**
     * @return array|object|null
     */
    public function getNormalHeroes() {
        global $wpdb;

        /** @var $tableName */
        $tableName = $wpdb->prefix.self::TABLE_NAME;

        /** Get all from table */
        $query = "SELECT * FROM $tableName WHERE access != 'super_haste' AND ".self::DB_WALL_OF_HEROES_VISIBLE." = 1";
        $results = $wpdb->get_results( $query );
        if( $results == NULL ) return [];
        return $results;
    }

    /**
     * @return bool|object
     */
    public function saveBacker() {
        global $wpdb;
        if( $this->getId() == '' || empty( $this->getId() ) ) return false;

        $id                        = $this->getId();
        $email                     = $this->getEmail();
        $access                    = $this->getAccess();
        $name                      = $this->getName();
        $addressLine1              = $this->getAddressLine1();
        $addressLine2              = $this->getAddressLine2();
        $addressCity               = $this->getAddressCity();
        $addressState              = $this->getAddressState();
        $addressCountry            = $this->getAddressCountry();
        $phone                     = $this->getPhone();
        $sleevesIndividual         = $this->getIndividualSleeves();
        $sleevesRemaining          = $this->getRemainingSleeves();
        $sleevesRedeemed           = $this->getRedeemedSleeves();
        $commission                = $this->getCommissionType();
        $set3bl                    = $this->getSet3bl();
        $set4s                     = $this->getSet4s();
        $set5pan                   = $this->getSet5pan();
        $set6com                   = $this->getSet6com();
        $setSnapbolt               = $this->getSetSnapbolt();
        $setsRedeemed              = $this->getSetsRedeemed();
        $orders                    = $this->getOrders();
        $orderSleevesTotal         = $this->getOrdersSleevesTotal();
        $orderSleevesIndividual    = $this->getOrdersSleevesIndividual();
        $orderSleevesSets          = $this->getOrdersSleevesSets();
        $orderRoyaltiesIndividuals = $this->getRoyaltiesIndividuals() ?? '';
        $orderRoyaltiesSets        = $this->getRoyaltiesSets() ?? '';
        $orderRoyaltiesCommissions = $this->getRoyaltiesCommissions() ?? '';
        $orderRoyaltiesTotal       = $this->getRoyaltiesTotal() ?? '';
        $wallVisible               = $this->getWallVisible() ?? '';
        $wallPosition              = $this->getWallPosition() ?? '';
        $wallName                  = $this->getWallName() ?? '';

        $tableName = $wpdb->prefix.self::TABLE_NAME;

        $sql = " UPDATE $tableName";
        $sql .= " SET email = '$email',";
        $sql .= " access = '$access',";
        $sql .= " name = '$name',";
        $sql .= " address_line_1 = '$addressLine1',";
        $sql .= " address_line_2 = '$addressLine2',";
        $sql .= " address_city = '$addressCity',";
        $sql .= " address_state = '$addressState',";
        $sql .= " address_country = '$addressCountry',";
        $sql .= " phone = '$phone',";
        $sql .= " individual_sleeves = '$sleevesIndividual',";
        $sql .= " remaining_sleeves = '$sleevesRemaining',";
        $sql .= " redeemed_sleeves = '$sleevesRedeemed',";
        $sql .= " commission_type = '$commission',";
        $sql .= " set_3bl = '$set3bl',";
        $sql .= " set_4s = '$set4s',";
        $sql .= " set_5pan = '$set5pan',";
        $sql .= " set_6com = '$set6com',";
        $sql .= " set_snapbolt = '$setSnapbolt',";
        $sql .= " sets_redeemed = '$setsRedeemed',";
        $sql .= " orders = '$orders',";
        $sql .= " orders_sleeves_total = '$orderSleevesTotal',";
        $sql .= " orders_sleeves_individual = '$orderSleevesIndividual',";
        $sql .= " orders_sleeves_sets = '$orderSleevesSets',";
        $sql .= " royalties_sleeves = '$orderRoyaltiesIndividuals',";
        $sql .= " royalties_sets = '$orderRoyaltiesSets',";
        $sql .= " royalties_commissions = '$orderRoyaltiesCommissions',";
        $sql .= " royalties_total = '$orderRoyaltiesTotal',";
        $sql .= " ".self::DB_WALL_OF_HEROES_VISIBLE." = '$wallVisible',";
        $sql .= " ".self::DB_WALL_OF_HEROES_POSITION." = '$wallPosition',";
        $sql .= " ".self::DB_WALL_OF_HEROES_NAME." = '$wallName'";
        $sql .= " WHERE id='$id'";

        $wpdb->query( $sql );

        return $this->getBackerByEmail();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId( $id ) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail( $email ) {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getAccess() {
        return $this->access;
    }

    /**
     * @param string $access
     */
    public function setAccess( $access ) {
        $this->access = $access;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName( $name ) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAddressLine1() {
        return $this->addressLine1;
    }

    /**
     * @param string $addressLine1
     */
    public function setAddressLine1( $addressLine1 ) {
        $this->addressLine1 = $addressLine1;
    }

    /**
     * @return string
     */
    public function getAddressLine2() {
        return $this->addressLine2;
    }

    /**
     * @param string $addressLine2
     */
    public function setAddressLine2( $addressLine2 ) {
        $this->addressLine2 = $addressLine2;
    }

    /**
     * @return string
     */
    public function getAddressCity() {
        return $this->addressCity;
    }

    /**
     * @param string $addressCity
     */
    public function setAddressCity( $addressCity ) {
        $this->addressCity = $addressCity;
    }

    /**
     * @return string
     */
    public function getAddressState() {
        return $this->addressState;
    }

    /**
     * @param string $addressState
     */
    public function setAddressState( $addressState ) {
        $this->addressState = $addressState;
    }

    /**
     * @return string
     */
    public function getAddressCountry() {
        return $this->addressCountry;
    }

    /**
     * @param string $addressCountry
     */
    public function setAddressCountry( $addressCountry ) {
        $this->addressCountry = $addressCountry;
    }

    /**
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone( $phone ) {
        $this->phone = $phone;
    }

    /**
     * @return int
     */
    public function getIndividualSleeves() {
        return $this->individualSleeves;
    }

    /**
     * @param int $individualSleeves
     */
    public function setIndividualSleeves( $individualSleeves ) {
        $this->individualSleeves = $individualSleeves;
    }

    /**
     * @return int
     */
    public function getRemainingSleeves() {
        return $this->remainingSleeves;
    }

    /**
     * @param int $remainingSleeves
     */
    public function setRemainingSleeves( $remainingSleeves ) {
        $this->remainingSleeves = $remainingSleeves;
    }

    /**
     * @return bool
     */
    public function getRedeemedSleeves() {
        return $this->redeemedSleeves;
    }

    /**
     * @param bool $redeemedSleeves
     */
    public function setRedeemedSleeves( $redeemedSleeves ) {
        $this->redeemedSleeves = $redeemedSleeves;
    }

    /**
     * @return string
     */
    public function getCommissionType() {
        return $this->commissionType;
    }

    /**
     * @param string $commissionType
     */
    public function setCommissionType( $commissionType ) {
        $this->commissionType = $commissionType;
    }

    /**
     * @return string
     */
    public function getSet3bl() {
        return $this->set3bl;
    }

    /**
     * @param string $set3bl
     */
    public function setSet3bl( $set3bl ) {
        $this->set3bl = $set3bl;
    }

    /**
     * @return string
     */
    public function getSet4s() {
        return $this->set4s;
    }

    /**
     * @param string $set4s
     */
    public function setSet4s( $set4s ) {
        $this->set4s = $set4s;
    }

    /**
     * @return string
     */
    public function getSet5pan() {
        return $this->set5pan;
    }

    /**
     * @param string $set5pan
     */
    public function setSet5pan( $set5pan ) {
        $this->set5pan = $set5pan;
    }

    /**
     * @return string
     */
    public function getSet6com() {
        return $this->set6com;
    }

    /**
     * @param string $set6com
     */
    public function setSet6com( $set6com ) {
        $this->set6com = $set6com;
    }

    /**
     * @return bool
     */
    public function getSetSnapbolt() {
        return $this->setSnapbolt;
    }

    /**
     * @param bool $setSnapbolt
     */
    public function setSetSnapbolt( $setSnapbolt ) {
        $this->setSnapbolt = $setSnapbolt;
    }

    /**
     * @return string
     */
    public function getSetsRedeemed() {
        return $this->setsRedeemed;
    }

    /**
     * @param string $setsRedeemed
     */
    public function setSetsRedeemed( $setsRedeemed ) {
        $this->setsRedeemed = $setsRedeemed;
    }

    /**
     * @return string
     */
    public function getOrders() {
        return $this->orders;
    }

    /**
     * @param string $orders
     * @Todo Make array
     */
    public function setOrders( $orders ) {
        $this->orders = $orders;
    }

    /**
     * @return string
     */
    public function getOrdersSleevesTotal() {
        return $this->ordersSleevesTotal;
    }

    /**
     * @param int $ordersSleevesTotal
     */
    public function setOrdersSleevesTotal( $ordersSleevesTotal ) {
        $this->ordersSleevesTotal = $ordersSleevesTotal;
    }

    /**
     * @return int
     */
    public function getOrdersSleevesIndividual() {
        return $this->ordersSleevesIndividual;
    }

    /**
     * @param int $ordersSleevesIndividual
     */
    public function setOrdersSleevesIndividual( $ordersSleevesIndividual ) {
        $this->ordersSleevesIndividual = $ordersSleevesIndividual;
    }

    /**
     * @return int
     */
    public function getOrdersSleevesSets() {
        return $this->ordersSleevesSets;
    }

    /**
     * @param int $ordersSleevesSets
     */
    public function setOrdersSleevesSets( $ordersSleevesSets ) {
        $this->ordersSleevesSets = $ordersSleevesSets;
    }

    /**
     * @return float
     */
    public function getRoyaltiesIndividuals() {
        return $this->royaltiesIndividuals;
    }

    /**
     * @param float $royaltiesIndividuals
     */
    public function setRoyaltiesIndividuals( $royaltiesIndividuals ) {
        $this->royaltiesIndividuals = $royaltiesIndividuals;
    }

    /**
     * @return float
     */
    public function getRoyaltiesSets() {
        return $this->royaltiesSets;
    }

    /**
     * @param float $royaltiesSets
     */
    public function setRoyaltiesSets( $royaltiesSets ) {
        $this->royaltiesSets = $royaltiesSets;
    }

    /**
     * @return float
     */
    public function getRoyaltiesCommissions() {
        return $this->royaltiesCommissions;
    }

    /**
     * @param float $royaltiesCommissions
     */
    public function setRoyaltiesCommissions( $royaltiesCommissions ) {
        $this->royaltiesCommissions = $royaltiesCommissions;
    }

    /**
     * @return float
     */
    public function getRoyaltiesTotal() {
        return $this->royaltiesTotal;
    }

    /**
     * @param float $royaltiesTotal
     */
    public function setRoyaltiesTotal( $royaltiesTotal ) {
        $this->royaltiesTotal = $royaltiesTotal;
    }

    /**
     * @return mixed
     */
    public function getWallVisible() {
        return $this->wallVisible;
    }

    /**
     * @param mixed $wallVisible
     */
    public function setWallVisible( $wallVisible ) {
        $this->wallVisible = $wallVisible;
    }

    /**
     * @return mixed
     */
    public function getWallPosition() {
        return $this->wallPosition;
    }

    /**
     * @param mixed $wallPosition
     */
    public function setWallPosition( $wallPosition ) {
        $this->wallPosition = $wallPosition;
    }

    /**
     * @return mixed
     */
    public function getWallName() {
        return $this->wallName;
    }

    /**
     * @param mixed $wallName
     */
    public function setWallName( $wallName ) {
        $this->wallName = $wallName;
    }

    /**
     * @param string $email
     * @return bool|object
     */
    public function getBackerByEmail( $email = '' ) {
        if( $email == '' ) return false;
        global $wpdb;

        /** @var $tableName */
        $tableName = $wpdb->prefix.self::TABLE_NAME;

        /** Get all from table */
        $query   = "SELECT * FROM $tableName WHERE email='$email'";
        $results = $wpdb->get_results( $query );
        if( $results == NULL ) return false;
        $result = $results[0];

        // Set Variables for Instance
        $this->setId( $result->id );
        $this->setEmail( $result->email );
        $this->setAccess( $result->access );
        $this->setName( $result->name );
        $this->setAddressLine1( $result->address_line_1 );
        $this->setAddressLine2( $result->address_line_2 );
        $this->setAddressCity( $result->address_city );
        $this->setAddressState( $result->address_state );
        $this->setAddressCountry( $result->address_country );
        $this->setPhone( $result->phone );
        $this->setIndividualSleeves( $result->individual_sleeves );
        $this->setRemainingSleeves( $result->remaining_sleeves );
        $this->setRedeemedSleeves( $result->redeemed_sleeves );
        $this->setCommissionType( $result->commission_type );
        $this->setSet3bl( $result->set_3bl );
        $this->setSet4s( $result->set_4s );
        $this->setSet5pan( $result->set_5pan );
        $this->setSet6com( $result->set_6com );
        $this->setSetSnapbolt( $result->set_snapbolt );
        $this->setSetsRedeemed( $result->sets_redeemed );
        $this->setOrders( $result->orders );
        $this->setOrdersSleevesTotal( $result->orders_sleeves_total );
        $this->setOrdersSleevesIndividual( $result->orders_sleeves_individual );
        $this->setOrdersSleevesSets( $result->orders_sleeves_sets );
        $this->setRoyaltiesIndividuals( $result->royalties_sleeves );
        $this->setRoyaltiesSets( $result->royalties_sets );
        $this->setRoyaltiesCommissions( $result->royalties_commissions );
        $this->setRoyaltiesTotal( $result->royalties_total );
        $this->setWallVisible( $result->wall_of_heroes_visible );
        $this->setWallPosition( $result->wall_of_heroes_position );
        $this->setWallName( $result->wall_of_heroes_name );

        return $result;
    }

}
