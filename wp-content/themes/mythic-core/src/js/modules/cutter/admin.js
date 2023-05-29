$(function() {
    /** MC_Cutter Management **/

    /** DELETING **/
    // Delete component - Check
    $(document).on('click', '.action-init-delete', function() {
        let target = $(this).data('target');
        $('.delete-check').hide();
        $('#target-' + target + ' .delete-hide').hide();
        $('#target-' + target + ' .delete-check').show();
    });

    // Delete component - Confirm
    $(document).on('click', '.action-confirm-delete', function() {
        let type = $(this).data('type'),
            target = $(this).data('target');
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-cutter-delete-component",
                type: type,
                id: target
            },
            success: function() {
                $('#target-' + target).fadeOut().remove();
            }
        });
    });

    // Delete variation - Confirm
    $(document).on('click', '#action-create-element-variation', function() {
        let element = $('#input-element-id').val();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-cutter-create-element-variation",
                element: element
            },
            success: function( response ) {
                $('#element-variations').append('<div class="col-6 col-sm-4">' + response.output + '</div>');
            }
        });
    });

    /** Mask Card selection **/
    // Card Search
    if( $('#input-cutter-card-search').length ) {
        $(document).on('keyup', 'input[name="cutter_card_search"]', function() {
            if( $(this).val().length > 0 ) {
                clearTimeout(doneTyping);
                doneTyping = setTimeout(cardSearch, doneTypingInterval);
            } else {
                clearTimeout(doneTyping);
                $('#results-card-name').empty().hide();
            }
        });
    }

    function cardSearch() {
        let valueCardSearchTerm = $('input[name="cutter_card_search"]').val();
        $('#cutter-card-search-results').hide().empty();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-cutter-card-search",
                search_term: valueCardSearchTerm
            },
            success: function( response ) {
                $('#cutter-card-search-results').replaceWith(response.output);
                $('#cutter-card-search-results').show();
            }
        });
    }

    $(document).on('click', '.cutter-card-result', function() {
        let card = $(this).data('card');
        $('#cutter-card-search-results').hide().empty();
        $('#cutter-printing-results').hide().empty();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-cutter-card-selection",
                card: card
            },
            success: function( response ) {
                $('#cutter-printing-results').replaceWith(response.output);
                $('#cutter-printing-results').show();
            }
        });
    });
    $(document).on('click', '.cutter-printing-result', function() {
        let printing = $(this).data('printing');
        $('#cutter-card-search-results').hide().empty();
        $('#cutter-printing-results').hide().empty();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-cutter-printing-selection",
                printing: printing
            },
            success: function( response ) {
                $('#cutter-selected-printing').replaceWith(response.output);
            }
        });
    });

    /** UPLOADING VARIATION **/
    $(document).on('change', '.variation-file-upload', function( e ) {
        let target = $(this).data('target');
        uploadMessageFile(target);
    });

    function uploadMessageFile( target ) {
        $('#target-' + target + ' .variation-file-image').empty();
        let field = $('#variation-file-upload-' + target);
        var file_data = field.prop('files')[0],
            form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('action', 'cutter_variation_file');

        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php',
            beforeSend: function() {
                $('.loading-upload').show();
            },
            type: 'post',
            contentType: false,
            processData: false,
            data: form_data,
            success: function( response ) {
                $('#target-' + target + ' .variation-file-image')
                    .html('<img class="p-3" src="' + response.url + '" style="width:100%">');
                $('#variation-file-' + target).val(response.url);
            },
            error: function() {
                console.log('error');
            }
        });
    }

    // Variation Highlighting
    $(".element-variation-highlight").hover(function() {
        let element = $(this).data('element');
        $('#element-preview-' + element)
            .css("opacity", "0.3");
    }, function() {
        let element = $(this).data('element');
        $('#element-preview-' + element)
            .css("opacity", "initial");
    });

    // Scroll preview map
    $(window).scroll(function() {
        if( !$('#map-preview-container').length ) return;
        let containerTop = $('#map-preview-container').position().top,
            windowScrollTop = $(window).scrollTop();
        if( windowScrollTop > containerTop )
            $('#map-affix')
                .css('position', 'absolute')
                .css('top', windowScrollTop - containerTop);
        else {
            $('#map-affix')
                .css('position', 'relative')
                .css('top', 0);
        }
    })
})