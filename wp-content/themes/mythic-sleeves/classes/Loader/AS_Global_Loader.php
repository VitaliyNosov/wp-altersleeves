<?php

namespace Alter_Sleeves\Loader;

use Alter_Sleeves\System\AS_Actions;
use Alter_Sleeves\System\AS_Filters;
use Alter_Sleeves\System\AS_Redirects;
use Mythic_Core\Abstracts\MC_Init;

class AS_Global_Loader extends MC_Init {
    
    public const PREFIX = 'AS';
    
    public function initClasses() {
        new AS_Actions();
        new AS_Filters();
        new AS_Redirects();
    }
    
    /**
     * @return string
     */
    public function getCurrentPrefix() : string {
        return self::PREFIX.'_';
    }
    
    /**
     * @return string
     */
    public function getCurrentNamespaceMain() : string {
        $namespace = explode( '\\', __NAMESPACE__ );
        
        return $namespace[0];
    }
    
}
