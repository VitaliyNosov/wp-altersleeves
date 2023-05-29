$(function() {

    let _,
        idAlter,
        issueResolution,
        issueCopyright,
        issueCloneTool,
        issueCroppingTight,
        issueCroppingInsufficient,
        issueOpacity,
        issueWhiteTransparent,
        message,
        url,
        version,
        valueDesignId,
        valueAlterType,
        valueGeneric;

    $(document).on('change', "#input-mod-alter-type", function() {
        valueDesignId = $(this).data('design');
        valueAlterType = $(this).val();
        valueGeneric = 0;

        if( $('#input-mod-generic').length ) {
            if( $('#input-mod-generic').prop('checked') ) {
                valueGeneric = 1;
            }
        }
        updateAlterType(valueDesignId, valueAlterType, valueGeneric);
    });

    $(document).on('change', "#input-mod-generic", function() {
        valueDesignId = $('#input-mod-alter-type').data('design');
        valueAlterType = $('#input-mod-alter-type').val();
        valueGeneric = 0;
        if( $('#input-mod-generic').prop('checked') ) {
            valueGeneric = 1;
        }
        updateAlterType(valueDesignId, valueAlterType, valueGeneric);
    });

    function updateAlterType( design_id, alter_type, generic ) {
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-mod-alter-type",
                design_id: design_id,
                alter_type: alter_type,
                generic: generic
            },
            success: function( e ) {
                $('#mod_types').replaceWith(e.output);
            }
        });
    }

    /** Actions **/

    $(document).on('click', '.action-approve', function( e ) {
        idAlter = $(this).data('alter-id');
        removeAlter(idAlter);
        approveAlter(idAlter);
    });

    $(document).on('click', '.action-reject', function( e ) {
        idAlter = $(this).data('alter-id');
        removeAlter(idAlter);
        approvalReject(idAlter);
    });
    $(document).on('click', '.action-flag', function( e ) {
        idAlter = $(this).data('alter-id');
        console.log(idAlter);
        productFlag(idAlter);
    });
    $(document).on('click', '.action-pull', function( e ) {
        idAlter = $(this).data('alter-id');
        console.log('pull' + idAlter);
        productFlag(idAlter, 'pull');
    });

    function approveAlter( idAlter ) {
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "alter-approve",
                alter_id: idAlter,
                mc_nonce: getNonceValByAction('alter-approve')
            },
            success: function( response ) {

            }
        });
    }

    function approvalReject( idAlter ) {
        issueCopyright =
            $('#approval-' + idAlter + ' #option-flag-copyright').not(':checked').length ? 0 : 1;
        issueResolution =
            $('#approval-' + idAlter + ' #option-flag-resolution').not(':checked').length ? 0 : 1;
        issueCroppingTight =
            $('#approval-' + idAlter + ' #option-flag-cropping-tight').not(':checked').length ? 0 : 1;
        issueCroppingInsufficient =
            $('#approval-' + idAlter + ' #option-flag-cropping-insufficient').not(':checked').length ? 0 : 1;
        issueOpacity =
            $('#approval-' + idAlter + ' #option-flag-opacity').not(':checked').length ? 0 : 1;
        issueWhiteTransparent =
            $('#approval-' + idAlter + ' #option-flag-white-transparent').not(':checked').length ? 0 : 1;

        message = $('#approval-' + idAlter + ' #input-message').val();

        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "alter-reject",
                alter_id: idAlter,
                issue_resolution: issueResolution,
                issue_copyright: issueCopyright,
                issue_cropping_tight: issueCroppingTight,
                issue_cropping_insufficient: issueCroppingInsufficient,
                issue_opacity: issueOpacity,
                issue_white_transparent: issueWhiteTransparent,
                message: message,
                mc_nonce: getNonceValByAction('alter-reject')
            },
            success: function() {
                removeAlter(idAlter);
            }
        });
    }

    function productFlag( idAlter, action = 'flag' ) {
        // Production issues
        issueCopyright =
            $('#option-flag-copyright-' + idAlter).not(':checked').length ? 0 : 1;
        issueCloneTool =
            $('#option-flag-clone-tool-' + idAlter).not(':checked').length ? 0 : 1;
        issueResolution =
            $('#option-flag-resolution-' + idAlter).not(':checked').length ? 0 : 1;
        issueCroppingTight =
            $('#option-flag-cropping-tight-' + idAlter).not(':checked').length ? 0 : 1;
        issueCroppingInsufficient =
            $(' #option-flag-cropping-insufficient-' + idAlter).not(':checked').length ? 0 : 1;
        issueOpacity =
            $('#option-flag-opacity-' + idAlter).not(':checked').length ? 0 : 1;
        issueWhiteTransparent =
            $('#option-flag-white-transparent-' + idAlter).not(':checked').length ? 0 : 1;

        let issueTypeDesign =
                $('#option-flag-design-type-' + idAlter).not(':checked').length ? 0 : 1,
            issueTypeAlter =
                $('#option-flag-alter-type-' + idAlter).not(':checked').length ? 0 : 1,
            issueTags =
                $('#option-flag-tags-' + idAlter).not(':checked').length ? 0 : 1,
            issuePrinting =
                $('#option-flag-printing-' + idAlter).not(':checked').length ? 0 : 1,

            message = $('#input-message').length ? $('#input-message').val() : '';

        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-alter-flag",
                flag_action: action,
                alter_id: idAlter,
                incorrect_resolution: issueResolution,
                copyright: issueCopyright,
                cropping_tight: issueCroppingTight,
                cropping_insufficient: issueCroppingInsufficient,
                white_blobs: issueOpacity,
                transparent: issueWhiteTransparent,
                incorrect_design_type: issueTypeDesign,
                incorrect_alter_type: issueTypeAlter,
                incorrect_tags: issueTags,
                incorrect_printing: issuePrinting,
                message: message,
                mc_nonce: getNonceValByAction('as-alter-flag')
            },
            success: function() {
                $('#acceptance-buttons').replaceWith('<div class="text-danger text-right w-100 px-3">Alterist' +
                    ' messaged</div>');
            }
        });
    }

    function removeAlter( idAlter ) {
        _ = $('#approval-' + idAlter);
        _.fadeOut(500, function() {
            _.remove();
        });
        $('.camera-result').empty();
        var curr_val = $('#count-approvals').text();
        var new_val = parseInt(curr_val) - 1;
        $('#count-approvals').text(new_val);
    }

    function showLoad() {
        $('.loading').show();
    }

    function hideLoad() {
        $('.loading').hide();
    }

    if( !$('.camera-feed').length ) return;

    /** Functions **/
    $(document).on('click', '.action-capture', function( e ) {
        showLoad();
        idAlter = $(this).data('alter-id');
        version = $(this).data('version');
        snapAlter(idAlter, version);
        $('#approve-' + idAlter).show();
        $('#reject-' + idAlter).show();
        hideLoad();
    });

    $(function() {
        Webcam.set({
            width: 320,
            height: 240,
            image_format: 'jpeg',
            jpeg_quality: 100,
            dest_width: 640,
            dest_height: 480,
        });
        Webcam.attach('.camera-feed');
    })

    function snapAlter( product_id, version ) {
        Webcam.snap(function( data_uri ) {
            let data = {
                product_id: product_id,
                folder: '/files/alters/' + product_id,
                filename: version + '.jpg',
                img_data: data_uri
            };
            ajaxPost('mc-store-webcam-image', data, function( response ) {
                console.log(response);
                $('.camera-result').html('<br>' +
                    '<h2>Captured Image</h2>' +
                    '<img src="' + response.url + '"><br>');
            })
        });
    }

});
