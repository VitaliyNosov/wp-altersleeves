<?php

namespace Mythic_Gaming\Loader;

use Mythic_Gaming\System\MG_Enqueue;

/**
 * Class MG_Public_Loader
 *
 * @package Mythic_Gaming\Loader
 */
class MG_Public_Loader {

    /**
     * MG_Public_Loader constructor.
     */
    public function __construct() {
        new MG_Enqueue();
    }

}