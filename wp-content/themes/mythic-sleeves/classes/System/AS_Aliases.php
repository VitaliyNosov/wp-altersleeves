<?php

namespace Alter_Sleeves\System;

/**
 * Class AS_Aliases
 *
 * @package Alter_Sleeves\System
 */
class AS_Aliases {
    
    /**
     * AS_Aliases constructor.
     */
    public function __construct() {
        // System
        class_alias( 'Alter_Sleeves\System\AS_Actions', 'AS_Actions' );
        class_alias( 'Alter_Sleeves\System\AS_Aliases', 'AS_Aliases' );
        class_alias( 'Alter_Sleeves\System\AS_Browse', 'AS_Browse' );
        class_alias( 'Alter_Sleeves\System\AS_Constants', 'AS_Constants' );
        class_alias( 'Alter_Sleeves\System\AS_Crons', 'AS_Crons' );
        class_alias( 'Alter_Sleeves\System\AS_Enqueue', 'AS_Enqueue' );
        class_alias( 'Alter_Sleeves\System\AS_Filters', 'AS_Filters' );
        class_alias( 'Alter_Sleeves\System\AS_Redirects', 'AS_Redirects' );
        class_alias( 'Alter_Sleeves\System\AS_Scripts', 'AS_Scripts' );
        class_alias( 'Alter_Sleeves\System\AS_Styles', 'AS_Styles' );
    }
    
}