<?php

namespace Mythic_Template\Loader;

use Mythic_Template\Abstracts\MT_Init;
use Mythic_Template\System\MT_Actions;

/**
 * Class MT_Global_Loader
 *
 * @package Mythic_Template\Loader
 */
class MT_Global_Loader extends MT_Init {

    const PREFIX = 'MT';

    public function getCurrentPrefix() : string {
        return self::PREFIX.'_';
    }

    public function initClasses() {
        new MT_Actions();
    }

}
