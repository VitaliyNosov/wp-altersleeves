<?php

namespace Mythic_Retail\System;

/**
 * Class MR_Aliases
 *
 * @package Mythic_Retail\Globals
 */
class MR_Aliases {

    /**
     * MR_Aliases constructor.
     */
    public function __construct() {
        // System
        class_alias( 'Mythic_Retail\System\MR_Actions', 'MR_Actions' );
        class_alias( 'Mythic_Retail\System\MR_Constants', 'MR_Constants' );
        class_alias( 'Mythic_Retail\System\MR_Crons', 'MR_Crons' );
        class_alias( 'Mythic_Retail\System\MR_Enqueue', 'MR_Enqueue' );
        class_alias( 'Mythic_Retail\System\MR_Filters', 'MR_Filters' );
        class_alias( 'Mythic_Retail\System\MR_Redirects', 'MR_Redirects' );
        class_alias( 'Mythic_Retail\System\MR_Scripts', 'MR_Scripts' );
        class_alias( 'Mythic_Retail\System\MR_Styles', 'MR_Styles' );
    }

}