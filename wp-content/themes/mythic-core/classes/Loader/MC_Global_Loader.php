<?php

namespace Mythic_Core\Loader;

use Mythic_Core\Abstracts\MC_Init;
use Mythic_Core\System\MC_Actions;
use Mythic_Core\System\MC_Crons;
use Mythic_Core\System\MC_Filters;
use Mythic_Core\System\MC_Sidebars;
use Mythic_Core\System\MC_Wordpress;

/**
 * Class MC_Global_Loader
 *
 * @package Mythic_Core\Loader
 */
class MC_Global_Loader extends MC_Init {
    
    const PREFIX = 'MC';
    
    public function getCurrentPrefix() : string {
        return self::PREFIX.'_';
    }
    
    public function initClasses() {
        new MC_Actions();
        new MC_Crons();
        new MC_Filters();
        new MC_Sidebars();
        new MC_Wordpress();
    }
    
}