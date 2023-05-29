<?php

namespace Mythic_Retail\Loader;

use Mythic_Retail\System\MR_Enqueue;
use Mythic_Retail\System\MR_Redirects;

/**
 * Class MR_Public_Loader
 *
 * @package Mythic_Retail\Loader
 */
class MR_Public_Loader {

    /**
     * MR_Public_Loader constructor.
     */
    public function __construct() {
        new MR_Enqueue();
        new MR_Redirects();
    }

}