<?php

namespace Alter_Sleeves\Loader;

use Alter_Sleeves\System\AS_Enqueue;
use Alter_Sleeves\System\AS_Redirects;
use Alter_Sleeves\System\AS_Taxonomies;

/**
 * Class AS_Public_Loader
 *
 * @package Alter_Sleeves\Loader
 */
class AS_Public_Loader {
    
    /**
     * AS_Public_Loader constructor.
     */
    public function __construct() {
        new AS_Enqueue();
        new AS_Redirects();
    }
    
}