<?php

namespace Mythic_Gaming\System;

/**
 * Class MG_Aliases
 *
 * @package Mythic_Gaming\Globals
 */
class MG_Aliases {

    /**
     * MG_Aliases constructor.
     */
    public function __construct() {
        // Functions
        class_alias( 'Mythic_Gaming\Display\MG_Render', 'MG_Render' );
        // System
        class_alias( 'Mythic_Gaming\System\MG_Actions', 'MG_Actions' );
        class_alias( 'Mythic_Gaming\System\MG_Aliases', 'MG_Aliases' );
        class_alias( 'Mythic_Gaming\System\MG_Constants', 'MG_Constants' );
        class_alias( 'Mythic_Gaming\System\MG_Content', 'MG_Content' );
        class_alias( 'Mythic_Gaming\System\MG_Enqueue', 'MG_Enqueue' );
        class_alias( 'Mythic_Gaming\System\MG_Filters', 'MG_Filters' );
        class_alias( 'Mythic_Gaming\System\MG_Scripts', 'MG_Scripts' );
        class_alias( 'Mythic_Gaming\System\MG_Styles', 'MG_Styles' );
    }

}