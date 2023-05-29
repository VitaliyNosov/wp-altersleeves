<?php

namespace Alter_Sleeves\System;

use Mythic_Core\System\MC_Crons;

class AS_Crons {
    
    public static function init() {
        new self();
    }
    
    public function __construct() {
        $this->daily();
        $this->hourly();
    }
    
    public function hourly() {
        MC_Crons::recurring( 'mc_alter_recent_results', '1620100500' );
    }
    
    public function daily() {
        MC_Crons::recurring( 'mc_alters_design_groups', '1620100800', 'daily' );
        MC_Crons::recurring( 'mc_alter_names', '1620097200', 'daily' );
        MC_Crons::recurring( 'import_royalties_from_orders', '1576652400', 'daily' );
        MC_Crons::recurring( 'mc_update_product_sales', '1620086700', 'daily' );
        MC_Crons::recurring( 'mc_run_search_indexing_json', '1620817800', 'daily' );
        MC_Crons::recurring( 'mc_sleeves_production_report', 1623290400, 'daily' );
        MC_Crons::recurring( 'mc_publishing_royalties', 1623290400, 'daily' );
    }
    
}
