<?php

namespace Alter_Sleeves\System;

/**
 * Class MC_Globals
 *
 * @package Alter_Sleeves\System
 */
class AS_Globals {
    
    /**
     * MC_Globals constructor.
     */
    public function __construct() {
        global $icon_logo;
        $icon_logo = AS_URI_ICON_LOGO;
    }
    
}