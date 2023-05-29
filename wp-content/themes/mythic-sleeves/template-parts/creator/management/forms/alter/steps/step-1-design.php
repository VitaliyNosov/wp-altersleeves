<?php

$setup_options        = MC_Alter_Functions::design_setup_options();
$availability_options = MC_Woo_Product_Functions::availabilityOptions();
if( !empty( $alter_id ) ) {
    $setup_options['new']['selected'] = false;
    $setup_options['new']['disabled'] = true;
    $setup_options['add']['selected'] = true;
    $setup_options['add']['disabled'] = true;
    
    if( MC_Vars::stringContains( get_post_status( $alter_id ), 'internal' ) ) {
        $availability_options['internal']['selected'] = true;
    }
}
?>

    <div class="tab-pane fade show active" id="assign_submit" role="tabpanel" aria-labelledby="assign_tab">
        <div class="row align-items-start">
            <div class="col-sm-9 bg-white pb-5">

                <!-- Design Setup -->
                <div class="mb-3 has-info" data-info-id="info_design_setup">
                    <h3>I am uploading <span style="color:red;">*</span></h3>
                    <?php
                    
                    MC_Forms::buildRadioFields( 'submit_setup_design', $setup_options ); ?>
                </div>

                <!-- Search for design -->
                <div id="submit_design_search" class="d-none">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="sr-only" for="designSearch">Find a design</label>
                            <input id="designSearch" class="form-control" autocomplete="on" type="text" placeholder="Search design name or ID"
                                   name="design_search" value="">
                        </div>
                        <div class="col-auto">
                            <button id='trigger_design_search' type="button" class="btn btn-primary">Search Designs</button>
                        </div>
                    </div>
                </div>

                <div id="design_search_results" class="d-none submit-card-results mb-3">
                    <div class="row align-items-start "></div>
                </div>

                <div id="submit_design_config">
                    <div id="display_design_ids" class="d-none">
                        <hr>
                        <!-- Linked Designs -->
                        <div class="mb-3 d-none">
                            <div class="mb-3 has-info" data-info-id="info_linked_designs">
                                <label class="form-label" for="assign_linked_designs">Linked designs</label>
                                <input autocomplete="off" type="text" class="form-control" id="assign_linked_designs" value="[]" readonly='' required>
                            </div>
                        </div>
                        <div class="row mb-3 justify-content-space-between">
                            <div class="col-6">
                                <!-- Design ID -->
                                <div class="mb-3 has-info" data-info-id="info_design_id">
                                    <label class="form-label" for="assign_design_id">Design ID</label>
                                    <input autocomplete="off" type="text" class="form-control" id="assign_design_id" value="0" readonly='' required>
                                </div>
                            </div>
                            <!-- Print ID -->
                            <div class="mb-3 has-info d-none" data-info-id="info_print_id">
                                <label class="form-label" for="assign_print_id">Print ID</label>
                                <input autocomplete="off" type="text" class="form-control" id="assign_print_id" value="0" readonly='' required>
                            </div>

                        </div>
                    </div>
                    <hr>

                    <!-- Design Name -->
                    <div class="mb-3 has-info" data-info-id="info_design_name">
                        <label class="form-label" for="assign_submit_name">Design Name <span style="color:red;">*</span></label>
                        <input autocomplete="off" type="text" class="form-control" id="assign_submit_name" placeholder="eg. The Great White Dragon"
                               value="<?= !empty( $design_id ) ? get_the_title( $design_id ) : '' ?>" required>
                    </div>

                    <!-- Cropping Type -->
                    <div class="mb-3 has-info" data-info-id="info_crop_type">
                        <label class="form-label" for="assign_submit_crop_type">This type best describes my new variation <span
                                    style="color:red;">*</span></label>
                        <select class="form-control" id="assign_submit_crop_type" required>
                        </select>
                    </div>

                    <!-- Design generic -->
                    <div class="form-check mb-3 has-info" data-info-id="info_design_generic">
                        <input disabled="disabled" class="form-check-input" type="checkbox" value="" id="assign_submit_design_generic">
                        <label class="form-check-label form-label" for="assign_submit_design_generic">
                            This design is generic and will work for all cards of matching frame sets
                        </label>
                    </div>
                    <hr>
                    <div class="mb-3 has-info" data-info-id="info_design_availability">
                        <h3 id="assign_submit_availablity">This design will be <span style="color:red;">*</span></h3>
                        <!-- Alter Availablity -->
                        <?php MC_Forms::buildRadioFields( 'assign_submit_alter_availability', $availability_options ) ?>
                    </div>
                    <input disabled="disabled" class="form-check-input" type="hidden" value="<?= wp_get_current_user()->ID ?>"
                           id="assign_submit_creator">
                    <label class="form-check-label sr-only" for="assign_submit_creator">Author ID</label>
                </div>
            </div>
        </div>
    </div>

<?php return; ?>