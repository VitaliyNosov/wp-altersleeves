<?php

namespace Mythic\Functions\Website;

use Mythic\Helpers\MC2;

class MC2_Legal_Notices extends \Mythic\Abstracts\MC2_Class {
    
    /**
     * Returns the default copyright string
     *
     * @return string
     */
    public static function copyright() : string {
        return MC2::sprintf('&#169;%u Mythic Gaming. All rights reserved', [date( "Y" )]);
    }
    
}