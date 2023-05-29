<?php

namespace Mythic_Core\Loader;

use MC_Cookies;
use MC_Enqueue;
use MC_Nav;
use MC_Post_Types;
use MC_Redirects;
use MC_Statuses;
use MC_Taxonomies;

/**
 * Class MC_Public_Loader
 *
 * @package Mythic_Core\Loader
 */
class MC_Public_Loader {
    
    /**
     * MC_PublicLoader constructor.
     */
    public function __construct() {
        new MC_Cookies();
        new MC_Enqueue();
        new MC_Nav();
        new MC_Post_Types();
        new MC_Statuses();
        new MC_Taxonomies();
        new MC_Redirects();
    }
    
}