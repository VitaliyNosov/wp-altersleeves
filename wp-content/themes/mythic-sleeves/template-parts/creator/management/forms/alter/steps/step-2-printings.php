<?php

$printings      = !empty( $alter_id ) ? MC_Alter_Functions::additionalPrintings( $alter_id ) : [];
$printings_json = json_encode( $printings, JSON_NUMERIC_CHECK );
?>

<div class="tab-pane fade pb-5" id="select_submit" role="tabpanel" aria-labelledby="select_tab">

    <!-- Card Search -->
    <div class="mb-3 has-info" data-info-id="info_card_search">
        <h2>Card Selection</h2>
        <p>Select the printings that work for your alter. Start by searching for a card below:<br>
            <small>Please note - does not yet work for <strong>generic</strong> lands</small></p>

        <div class="row mt-3">
            <div class="col">
                <label class="sr-only" for="select_submit_card_search_txt">Find a Card</label>
                <input id="select_submit_card_search_txt" class="form-control" autocomplete="off" type="text" placeholder="Search card name"
                       name="select_submit_card_search_txt" required value="">
            </div>
            <div class="col-auto">
                <button id='select_submit_card_search_btn' type="button" class="btn btn-primary">Search for Card</button>
            </div>
        </div>
    </div>

    <!-- Card Selection -->
    <div id="select_submit_card_search_res" class="mb-3 has-info" data-info-id="info_card_search">
        <div>
            <span id="select_submit_card_selected" class='d-none fw-bold'>Selected card: </span>
            <nav id="select_submit_card_search_nav" class="nav"></nav>
        </div>
    </div>

    <!-- Printing Selection -->
    <div id="select_submit_card_printings_results" class="d-none submit-card-results mb-4 has-info" data-info-id="info_printing_selection">
        <div class="row align-items-start justify-content-space-between"></div>
    </div>

    <!-- Printing IDs -->
    <div class="mb-3 has-info" data-info-id="info_design_id">
        <label class="form-label" for="assign_design_id">Selected Printings (IDs)</label>
        <input autocomplete="off" type="text" class="form-control" id="assign_visible_printings"
               value="<?= !empty( $printings ) ? $printings[0] : '' ?>" readonly='' required>
    </div>

    <input id="assign_printings" type="hidden" value="<?= $printings_json ?>">
    <input id="assign_framecode" type="hidden" value="">
</div>