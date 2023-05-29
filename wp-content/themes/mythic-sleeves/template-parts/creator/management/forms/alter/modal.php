<?php

use Mythic_Core\Functions\MC_Alter_Functions;

$alter_id  = $_GET['alter_id'] ?? 0;
$design_id = MC_Alter_Functions::design( $alter_id );
?>

<div id="alter_submit_modal" class="modal fade modal-full" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div id="alter_submit_modal_" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-none">
                <h5 class="modal-title">Submit new design or design variation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php include 'nav.php' ?>
                <div class="container py-4">
                    <div class="row align-items-start tabs-wrapper position-relative justify-content-start">
                        <div class="col-md-9 tab-content" id="myTabContent">
                            <?php
                            include 'steps/step-1-design.php';
                            include 'steps/step-2-printings.php';
                            include 'steps/step-3-cutter.php';
                            include 'steps/step-4-tagging.php';
                            include 'steps/step-5-confirm.php';
                            ?>
                        </div>
                        <?php include 'info.php' ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Dejan to do : Add loader -->
                <button type="button" class="btn btn-outline-danger mr-auto" data-bs-dismiss="modal">Close</button>
                <div class="loading-icon" style="display: none;"></div>
                <button id="prev_submit" type="button" disabled="" class="btn btn-secondary">Previous</button>
                <button id="next_submit" type="button" disabled="" class="btn btn-primary">Next</button>
            </div>
        </div>
    </div>
</div>
