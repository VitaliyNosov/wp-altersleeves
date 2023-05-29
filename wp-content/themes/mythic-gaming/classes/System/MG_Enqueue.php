<?php

namespace Mythic_Gaming\System;

/**
 * Class MG_Enqueue
 *
 * @package Mythic_Gaming\System
 */
class MG_Enqueue {

    /**
     * MG_Enqueue constructor.
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'add' ], 1 );
    }

    public function add() {
        add_filter( 'mc_styles', [ MG_Styles::class, 'add' ], 10, 1 );
        add_filter( 'mc_scripts', [ MG_Scripts::class, 'add' ], 10, 1 );
    }

}