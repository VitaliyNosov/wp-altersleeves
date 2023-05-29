<?php

namespace Mythic_Retail\System;

/**
 * Class MR_Crons
 *
 * @package Mythic_Retail\System
 */
class MR_Crons {

    /**
     * MR_Crons constructor.
     */
    public function __construct() {
        return;
        $this->monthly();
        $this->weekly();
        $this->daily();
        $this->hourly();
    }

    /** Hourly Crons */
    public function hourly() {
    }

    /**
     * Daily crons - GMT Firing order
     */
    public function daily() {
    }

    /**
     * Weekly crons - GMT Firing order
     */
    public function weekly() {
    }

    public static function monthly() {
    }

}