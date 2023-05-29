<?php

use Mythic_Core\Functions\MC_Mtg_Card_Functions;

$type = !empty( $_REQUEST['browse_type'] ) && $_REQUEST['browse_type'] == 'sets' ? 'sets' : 'cards';
if( $type == 'cards' ) {
    $printings = MC_Mtg_Card_Functions::printings( $card_id ); ?>

    <div id="field-card-printing" class="mb-3 col-sm-6 col-md-3">
        <small><label class="form-label" for="filter-card-printing">Set</label><a href="javscript:void(0);" class="reset" data-reset="tags"
                                                                                  style="display: none;"> - reset</a></small>
        <select class="form-control mc_select2 form-control-filter" id="filter-card-printing"
                name="card_set" <?= empty( $card_id ) ? 'disabled' : '' ?>>
            
            <?php if( empty( $card_id ) ) : ?>
                <option value="0">Select a Set</option>
            <?php else : ?>
                <option value="0">All Sets</option>
                <?php
                
                $sets = [];
                foreach( $printings as $printing ) :
                    $name = $printing->set_name;
                    if( in_array( $name, $sets ) ) continue;
                    $sets[] = $name;
                    $value  = $printing->set_id;
                    ?>
                    <option value="<?= $value ?>" <?= $value == $set_id ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?><?php endif; ?>
        </select>

    </div>

<?php } else {
    $set_name = $set_name ?? ''; ?>

    <div id="field-card-printing" class="mb-3 col-sm-6 col-md-3">
        <small><label class="form-label" for="filter-card-printing">Set Name</label><a href="javscript:void:(0);" class="reset"
                                                                                       data-reset="card-printing" style="display: none;"> -
                reset</a></small>
        <input type="text" class="form-control" id="filter-card-printing" placeholder="Enter set name here"
               name="card_name" value="<?= $set_name ?>" disabled>
    </div>

<?php }