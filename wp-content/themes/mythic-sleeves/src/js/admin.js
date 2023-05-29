$(function() {

    let setAvailable,
        setId,
        doneTyping,
        doneTypingInterval = 1000,
        withdrawalApproved,
        withdrawalId;

    $('input[type="checkbox"].set-availability').change(function() {
        setAvailable = this.checked === true ? 1 : 0;
        setId = $(this).val();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-set-status",
                available: setAvailable,
                id: setId,
                mc_nonce: getNonceValByAction('as-set-status')
            }
        });
    });

    $('input[type="checkbox"].product-internal-selector').change(function() {
        let productInternal = this.checked === true ? 1 : 0,
            productId = $(this).data('id');
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "mc-product-internal",
                internal: productInternal,
                id: productId,
                mc_nonce: getNonceValByAction('mc-product-internal')
            },
            success: function( response ) {
            }
        });
    });

    $('input[type="checkbox"].withdrawal-approved').change(function() {
        withdrawalApproved = this.checked === true ? 1 : 0;
        withdrawalId = $(this).val();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "mc-withdrawal-approval",
                approved: withdrawalApproved,
                withdrawal_id: withdrawalId
            }
        });
    });

    $(document).on('click', '#action-send-approvals', function() {
        let _ = jQuery(this);
        _.html(_.html().replace('SEND APPROVALS', 'SENDING'));

        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-send-approvals",
                count: $('#input-approval-count').val(),
                mc_nonce: getNonceValByAction('as-send-approvals')
            },
            success: function( response ) {
                _.html(_.html().replace('SENDING', 'SENT'));
            }
        });

    });

    $(document).on('click', '.frame-update', function( e ) {
        $("form").submit(function( e ) {
            e.preventDefault(e);
        });
        let team = $(this).data('team');
        let _ = $(this);

        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "mythic-frames-update",
                team: team,
                'alterist_image': $('#input-team-image-' + team).val(),
                'content_creator_image': $('#input-cc-image-' + team).val(),
                'description': $('#input-team-description-' + team).val(),
                'Creature': $('#input-creature-' + team).val(),
                'Non-Creature': $('#input-noncreature-' + team).val(),
                'Land': $('#input-land-' + team).val(),
                'Commander 1': $('#input-commander-1-' + team).val(),
                'Commander 2': $('#input-commander-2-' + team).val(),
                'Commander 3': $('#input-commander-3-' + team).val(),
            },
            success: function( response ) {
                _.text('Saving');
                _.text('Saved');
            }
        });
    });

})