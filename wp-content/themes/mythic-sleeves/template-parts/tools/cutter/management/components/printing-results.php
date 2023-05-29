<?php

use Mythic_Core\Objects\MC_Mtg_Printing;

?>

<div id="cutter-printing-results" class=" my-2 p-3" style="display: none;">
    <h3>Select a printing</h3>
    <?php
    
    if( !empty( $printings ) ) : ?><?php foreach( $printings as $printing_id ) :
        $printing = new MC_Mtg_Printing( $printing_id );
        if( empty( $printing ) ) continue;
        $name             = $printing->name;
        $set              = $printing->set_name;
        $collector_number = $printing->collector_number;
        $name             = $name.' - '.$set.' ('.$collector_number.')';
        ?>
        <div class="cutter-printing-result py-2" data-printing="<?= $printing_id ?>"><a href="javascript:void(0);"><?= $name ?></a></div>
    <?php endforeach; ?><?php endif; ?>
    <style>
        #cutter-printing-results {
            max-height: 400px;
            background: #fff;
            overflow-y: auto;
        }

        .cutter-card-result:hover {
            cursor: pointer;
        }
    </style>
</div>