<?php

namespace Mythic_Gaming\System;

use Mythic_Core\Display\MC_Render;
use Mythic_Core\Functions\MC_Mythic_Frames_Functions;

/**
 * Class MG_Actions
 *
 * @package Mythic_Gaming\System
 */
class MG_Actions {

    /**
     * MG_Actions constructor.
     */
    public function __construct() {
        //add_action( 'mc_credit_totals', [ MC_Mythic_Frames_Functions::class, 'updateCreditTotals' ] );
        add_action( 'mc_calculate_production_totals', [ MC_Mythic_Frames_Functions::class, 'update_production_totals' ]);
        add_action( 'mc_calculate_production_payout', [ MC_Mythic_Frames_Functions::class, 'update_production_payout' ]);
        add_action( 'mc_header_cart', [ self::class, 'headerCart' ] );
        add_action( 'mc_header_logo', [ self::class, 'headerLogo' ] );
        add_action( 'mc_header_nav', [ self::class, 'headerNav' ] );
    }

    public static function headerCart() {
        MC_Render::templatePart( 'header/cart' );
    }

    public static function headerLogo() {
        MC_Render::templatePart( 'header/logo' );
    }

    public static function headerNav() {
        MC_Render::templatePart( 'header/nav' );
    }

    public static function promotionProductModal() {
        MC_Render::templatePart( 'store', 'pre-cart' );
    }

}