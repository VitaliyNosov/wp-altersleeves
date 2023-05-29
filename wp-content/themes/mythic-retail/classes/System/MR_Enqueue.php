<?php

namespace Mythic_Retail\System;

/**
 * Class MR_Enqueue
 *
 * @package Mythic_Retail\System
 */
class MR_Enqueue {

    /**
     * MR_Enqueue constructor.
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'add' ], 1 );
    }

    public function add() {
        add_filter( 'mc_styles', [ MR_Styles::class, 'add' ], 10, 1 );
        add_filter( 'mc_scripts', [ MR_Scripts::class, 'add' ], 10, 1 );
    }

}