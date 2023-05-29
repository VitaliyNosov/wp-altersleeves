<?php

namespace Mythic_Gaming\Loader;

use Mythic_Core\Abstracts\MC_Init;
use Mythic_Gaming\System\MG_Actions;
use Mythic_Gaming\System\MG_Crons;
use Mythic_Gaming\System\MG_Filters;

/**
 * Class MG_Global_Loader
 *
 * @package Mythic_Gaming\Loader
 */
class MG_Global_Loader extends MC_Init {

    public function initClasses() {
        new MG_Actions();
        new MG_Filters();
    }

    /**
     * @return string
     */
    public function getCurrentPrefix() : string {
        return 'MG_';
    }

    /**
     * @return string
     */
    public function getCurrentNamespaceMain() : string {
        $namespace = explode( '\\', __NAMESPACE__ );

        return $namespace[0];
    }

}
