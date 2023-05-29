<?php

namespace Mythic_Retail\Loader;

use Mythic_Core\Abstracts\MC_Init;
use Mythic_Retail\Functions\MR_User_Functions;
use Mythic_Retail\System\MR_Actions;
use Mythic_Retail\System\MR_Crons;
use Mythic_Retail\System\MR_Filters;

/**
 * Class MR_Global_Loader
 *
 * @package Mythic_Retail\Loader
 */
class MR_Global_Loader extends MC_Init {

    public function initClasses() {
        new MR_Actions();
        new MR_Filters();
        new MR_Crons();

        new MR_User_Functions();
    }

    /**
     * @return string
     */
    public function getCurrentPrefix() : string {
        return 'MR_';
    }

    /**
     * @return string
     */
    public function getCurrentNamespaceMain() : string {
        $namespace = explode( '\\', __NAMESPACE__ );

        return $namespace[0];
    }

}
