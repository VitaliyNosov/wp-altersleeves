<?php

namespace Mythic\Functions\Admin;

use Mythic\Abstracts\MC2_Class;
use Mythic\Helpers\MC2_Functions;
use Mythic\Objects\System\MC2_Ajax;

class MC2_Run_Functions extends MC2_Class {
    
    public function ajax() {
        MC2_Ajax::new(  'AdminRunCommands', [ MC2_Functions::class, 'run' ]  );
    }
    
}