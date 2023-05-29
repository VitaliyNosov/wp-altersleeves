<?php

namespace Mythic\Functions\Production;

use Mythic\Abstracts\MC2_Class;
use Mythic\Functions\MC2_Production_Functions;
use Mythic\Objects\System\MC2_Action;

class MC2_Printing_Functions extends MC2_Class {

    public function actions() {
        MC2_Action::new( 'mc_sleeves_production_report', [ MC2_Production_Functions::class, 'dailyOrderReports' ], 'daily', '2021-06-10 02:00:00' );
    }

}