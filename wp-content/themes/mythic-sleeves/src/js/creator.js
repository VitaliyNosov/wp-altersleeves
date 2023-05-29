jQuery(document).ready(function() {

    var doneTyping,
        doneTypingInterval = 500,
        idTag,
        tagIndex,
        tags;

    /** Management - 10. Design Tags **/
    $(document).on('click', '.management-tags .tag a', function( e ) {
        tags = $('#input-design-tags').val().length ? JSON.parse($('#input-design-tags').val()) : [];
        idTag = $(this).data('tag-id');

        if( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
            tagIndex = tags.indexOf(idTag);
            if( tagIndex !== -1 ) tags.splice(tagIndex, 1);
        } else {
            $(this).addClass('selected');
            if( !tags.includes(idTag) ) tags.push(idTag);
        }
        $('#input-design-tags').val(JSON.stringify(tags));
    });

    function noticeSaved() {
        $(".notice-saved").show();
        setTimeout(function() {
            $(".notice-saved").hide();
        }, 5000);
        loadHide();
    }

    function loadHide() {
        $('.loading').hide();
        $('#loading-card-search').hide();
    }

    /** Delete Designs **/
    $(".manage__delete").on('click', function( event ) {

        if ( confirm("Delete this alter?") ) {

            var id = $(this).data('delete-id');
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: vars.ajaxurl,
                data: {
                    action: "cas-trash-design",
                    product_id: id,
                    mc_nonce: getNonceValByAction('cas-trash-design')
                },
                success: function( response ) {
                    $('#' + id).empty();
                    $('#' + id).append('<h2 class="text-danger">Item Deleted</h2>');
                    setTimeout(function() {
                        $('#' + id).fadeOut().remove();
                    }, 2000);
                    $('#modal_' + id).remove();
                }
            })
        }
    });

    /** Managing Collections **/
    var designAdd = $('.design-add'),
        designsCount = $('.design-sortable').length,
        designCount = $('.design-count'),
        designLess = $('.design-less'),
        designSelected,
        designsSelected = [],
        designSlots,
        designsSortable = $('.designs-sortable'),
        designSortable,
        designSortableClass = '.design-sortable',
        designSortableId,
        designSortableMin = 3,
        designSortableMax = 6,
        designSortableNew,
        designSortablePos,
        idCollection,
        inputSelectedPos = $('#input-selected-position'),
        inputSelectedPosVal,
        sortableIndex,
        sortableIndexRead,
        sortableEnd;

    if( designsSortable.length ) {
        designsSortable.sortable({
            items: ' > div:not(.design-add)',
            update: function( event, ui ) {
                designSortable = $(designSortableClass);
                designSortable.each(function( e ) {
                    sortableIndexRead = e + 1;
                    $(this).attr('id', 'sortable-' + e).attr('data-index', e);
                    $(this).find('.sortable-index').text(sortableIndexRead);
                    $(this).find('.remove').attr('data-index', e);
                });

            }
        });

    }

    $(document).on('click', '.design-sortable .remove', function() {
        designSortable = $(designSortableClass);
        if( designSortable.length <= designSortableMin ) return;
        sortableIndex = $(this).data('index');
        $('#sortable-' + sortableIndex + '.design-sortable').remove();
        // Recall to recount
        designSortable = $(designSortableClass);
        designSortable.each(function( e ) {
            sortableIndexRead = e + 1;
            $(this).attr('id', 'sortable-' + e).attr('data-index', e);
            $(this).find('.sortable-index').text(sortableIndexRead);
            $(this).find('.remove').attr('data-index', e);
        });
        designSortable = $(designSortableClass);
        designsCount = designSortable.length;
        designCount.text(designsCount);
    });

    $(document).on('click', '.design-add', function() {
        designSortable = $(designSortableClass);
        if( designSortable.length >= designSortableMax ) return;
        sortableIndex = designSortable.length;
        sortableIndexRead = sortableIndex + 1;
        designSortableNew = fnDesignSortableBlank();
        $(designsSortable).append(designSortableNew);
        designSortable = $(designSortableClass);
        designsCount = designSortable.length;
        designCount.text(designsCount);
    });

    $(document).on('click', '.design-less', function() {
        designSortable = $(designSortableClass);
        if( designSortable.length <= designSortableMin ) return;
        $('.design-sortable:not(.has-design):last-child').remove();
        designSortable = $(designSortableClass);
        designSortable.each(function( e ) {
            sortableIndexRead = e + 1;
            $(this).attr('id', 'sortable-' + e).attr('data-index', e);
            $(this).find('.sortable-index').text(sortableIndexRead);
            $(this).find('.remove').attr('data-index', e);
        });
        designSortable = $(designSortableClass);
        designsCount = designSortable.length;
        designCount.text(designsCount);
    });

    // Set target for loading in design
    $(document).on('click', '.design-sortable', function() {
        sortableIndex = $(this).data('index');
        inputSelectedPos.val(sortableIndex);
    })

    $(document).on('click', '#trigger-design-id', function() {
        inputSelectedPosVal = $('#input-selected-position').val();
        designsSelected = fnDesignsSelected();
        designSortableId = $('input[name="design_id"]').val();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            async: false,
            data: {
                action: "collection_design_sortable",
                index: inputSelectedPosVal,
                design_id: designSortableId,
                designs: designsSelected
            },
            success: function( response ) {
                if( response.duplicate === 1 ) {
                    $('#field-design-id')
                        .val('')
                        .attr('placeholder', 'Design ID already in use');
                } else if( response.html.length ) {
                    $('#sortable-' + inputSelectedPosVal).replaceWith(response.html);
                    $('.modal').modal('hide');
                    $('input[name="design_id"]').val('');
                }
                if( response.replace !== 0 ) {
                    $('#sortable-' + inputSelectedPosVal).replaceWith(fnDesignSortableBlank());
                }
            }
        });
        designsSelected = fnDesignsSelected();
    })

    function fnDesignsSelected() {
        designSelected = $(designSortableClass + '.has-design');
        if( !designSelected.length ) return {};
        designsSelected = {};
        designSelected.each(function() {
            $(this).removeData('index').removeData('design-id');
            designSortablePos = $(this).data('index');
            designSortableId = $(this).data('design-id');
            designsSelected[designSortablePos] = designSortableId;
        });
        return designsSelected;
    }

    function fnDesignSortableBlank() {
        designSortableNew = '<div id="sortable-' + sortableIndex + '" class="col-4 design-sortable" data-index="' + sortableIndex + '">';
        designSortableNew += '<h5>Design <span class="sortable-index">' + sortableIndexRead + '</span></h5>';
        designSortableNew += '<div class="inner card-display" data-target="#modal-design-search" data-toggle="modal">';
        designSortableNew += '<img class="design-placeholder"' +
            ' src="/wp-content/themes/mythic-sleeves//img/creator/design-sortable.png">';
        designSortableNew += '</div>';
        designSortableNew += '<div class="remove" data-index="' + sortableIndex + '">remove</div>';
        designSortableNew += '</div>';
        return designSortableNew;
    }

    $(document).on('click', '#submit-collection', function( e ) {
        designSlots = $(designSortableClass);
        designSelected = $(designSortableClass + '.has-design');
        if( designSlots.length !== designSelected.length ) {
            $('.notice-error').show();
            return;
        }
        designsSelected = fnDesignsSelected();
        idCollection = getUrlVars()['collection_id'] !== undefined ? getUrlVars()['collection_id'] : 0;

        $('.loading-confirm').show();

        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "manage_collection",
                designs: designsSelected,
                collection_id: idCollection
            },
            success: function( response ) {
                noticeSaved();
                // window.location.href = '/dashboard/submit-collection';
            }
        });

    });

    /**  Favorite Alters **/
    $(document).on('keyup', 'input[name="fav-term"]', function( e ) {
        if( $(this).val().length > 1 ) {
            $('#field-design-connector .loading-inline').show();
            clearTimeout(doneTyping);
            doneTyping = setTimeout(favAlter, doneTypingInterval);
        } else {
            clearTimeout(doneTyping);
        }
    });
    $(document).on('change', 'input[name="fav-term"]', function( e ) {
        favAlter();
    });

    function favAlter() {
        $('.loading-inline').show();
        let term = $('input[name="fav-term"]').val();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-alter-favoriter",
                term: term,
                mc_nonce: getNonceValByAction('as-alter-favoriter')
            },
            success: function( response ) {
                if( response.id !== 0 ) {
                    if( !$('#alter-fav-id-' + response.id).length ) $('#alter-favorites').append(response.output);
                    $('input[name="fav-term"]').val('');
                    var connections = $('#input-alter-favorited').val().length ? JSON.parse($('#input-alter-favorited').val()) : [],
                        idConnection = response.id;
                    if( !connections.includes(idConnection) ) connections.push(Number(idConnection));
                    $('#input-alter-favorited').val(JSON.stringify(connections));
                }
                $('.loading-inline').hide();
            }
        });
    }

    $(document).on('click', '.design-remove-fav', function( e ) {
        var idFav = $(this).data('design-id'),
            favs = JSON.parse($('#input-alter-favorited').val()),
            favIndex = favs.indexOf(idFav);
        if( favIndex !== -1 ) favs.splice(favIndex, 1);
        $('#input-alter-favorited').val(JSON.stringify(favs));
        $('#alter-fav-id-' + idFav).remove();
    });

    $(document).on('click', '#save-fav-alters', function( e ) {
        let favAlters = $('#input-alter-favorited').length ? $('#input-alter-favorited').val() : '';
        $('.loading').show();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-save-fav-alters",
                user_id: vars.user_id,
                designs: favAlters,
                mc_nonce: getNonceValByAction('as-save-fav-alters')
            },
            success: function( response ) {
                noticeSaved();
            }
        });
    });

    $(document).on('click', '#action-request-withdrawal', function() {

        let name = $('#input-transferwise-name').val(),
            email = $('#input-transferwise-email').val(),
            currency = $('#input-transferwise-currency').val(),
            userId = vars.user_id,
            amount = $('#input-withdraw-amount').val();

        console.log(name, email, currency, userId, amount);

        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-request-withdrawal",
                user_id: userId,
                name: name,
                email: email,
                currency: currency,
                amount: amount,
                mc_nonce: getNonceValByAction('as-request-withdrawal')
            },
            success: function( response ) {
                console.log(response);
                if( response.success === 1 ) {
                    $('#form-withdrawal').replaceWith('<p class="p-5 text-success">Successful withdrawal! Please' +
                        ' reload page for updated figures</p>');
                } else {
                    $('#withdrawal-error').fadeIn();
                }
            }
        });
    })

    $(document).on('click', '#export-statement', function() {
        const fileName = "statement.csv";
        $(this).replaceWith('<a href="javascript:void(0)" id="exporting">exporting</a>');
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                user_id: $(this).data('user-id'),
                action: "export-statement",
                mc_nonce: getNonceValByAction('export-statement')
            },
            success: function( response ) {
                let data = decodeURIComponent(escape(response.data));
                saveData(data, fileName);
                $('#exporting').replaceWith('<a href="javascript:void(0)"><strong>exported</strong></a>');
            }
        })
    });

    /** Download label CSV **/
    const saveData = ( function() {
        const a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        return function( data, fileName ) {
            const blob = new Blob([data], { type: "octet/stream" }),
                url = window.URL.createObjectURL(blob);
            a.href = url;
            a.download = fileName;
            a.click();
            window.URL.revokeObjectURL(url);
        };
    }() );


    $('form.sale_agreement').on('submit', function (e) {
        e.preventDefault();
       
        let data = {
            action: "mc_sale_agreement",
            data: {
                sale_agreement: $('#mc-sale_agreement').is(':checked')==1?1:0,
            },
            mc_nonce: getNonceValByAction('mc_sale_agreement_data')
        };

        $(this).replaceWith('<p class="text-success">Saving</p>');

        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: data,
            success: function (response) {
                console.log(response);
                location.reload();
            }
        });
    });

});
