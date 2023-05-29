$(function() {

    $(document).on('submit', '.mc-new-prod-share-form', function( e ) {
        e.preventDefault();
        let currentForm = $(this);
        let currentFormNotices = currentForm.find('.mc-styled-form-notices').removeClass('mc-styled-form-notices-success').text('');
        let productRightsShareData = mcGetRegisterNewProdShareFormData();
        if( !mcCheckFormRequiredFields(currentForm) ) {
            currentFormNotices.text('You need to fill all required fields!');
            return
        }
        if(!productRightsShareData.artistId || productRightsShareData.artistId == '' || productRightsShareData.artistId == 0){
            $('.mc-new-prod-share-form #mc-af-edit-existing-search-input').addClass('mc-required-fail');
            currentFormNotices.text('You need to fill all required fields!');
            return
        }
        let data = {
            action: "mcRegisterNewProductRightsShare",
            productRightsShareData: productRightsShareData,
            mc_nonce: getNonceValByAction('mcRegisterNewProductRightsShare')
        };

        mcChangeFormSubmitAccess(currentForm);
        $request = $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: data,
            success: function( response ) {
                if( !response ) return;
                mcChangeFormSubmitAccess(currentForm, false);
                if( response.status == 0 ) {
                    currentFormNotices.text(response.message);
                    scrollToElement(currentFormNotices);
                } else {
                    $('.mc-new-prod-share-form')[0].reset();
                    $('#mc-prod-share-publisher-id').val(0);
                    $('.mc-new-prod-share-form .as-clear-search').removeClass('as-clear-search-active');
                    currentFormNotices.addClass('mc-styled-form-notices-success').text(response.message);
                }
            }
        });
    });

    $(document).on('click', '.mc-prod-share-action', function( e ) {
        e.preventDefault();
        let currentElement = $(this);
        if(!confirm('Are you sure you want to '+currentElement.text()+' the invitation?')) return;
        let currentTabPane = currentElement.closest('.tab-pane');
        if( !currentTabPane.length ) return;
        let userType = 'publisher';
        let currentShareId = currentElement.attr('data-prod-share-id');
        let newShareStatus = currentElement.attr('data-prod-share-new-status');
        if( currentTabPane.hasClass('artist_all_shares') ) {
            userType = 'artist';
        }
        let data = {
            action: "mcUpdateProductRightsShare",
            currentShareId: currentShareId,
            newShareStatus: newShareStatus,
            userType: userType,
            mc_nonce: getNonceValByAction('mcUpdateProductRightsShare')
        };
        currentElement.addClass('as_disabled_element');
        $request = $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: data,
            success: function( response ) {
                if( !response ) return;
                let currentStatusContainer = currentElement.closest('.mc-table-flex-row').find('.mc-table-flex-td-key-status p');
                if( response.status == 0 ) {
                    currentStatusContainer.text(response.message);
                } else {
                    currentStatusContainer.text(response.share_status);
                    currentElement.parent().html(response.actions);
                }
            }
        });
    });

    $(document).on('mcFillArtistSharesData', '.mc-prod-shares-artist', function() {
        let currentElement = $(this);
        let data = {
            action: "mcGetAffiliateCouponDataAdmin",
            userType: 'artist',
            userId: $('#mc-prod-share-publisher-id').val(),
            mc_nonce: getNonceValByAction('mcGetAffiliateCouponDataAdmin')
        };
        currentElement.addClass('as_disabled_element');
        let currentLoader = $('.mc-prod-share-artist-loader').find('.loader-animation').addClass('loader-animation-active');
        $request = $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: data,
            success: function( response ) {
                currentLoader.removeClass('loader-animation-active');
                currentElement.removeClass('as_disabled_element');
                if( !response || !response.status ) return;
                currentElement.html(response.html)
            }
        });
    });

    $(document).on('mcFillPublisherSharesData', '.mc-prod-shares-publisher', function(  ) {
        let currentElement = $(this);
        let data = {
            action: "mcGetAffiliatePublishingDataAdmin",
            userType: 'publisher',
            userId: $('#mc-prod-share-current-publisher-id').val(),
            mc_nonce: getNonceValByAction('mcGetAffiliatePublishingDataAdmin')
        };
        currentElement.addClass('as_disabled_element');
        let currentLoader = $('.mc-prod-share-publisher-loader').find('.loader-animation').addClass('loader-animation-active');
        $request = $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: data,
            success: function( response ) {
                currentLoader.removeClass('loader-animation-active');
                currentElement.removeClass('as_disabled_element');
                if( !response || !response.status ) return;
                currentElement.html(response.html)
            }
        });
    });


    function mcGetRegisterNewProdShareFormData() {
        return {
            artistId: $('#mc-prod-share-artist-id').val(),
            publisherId: $('#mc-prod-share-publisher-id').val(),
            productIds: $('#mc-prod-share-new-product-ids').val(),
        };
    }

    function mcCheckFormRequiredFields( currentForm ) {
        let currentRequiredFields = currentForm.find('input.mc-required');
        let formIsValid = true;
        if( !currentRequiredFields.length ) return formIsValid;
        currentRequiredFields.each(function() {
            let currentField = $(this);
            if(currentField.attr('type') == 'checkbox') {
                if(!currentField.is(':checked')){
                    currentField.addClass('mc-required-fail');
                    formIsValid = false;
                }
            } else if(currentField.val() == '') {
                currentField.addClass('mc-required-fail');
                formIsValid = false;
            }
        });

        return formIsValid;
    }

    function mcChangeFormSubmitAccess( currentForm, isDisabled = true ) {
        let currentSubmit = currentForm.find('button[type="submit"]');
        let currentLoader = currentForm.find('.mc-styled-form-submit .loader-animation');
        if( isDisabled ) {
            currentSubmit.addClass('as_disabled_element');
            currentLoader.addClass('loader-animation-active');
            return;
        }

        currentSubmit.removeClass('as_disabled_element');
        currentLoader.removeClass('loader-animation-active');
    }


});