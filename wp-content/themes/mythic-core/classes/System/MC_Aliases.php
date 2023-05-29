<?php

namespace Mythic_Core\System;

/**
 * Class MC_Aliases
 *
 * @package Mythic_Core\System
 *
 */
class MC_Aliases {
    
    /**
     * MC_Aliases constructor.
     */
    public function __construct() {
        // Display
        class_alias( 'Mythic_Core\Display\MC_Forms', 'MC_Forms' );
        class_alias( 'Mythic_Core\Display\MC_Render', 'MC_Render' );
        class_alias( 'Mythic_Core\Display\MC_Table_Flex', 'MC_Table_Flex' );
        class_alias( 'Mythic_Core\Display\MC_Tabs_With_Panes', 'MC_Tabs_With_Panes' );
        class_alias( 'Mythic_Core\Display\MC_Template_Parts', 'MC_Template_Parts' );
        // Functions
        class_alias( 'Mythic_Core\Functions\MC_Ajax_Functions', 'MC_Ajax_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Comment_Functions', 'MC_Comment_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Currency_Functions', 'MC_Currency_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Design_Functions', 'MC_Design_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Invoice_Functions', 'MC_Invoice_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Licensing_Functions', 'MC_Licensing_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Mask_Cutter_Functions', 'MC_Mask_Cutter_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Mtg_Card_Functions', 'MC_Mtg_Card_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Mtg_Printing_Functions', 'MC_Mtg_Printing_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Mtg_Set_Functions', 'MC_Mtg_Set_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Mythic_Frames_Functions', 'MC_Mythic_Frames_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Production_Functions', 'MC_Production_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Search_Functions', 'MC_Search_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Business_Costs_Functions', 'MC_Transaction_Business_Costs_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Contracted_Fee_Functions', 'MC_Transaction_Contracted_Fee_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Discount_Functions', 'MC_Transaction_Discount_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Fee_Functions', 'MC_Transaction_Fee_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Functions', 'MC_Transaction_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Miscellaneous_Functions', 'MC_Transaction_Miscellaneous_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Order_Functions', 'MC_Transaction_Order_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Referral_Fee_Functions', 'MC_Transaction_Referral_Fee_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Refund_Functions', 'MC_Transaction_Refund_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Royalty_Functions', 'MC_Transaction_Royalty_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Sale_Functions', 'MC_Transaction_Sale_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Tax_Functions', 'MC_Transaction_Tax_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Transaction_Withdrawal_Functions', 'MC_Transaction_Withdrawal_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_User_Functions', 'MC_User_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Withdrawal_Functions', 'MC_Withdrawal_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Woo_Cart_Functions', 'MC_Woo_Cart_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Woo_Cart_Item_Functions', 'MC_Woo_Cart_Item_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Woo_Coupon_Functions', 'MC_Woo_Coupon_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Woo_Customer_Functions', 'MC_Woo_Customer_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Woo_Order_Functions', 'MC_Woo_Order_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Woo_Order_Item_Functions', 'MC_Woo_Order_Item_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Woo_Product_Functions', 'MC_Woo_Product_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_WP_Post_Functions', 'MC_WP_Post_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Alter_Functions', 'MC_Alter_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Artist_Functions', 'MC_Artist_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Backer_Functions', 'MC_Backer_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Giftcard_Functions', 'MC_Giftcard_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Product_Functions', 'MC_Product_Functions' );
        class_alias( 'Mythic_Core\Functions\MC_Royalty_Functions', 'MC_Royalty_Functions' );
        // Objects
        class_alias( 'Mythic_Core\Objects\MC_Campaign', 'MC_Campaign' );
        class_alias( 'Mythic_Core\Objects\MC_Campaign_Credit', 'MC_Campaign_Credit' );
        class_alias( 'Mythic_Core\Objects\MC_Giftcard', 'MC_Giftcard' );
        class_alias( 'Mythic_Core\Objects\MC_Log', 'MC_Log' );
        class_alias( 'Mythic_Core\Objects\MC_Mtg_Card', 'MC_Mtg_Card' );
        class_alias( 'Mythic_Core\Objects\MC_Mtg_Printing', 'MC_Mtg_Printing' );
        class_alias( 'Mythic_Core\Objects\MC_Mtg_Set', 'MC_Mtg_Set' );
        class_alias( 'Mythic_Core\Objects\MC_Shortlink', 'MC_Shortlink' );
        class_alias( 'Mythic_Core\Objects\MC_Team', 'MC_Team' );
        class_alias( 'Mythic_Core\Objects\MC_Transaction', 'MC_Transaction' );
        class_alias( 'Mythic_Core\Objects\MC_User', 'MC_User' );
        class_alias( 'Mythic_Core\Objects\MC_Withdrawal', 'MC_Withdrawal' );
        class_alias( 'Mythic_Core\Objects\MC_Woo_Cart_Item', 'MC_Woo_Cart_Item' );
        class_alias( 'Mythic_Core\Objects\MC_Woo_Order', 'MC_Woo_Order' );
        class_alias( 'Mythic_Core\Objects\MC_WP_Post', 'MC_WP_Post' );
        // Objects
        class_alias( 'Mythic_Core\Objects\MC_Alter', 'MC_Alter' );
        class_alias( 'Mythic_Core\Objects\MC_Design', 'MC_Design' );
        class_alias( 'Mythic_Core\Objects\MC_Ranked_Sale', 'MC_Ranked_Sale' );
        class_alias( 'Mythic_Core\Objects\MC_Royalty', 'MC_Royalty' );
        // Settings
        class_alias( 'Mythic_Core\Settings\MC_Data_Settings', 'MC_Data_Settings' );
        class_alias( 'Mythic_Core\Settings\MC_Run_Commands', 'MC_Run_Commands' );
        class_alias( 'Mythic_Core\Settings\MC_Site_Settings', 'MC_Site_Settings' );
        // System
        class_alias( 'Mythic_Core\System\MC_Access', 'MC_Access' );
        class_alias( 'Mythic_Core\System\MC_Actions', 'MC_Actions' );
        class_alias( 'Mythic_Core\System\MC_Admin_Bar', 'MC_Admin_Bar' );
        class_alias( 'Mythic_Core\System\MC_Cookies', 'MC_Cookies' );
        class_alias( 'Mythic_Core\System\MC_Crons', 'MC_Crons' );
        class_alias( 'Mythic_Core\System\MC_Enqueue', 'MC_Enqueue' );
        class_alias( 'Mythic_Core\System\MC_Filters', 'MC_Filters' );
        class_alias( 'Mythic_Core\System\MC_Nav', 'MC_Nav' );
        class_alias( 'Mythic_Core\System\MC_Post_Types', 'MC_Post_Types' );
        class_alias( 'Mythic_Core\System\MC_Redirects', 'MC_Redirects' );
        class_alias( 'Mythic_Core\System\MC_Scripts', 'MC_Scripts' );
        class_alias( 'Mythic_Core\System\MC_Settings', 'MC_Settings' );
        class_alias( 'Mythic_Core\System\MC_Social', 'MC_Social' );
        class_alias( 'Mythic_Core\System\MC_Statuses', 'MC_Statuses' );
        class_alias( 'Mythic_Core\System\MC_Styles', 'MC_Styles' );
        class_alias( 'Mythic_Core\System\MC_Taxonomies', 'MC_Taxonomies' );
        class_alias( 'Mythic_Core\System\MC_WP', 'MC_WP' );
        // Utils
        class_alias( 'Mythic_Core\Utils\MC_Assets', 'MC_Assets' );
        class_alias( 'Mythic_Core\Utils\MC_Colors', 'MC_Colors' );
        class_alias( 'Mythic_Core\Utils\MC_Content', 'MC_Content' );
        class_alias( 'Mythic_Core\Utils\MC_Dates', 'MC_Dates' );
        class_alias( 'Mythic_Core\Utils\MC_Database', 'MC_Database' );
        class_alias( 'Mythic_Core\Utils\MC_Debug', 'MC_Debug' );
        class_alias( 'Mythic_Core\Utils\MC_Files', 'MC_Files' );
        class_alias( 'Mythic_Core\Utils\MC_Geo', 'MC_Geo' );
        class_alias( 'Mythic_Core\Utils\MC_Images', 'MC_Images' );
        class_alias( 'Mythic_Core\Utils\MC_Inline', 'MC_Inline' );
        class_alias( 'Mythic_Core\Utils\MC_Pagination', 'MC_Pagination' );
        class_alias( 'Mythic_Core\Utils\MC_Server', 'MC_Server' );
        class_alias( 'Mythic_Core\Utils\MC_System', 'MC_System' );
        class_alias( 'Mythic_Core\Utils\MC_Url', 'MC_Url' );
        class_alias( 'Mythic_Core\Utils\MC_Vars', 'MC_Vars' );
        // Vendor
        class_alias( 'Mythic_Core\Utils\MC_Facebook', 'MC_Facebook' );
        class_alias( 'Mythic_Core\Utils\MC_File_Locations', 'MC_File_Locations' );
        class_alias( 'Mythic_Core\Utils\MC_Opendrive', 'MC_Opendrive' );
        class_alias( 'Mythic_Core\Utils\MC_Scryfall', 'MC_Scryfall' );
        class_alias( 'Mythic_Core\Utils\MC_Sendinblue', 'MC_Sendinblue' );
        class_alias( 'Mythic_Core\Utils\MC_Wise', 'MC_Wise' );
        class_alias( 'Mythic_Core\Utils\MC_Woo', 'MC_Woo' );
    }
    
}
