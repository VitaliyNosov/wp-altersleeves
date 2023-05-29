<?php

$type = !empty( $_REQUEST['browse_type'] ) && $_REQUEST['browse_type'] == 'sets' ? 'sets' : 'cards';
if( $type == 'cards' ) {
    $card_name = $card_name ?? ''; ?>

    <div id="field-card-name" class="mb-3 col-sm-6 col-md-3">
        <small><label class="form-label" for="filter-card-name">Card Name</label><a href="javscript:void:(0);" class="reset"
                                                                                    data-reset="card-name" style="display: none;"> - reset</a></small>
        <input type="text" class="form-control" id="filter-card-name" placeholder="Enter card name here"
               name="card_name" value="<?= $card_name ?>" disabled>
    </div>
<?php } else {
    $printings = MC_Mtg_Set_Functions::printings( $set_id ); ?>

    <div id="field-card-name" class="mb-3 col-sm-6 col-md-3">
        <small><label class="form-label" for="input-card_id">Card</label><a href="javscript:void(0);" class="reset" data-reset="tags"
                                                                            style="display: none;"> - reset</a></small>
        <select class="form-control mc_select2 form-control-filter" id="input-card_id"
                name="card_set">

            <option value="0">All Cards</option>
            <?php
            
            $cards = [];
            foreach( $printings as $printing ) :
                $name = $printing->name;
                if( in_array( $name, $cards ) ) continue;
                $cards[] = $name;
                $value   = $printing->card_id;
                ?>
                <option value="<?= $value ?>" <?= $value == $card_id ? 'selected' : '' ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>

    </div>
<?php }
