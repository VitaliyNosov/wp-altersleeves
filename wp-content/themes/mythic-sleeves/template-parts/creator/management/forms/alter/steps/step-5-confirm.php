<div class="tab-pane fade" id="confirmation_submit" role="tabpanel" aria-labelledby="confirmation_tab">
    <h2 class="text-center">Finalise your submission</h2>

    <div class="row mb-3">
        <div class="col-lg-6">
            <hr>
            <div class="mb-3">
                <label class="form-label" for="assign_submit_bounty">Outstanding bounties</label>
                <input type="text" class="form-control" id="assign_submit_bounty" placeholder="Enter bounties here; separate by comma">
            </div>
            <hr>
            <h3>Legal Notices</h3>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="assign_submit_consent">
                <label class="form-check-label" for="assign_submit_consent">
                    I own the right to all images or content used in this design and have not copied or cloned any parts of a
                    copyrighted card or copyrighted material: I accept responsibility as an individual if there is a copyright
                    claim
                </label>
            </div>

            <input disabled="disabled" class="form-check-input" type="hidden" value="" id="assign_submit_file">
            <label class="form-check-label sr-only" for="assign_submit_file">File path</label>

            <div class="text-center py-4 col-12">
                <button id="assign_submit_complete" type="button" class="btn btn-success w-100">
                    Submit <?= !empty( $alter_id ) ? 'Changes' : 'Alter' ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#assign_submit_complete').click(function() {
            showLoading();
            if( !document.getElementById("assign_submit_consent").checked ) {
                alert("Please confirm you consent to our terms and conditions");
                hideLoading();
                return;
            }

            $.ajax({
                type: "post",
                dataType: "json",
                url: vars.ajaxurl,
                data: {
                    action: "as-alter-submit",
                    creator: $('#assign_submit_creator').val(),
                    path_alter: $('#assign_submit_file').val(),
                    alter_id: <?= $_GET['alter_id'] ?? 0 ?>,
                    design_id: $('#assign_design_id').val(),
                    design_name: $('#assign_submit_name').val(),
                    printings: $('#assign_printings').val(),
                    alter_type: $('#assign_submit_crop_type').val(),
                    product_tags: $('#input-design-tags').val(),
                    bounty: $('#assign_submit_bounty').val(),
                    availability: $('input[type=radio][name=assign_submit_alter_availability]:checked').val(),
                    generic: document.getElementById('assign_submit_design_generic').checked
                },
                success: function() {
                    $('#assign_submit_complete').replaceWith('<p class="py-2 text-center text-success">Your alter has been' +
                        ' submitted' +
                        ' successfully</p>')
                    hideLoading();
                }
            })
        })
    })
</script>