$(function() {

    let $request = null;

    if( $('.mc-datepicker').length ) {
        $('.mc-datepicker').datepicker({
            dateFormat: 'yy-m-d'
        });
    }

    let conditionalFields = jQuery('.mc-conditional-field');
    if( conditionalFields.length ) {
        conditionalFields.each(function() {
            let currentField = jQuery(this);
            let currentConditionalParent = jQuery('#' + currentField.attr('data-mc-conditional-field'));
            if( currentConditionalParent.attr('type') !== 'checkbox' ) return;
            if( currentConditionalParent.length ) {
                currentConditionalParent.on('change', function() {
                    if( jQuery(this).is(':checked') ) {
                        currentField.addClass('mc-conditional-field-active');
                    } else {
                        currentField.removeClass('mc-conditional-field-active');
                    }
                });
				currentConditionalParent.trigger('change');
            }
        })
    }

    $(document).on('submit', '.mc-af-add-new-affiliate-form', function( e ) {
        e.preventDefault();
        let currentForm = $(this);
        let currentFormNotices = currentForm.find('.mc-styled-form-notices');
        if( !mcCheckFormRequiredFields(currentForm) ) {
            currentFormNotices.text('You need to fill all required fields!');
            return
        }
        let affiliateData = mcGetRegisterNewAffiliateFormData();
        let data = {
            action: "mcRegisterNewAffiliate",
            affiliateData: affiliateData,
            mc_nonce: getNonceValByAction('mc_affiliate_data')
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
                    currentFormNotices.text('');
                    currentForm.find('.mc-required-fail').removeClass('mc-required-fail');
                    $('.mc-af-add-new-affiliate-form')[0].reset();
                    affiliateData['userId'] = response.new_user_id;
                    affiliateData['isAffiliate'] = 1;
                    mcFillExistingUserData(affiliateData);
                    $('#mc-af-edit-existing-search-input').val(affiliateData['firstName'] + ' ' + affiliateData['lastName']);
                    new bootstrap.Tab($('.nav-link[href="#edit_existing_affiliate"]')).show();
                    $('.mc-af-edit-existing-container').slideDown();
                    $('.mc-af-edit-existing-container .mc-styled-form-notices').addClass('mc-styled-form-notices-success').text('New affiliate was added!');
                }
            }
        });
    });

    $(document).on('submit', '.mc-af-edit-existing-affiliate-form', function( e ) {
        e.preventDefault();
        let currentForm = $(this);
        let currentFormNotices = currentForm.find('.mc-styled-form-notices');
        currentFormNotices.removeClass('mc-styled-form-notices-success').text('');
        if( !mcCheckFormRequiredFields(currentForm) ) {
            currentFormNotices.text('You need to fill all required fields!');
            return
        }
        let affiliateData = mcGetUpdateAffiliateFormData();
        let data = {
            action: "mcUpdateAffiliate",
            affiliateData: affiliateData,
            mc_nonce: getNonceValByAction('mc_affiliate_data')
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
                    currentFormNotices.addClass('mc-styled-form-notices-success').text('Affiliate data was updated!');
                }
            }
        });
    });

    $(document).on('keyup', '.mc-required-fail', function() {
        let currentField = $(this);
        if( currentField.val() != '' ) {
            currentField.removeClass('mc-required-fail');
            currentField.closest('form').find('.mc-styled-form-notices').text('');
        }
    });

    $(document).on('change', '.mc-required-fail[type="checkbox"]', function() {
        let currentField = $(this);
        if( currentField.is(':checked') ) {
            currentField.removeClass('mc-required-fail');
            currentField.closest('form').find('.mc-styled-form-notices').text('');
        }
    });

    $(document).on('mcFillUserDataById', '.mc-af-edit-existing-affiliate-form', function( e ) {
        let currentFormNotices = $('.mc-af-edit-existing-affiliate-form .mc-styled-form-notices');
        currentFormNotices.removeClass('mc-styled-form-notices-success').text('');
        let data = {
            action: "mcGetAffiliateData",
            userId: $('#mc-af-edit-existing-user-id').val(),
            mc_nonce: getNonceValByAction('mc_affiliate_data')
        };
        $('.mc-af-edit-existing-loader .loader-animation').addClass('loader-animation-active');
        $('.mc-af-edit-existing-container').addClass('as_disabled_element');
        $request = $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: data,
            success: function( response ) {
                if( !response ) return;
                $('.mc-af-edit-existing-loader .loader-animation').removeClass('loader-animation-active');
                $('.mc-af-edit-existing-container').removeClass('as_disabled_element').slideDown();
                if( response.status == 0 ) {
                    currentFormNotices.text(response.message);
                    scrollToElement(currentFormNotices);
                } else {
                    mcFillExistingUserData(response.userData);
                }
            }
        });
    });

    function mcFillExistingUserData( affiliateData ) {
        $('#mc-af-edit-existing-email').val(affiliateData['email']);
        $('#mc-af-edit-existing-username').val(affiliateData['username']);
        if( affiliateData['isAffiliate'] == 1 ) {
            $('#mc-af-edit-existing-is-affiliate').attr('checked', true);
        } else {
            $('#mc-af-edit-existing-is-affiliate').attr('checked', false);
        }
        $('#mc-af-edit-existing-affiliate-user-role').val(affiliateData['affiliateUserRole']);
        $('#mc-af-edit-existing-affiliate-url').val(affiliateData['affiliateUrl']);
        $('#mc-af-edit-existing-f-name').val(affiliateData['firstName']);
        $('#mc-af-edit-existing-l-name').val(affiliateData['lastName']);
        $('#mc-af-edit-existing-monthly-fee').val(affiliateData['monthlyFee']);
        $('#mc-af-edit-existing-monthly-fee-currency').val(affiliateData['monthlyFeeCurrency']);
        $('#mc-af-edit-existing-date-come').val(affiliateData['dateOfComesIn']);
        $('#mc-af-edit-existing-user-id').val(affiliateData['userId']);
        $('#mc-affiliate-panel-link').attr('href', affiliateData['userId']);
    }

    function mcGetRegisterNewAffiliateFormData() {
        return {
            email: $('#mc-af-register-email').val(),
            username: $('#mc-af-register-username').val(),
            isAffiliate: $('#mc-af-register-is-affiliate').is(':checked') ? 1 : 0,
            affiliateUserRole: $('#mc-af-register-affiliate-user-role').val(),
            affiliateUrl: $('#mc-af-register-affiliate-url').val(),
            firstName: $('#mc-af-register-f-name').val(),
            lastName: $('#mc-af-register-l-name').val(),
            password: $('#mc-af-register-password').val(),
            monthlyFee: $('#mc-af-register-monthly-fee').val(),
            monthlyFeeCurrency: $('#mc-af-register-monthly-fee-currency').val(),
            dateOfComesIn: $('#mc-af-register-date-come').val(),
            sendEmail: $('#mc-af-register-send-email').is(':checked') ? 1 : 0,
            sendEmailText: $('#mc-af-register-send-email-text').val()
        };
    }

    function mcGetUpdateAffiliateFormData() {
        return {
            email: $('#mc-af-edit-existing-email').val(),
            username: $('#mc-af-edit-existing-username').val(),
            isAffiliate: $('#mc-af-edit-existing-is-affiliate').is(':checked') ? 1 : 0,
            affiliateUrl: $('#mc-af-edit-existing-affiliate-url').val(),
            affiliateUserRole: $('#mc-af-edit-existing-affiliate-user-role').val(),
            firstName: $('#mc-af-edit-existing-f-name').val(),
            lastName: $('#mc-af-edit-existing-l-name').val(),
            monthlyFee: $('#mc-af-edit-existing-monthly-fee').val(),
            monthlyFeeCurrency: $('#mc-af-edit-existing-monthly-fee-currency').val(),
            dateOfComesIn: $('#mc-af-edit-existing-date-come').val(),
            userId: $('#mc-af-edit-existing-user-id').val()
        };
    }

    function mcCheckFormRequiredFields( currentForm ) {
        let currentRequiredFields = currentForm.find('input.mc-required');
        let formIsValid = true;
        if( !currentRequiredFields.length ) return formIsValid;
        currentRequiredFields.each(function() {
            let currentField = $(this);
            if( currentField.attr('type') == 'checkbox' ) {
                if( !currentField.is(':checked') ) {
                    currentField.addClass('mc-required-fail');
                    formIsValid = false;
                }
            } else if( currentField.val() == '' ) {
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

    $(document).on('submit', '.mc-af-add-new-coupon-form', function( e ) {
        e.preventDefault();
        let currentForm = $(this);
        let currentFormNotices = currentForm.find('.mc-styled-form-notices');
        if( !mcCheckFormRequiredFields(currentForm) ) {
            currentFormNotices.text('You need to fill all required fields!');
            return
        }
        let affiliateCouponData = mcGetRegisterNewAffiliateCouponFormData();
        let data = {
            action: "mcRegisterNewAffiliateCoupon",
            affiliateCouponData: affiliateCouponData,
            mc_nonce: getNonceValByAction('mc_affiliate_coupon_data')
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
                    $('.mc-af-add-new-coupon-form')[0].reset();
                    $('.mc-af-edit-existing-coupon-form')[0].reset();
                    $('.mc-af-add-new-coupon-form input[type="checkbox"]').trigger('change');
                    mcFillExistingCouponsList(response.couponsList);
                    affiliateCouponData.currentAffiliateCoupons = response.newCouponId;
                    affiliateCouponData.promotionCodes = response.couponData.promotionCodes;
                    mcFillAffiliateCouponsData(affiliateCouponData);
                    // $('.nav-link[href="#edit_existing_promotion"]').trigger('click');
					new bootstrap.Tab($('.nav-link[href="#edit_existing_promotion"]')).show();
                    $('.mc-af-edit-existing-container').slideDown();
                    $('.mc-af-edit-existing-container .mc-styled-form-notices').addClass('mc-styled-form-notices-success').text('New affiliate coupon was added!');
                }
            }
        });
    });

    $(document).on('submit', '.mc-af-edit-existing-coupon-form', function( e ) {
        e.preventDefault();
        let currentForm = $(this);
        let currentFormNotices = currentForm.find('.mc-styled-form-notices');
        currentFormNotices.removeClass('mc-styled-form-notices-success').text('');
        if( !mcCheckFormRequiredFields(currentForm) ) {
            currentFormNotices.text('You need to fill all required fields!');
            return
        }
        let affiliateCouponData = mcGetUpdateAffiliateCouponFormData();
        let data = {
            action: "mcUpdateAffiliateCoupon",
            affiliateCouponData: affiliateCouponData,
            mc_nonce: getNonceValByAction('mc_affiliate_coupon_data')
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
                    mcFillAffiliateCouponsData(response.couponData);
                    currentFormNotices.addClass('mc-styled-form-notices-success')
                        .text('Affiliate coupon data was updated!');
                    scrollToElement(currentFormNotices);
                }
            }
        });
    });

    $(document).on('keyup', '#mc-af-register-free-products-list, #mc-af-edit-existing-free-products-list', function() {
        let currentElement = $(this);
        let currentElementVal = currentElement.val();
        let currentQuantityElement = '';
        if( currentElement.attr('id') == 'mc-af-register-free-products-list' ) {
            currentQuantityElement = $('#mc-af-register-free-products-quantity')
        } else {
            currentQuantityElement = $('#mc-af-edit-existing-free-products-quantity')
        }
        if( currentElementVal == '' ) {
            currentQuantityElement.val(0).attr('disabled', true)
        } else {
            let currentMaximumArray = currentElementVal.split(',').filter(function( el ) {
                return el != '';
            });
            let currentMaximum = currentMaximumArray.length;
            currentQuantityElement.attr('disabled', false).attr('max', currentMaximum);
            if( currentQuantityElement.val() > currentMaximum ) {
                currentQuantityElement.val(currentMaximum)
            }
        }
    });

    $(document).on('keyup', '#mc-af-register-free-products-quantity, #mc-af-edit-existing-free-products-quantity', function() {
        let currentElement = $(this);
        let currentMaximum = currentElement.attr('max');
        if( currentMaximum < currentElement.val() ) {
            currentElement.val(currentMaximum)
        }
    });

    $(document).on('mcFillCouponsDataByUserId', '.mc-af-edit-existing-coupon-form', function() {
        let currentLoader = $('.mc-af-edit-existing-loader .loader-animation').addClass('loader-animation-active');
        let currentContainer = $('.mc-af-edit-existing-container');

        currentLoader.addClass('loader-animation-active');
        currentContainer.removeClass('mc-af-edit-existing-container-active');
        $('.mc-af-edit-existing-coupon-form')[0].reset();
        $('.mc-promotions-codes-container').html('');

        let data = {
            action: "mcGetAffiliateCouponData",
            affiliateId: $('#mc-af-edit-existing-user-id-coupons').val(),
            mc_nonce: getNonceValByAction('mc_affiliate_coupon_data')
        };

        $request = $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: data,
            success: function( response ) {
                currentLoader.removeClass('loader-animation-active');
                currentContainer.addClass('mc-af-edit-existing-container-active');
                if( !response ) return;
                if( response.status == 1 ) {
                    mcFillExistingCouponsList(response.couponsList);
                    mcFillAffiliateCouponsData(response.couponData);
                }
            }
        });
    });

    $(document).on('change', '#mc-af-edit-existing-coupons-list', function() {
        let data = {
            action: "mcGetAffiliateCouponData",
            affiliateCouponId: $(this).val(),
            mc_nonce: getNonceValByAction('mc_affiliate_coupon_data')
        };
        let currentLoader = $('.mc-af-edit-existing-loader .loader-animation').addClass('loader-animation-active');
        let currentFormNotices = $('.mc-af-edit-existing-coupon-form .mc-styled-form-notices');
        let currentFrom = $('.mc-af-edit-existing-coupon-form').addClass('as_disabled_element');
        $('.mc-promotions-codes-container').html('');

        $request = $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: data,
            success: function( response ) {
                currentLoader.removeClass('loader-animation-active');
                currentFrom.removeClass('as_disabled_element');
                if( !response ) return;
                if( response.status == 0 ) {
                    currentFormNotices.text(response.message);
                    scrollToElement(currentFormNotices);
                } else {
                    $('.mc-af-edit-existing-coupon-form')[0].reset();
                    mcFillAffiliateCouponsData(response.couponData);
                    scrollToElement(currentFormNotices);
                }
            }
        });
    });

    function mcFillExistingCouponsList( couponsList ) {
        let listSelect = $('#mc-af-edit-existing-coupons-list');
        listSelect.html('');
        for( let couponId in couponsList ) {
            listSelect.prepend('<option value="' + couponId + '">' + couponsList[couponId] + '</option>');
        }
    }

    function mcFillAffiliateCouponsData( couponData ) {
        if( couponData['withDiscount'] == 1 ) {
            $('#mc-af-edit-existing-coupon-type').val(couponData['couponType']);
            $('#mc-af-edit-existing-coupon-value').val(couponData['discountValue']);
        } else {
            $('#mc-af-edit-existing-coupon-value').val(0);
        }
        mcSetCheckboxState($('#mc-af-edit-existing-with-discount'), couponData['withDiscount']);
        mcSetCheckboxState($('#mc-af-edit-existing-approved'), couponData['promotionApproved']);
        // mcSetCheckboxState($('#mc-af-edit-existing-free-untracked-shipping'), couponData['freeUntrackedShipping']);
        // mcSetCheckboxState($('#mc-af-edit-existing-free-tracked-shipping'), couponData['freeTrackedShipping']);
        mcSetCheckboxState($('#mc-af-edit-existing-free-products'), couponData['freeProducts']);
        if(couponData['freeProducts'] != 0){
        	$('#mc-af-edit-existing-free-products-quantity').val(couponData['freeProducts'])
		}
        mcSetCheckboxState($('#mc-af-edit-existing-add-their'), couponData['alwaysAddCouponAndTracking']);
        // mcSetCheckboxState($('#mc-af-edit-existing-highlighted-using'), couponData['highlightedUsing']);

        $('#mc-af-edit-existing-coupons-list').val(couponData['currentAffiliateCoupons']);
        $('#mc-af-edit-existing-promotion-title').val(couponData['promotionTitle']);
        $('#mc-af-edit-existing-free-products-list').val(couponData['freeProductsList']);
        $('#mc-af-edit-existing-redirect-link').val(couponData['redirectLink']);

        if( couponData.promotionCodes ) {
            $('.mc-promotions-codes-container').html(couponData.promotionCodes);
        }

        $('.mc-af-edit-existing-coupon-form input[type="checkbox"]').trigger('change');
    }

    function mcGetRegisterNewAffiliateCouponFormData() {
    	let freeProducts = 0,
            allProductsToCart = 0;
    	if($('#mc-af-register-free-products').is(':checked')){
			freeProducts = 1;
    		let freeProductsQuantity = $('#mc-af-register-free-products-quantity').val();
    		if(freeProductsQuantity != '' && freeProductsQuantity != 0) freeProducts = freeProductsQuantity;
		}
    	if($('#mc-af-all-products-to-cart').is(':checked')) {
            allProductsToCart = 1;
        }
        return {
            userId: $('#mc-af-edit-existing-user-id-coupons').val(),
            promotionTitle: $('#mc-af-register-promotion-title').val(),
            promotionNumberOfCodes: $('#mc-af-register-number-of-codes').val(),
            promotionApproved: $('#mc-af-register-approved').is(':checked') ? 1 : 0,
            withDiscount: $('#mc-af-register-with-discount').is(':checked') ? 1 : 0,
            couponType: $('#mc-af-register-coupon-type').val(),
            discountValue: $('#mc-af-register-coupon-value').val(),
            // freeUntrackedShipping: $('#mc-af-register-free-untracked-shipping').is(':checked') ? 1 : 0,
            // freeTrackedShipping: $('#mc-af-register-free-tracked-shipping').is(':checked') ? 1 : 0,
            freeProducts: freeProducts,
            freeProductsList: $('#mc-af-register-free-products-list').val(),
            allProductsToCart: allProductsToCart,
            redirectLink: $('#mc-af-register-redirect-link').val(),
            alwaysAddCouponAndTracking: $('#mc-af-register-add-their').is(':checked') ? 1 : 0,
            // highlightedUsing: $('#mc-af-register-highlighted-using').is(':checked') ? 1 : 0,
            // promotionExpiryDate: $('#mc-af-register-expiry-date').val(),
            // promotionPopupMessage: $('#mc-af-register-promotion-popup-message').val()
        };
    }

    function mcGetUpdateAffiliateCouponFormData() {
		let freeProducts = 0;
		if($('#mc-af-edit-existing-free-products').is(':checked')){
			freeProducts = 1;
			let freeProductsQuantity = $('#mc-af-edit-existing-free-products-quantity').val();
			if(freeProductsQuantity != '' && freeProductsQuantity != 0) freeProducts = freeProductsQuantity;
		}
        return {
            couponId: $('#mc-af-edit-existing-coupons-list').val(),
            userId: $('#mc-af-edit-existing-user-id-coupons').val(),
            promotionTitle: $('#mc-af-edit-existing-promotion-title').val(),
            promotionApproved: $('#mc-af-edit-existing-approved').is(':checked') ? 1 : 0,
            withDiscount: $('#mc-af-edit-existing-with-discount').is(':checked') ? 1 : 0,
            couponType: $('#mc-af-edit-existing-coupon-type').val(),
            discountValue: $('#mc-af-edit-existing-coupon-value').val(),
            // freeUntrackedShipping: $('#mc-af-edit-existing-free-untracked-shipping').is(':checked') ? 1 : 0,
            // freeTrackedShipping: $('#mc-af-edit-existing-free-tracked-shipping').is(':checked') ? 1 : 0,
            freeProducts: freeProducts,
            freeProductsList: $('#mc-af-edit-existing-free-products-list').val(),
            redirectLink: $('#mc-af-edit-existing-redirect-link').val(),
            alwaysAddCouponAndTracking: $('#mc-af-edit-existing-add-their').is(':checked') ? 1 : 0,
            // highlightedUsing: $('#mc-af-edit-existing-highlighted-using').is(':checked') ? 1 : 0,
            // promotionExpiryDate: $('#mc-af-edit-existing-expiry-date').val(),
            // promotionPopupMessage: $('#mc-af-edit-existing-promotion-popup-message').val()
        };
    }

    function mcSetCheckboxState( checkboxElement, isChecked = 0 ) {
        isChecked = isChecked != 0;
        checkboxElement.attr('checked', isChecked);
    }

    $(window).on('resize', function() {
        checkAndMoveSearchResultsContainer();
    });

    $(document).on('click', 'body', function( e ) {
        if( $(e.target).hasClass('mc-af-edit-existing-search-input') ) return;
        $(".mc-af-edit-existing-search-results").removeClass("mc-af-edit-existing-search-results-active");
    });

    $(document).on('click', '.mc-af-edit-existing-search-results', function( e ) {
        e.stopPropagation();
    });

    $(document).on('focus', '.mc-af-edit-existing-search-input', function() {
        let currentContainer = $(this).closest('.mc-af-edit-existing-search');
        if( currentContainer.find('.mc-af-edit-existing-search-results li').length ) currentContainer.find(".mc-af-edit-existing-search-results").addClass("mc-af-edit-existing-search-results-active");
    });

    $(document).on('keyup', ".mc-af-edit-existing-search-input", function() {
        let currentElement = $(this);
        let searchValue = currentElement.val();

        if( searchValue == '' ) {
            checkAndMaybeAbortRequest();
            hideSearchResultsContainer(currentElement);
            activateOrDeactivateElements(currentElement, null, false);
            return;
        }

        activateOrDeactivateElements(currentElement);
        searchAutocomplete(searchValue, currentElement);
    });

    $(document).on('click', '.as-clear-search', function( e ) {
        e.preventDefault();
        let currentElement = $(this);
        if( !currentElement.parent().hasClass('mc-af-edit-existing-search-input-container') ) return;
        checkAndMaybeAbortRequest();
        currentElement.removeClass('as-clear-search-active').siblings('.mc-af-edit-existing-search-input').val('');
        activateOrDeactivateElements(currentElement, 'loader-animation', false);

        hideSearchResultsContainer(currentElement);

        let currentTabPane = currentElement.closest('.tab-pane');
        if( currentTabPane.length && currentTabPane.hasClass('new_share') ) {
            $('#mc-prod-share-publisher-id').val(0);
        }
    });

    $(document).on('click', '.mc-user-search-result', function( e ) {
        e.preventDefault();
        let currentElement = $(this);
        let currentContainer = currentElement.closest('.mc-af-edit-existing-search');
        currentContainer.find('.mc-af-edit-existing-search-input').val(currentElement.text());
        let currentTabPane = currentElement.closest('.tab-pane');
        let currentUserId = currentElement.attr('data-mc-user-id');
        if( currentTabPane.hasClass('edit_existing_affiliate') ) {
            $('#mc-af-edit-existing-user-id').val(currentUserId);
            $('.mc-required-fail').removeClass('mc-required-fail');
            $('.mc-af-edit-existing-affiliate-form').trigger('mcFillUserDataById');
        } else if( currentTabPane.hasClass('promotions_by_affiliate') ) {
            $('#mc-af-edit-existing-user-id-coupons').val(currentUserId);
            $('.mc-af-edit-existing-user-coupons').slideDown();
            $('.mc-af-edit-existing-coupon-form').trigger('mcFillCouponsDataByUserId');
        } else if( currentTabPane.hasClass('new_share') ) {
            $('#mc-prod-share-publisher-id').val(currentUserId);
        } else if( currentTabPane.hasClass('admin_artist_shares') ) {
            $('#mc-prod-share-publisher-id').val(currentUserId);
            $('.mc-prod-shares-artist').trigger('mcFillArtistSharesData');
        } else if( currentTabPane.hasClass('admin_publisher_shares') ) {
            $('#mc-prod-share-current-publisher-id').val(currentUserId);
            $('.mc-prod-shares-publisher').trigger('mcFillPublisherSharesData');
        }

        hideSearchResultsContainer(currentElement);
    });

    function searchAutocomplete( searchValue, searchInput ) {
        checkAndMaybeAbortRequest();

        let data = {
            'action': "searchAutocompleteUsers",
            'search_term': JSON.stringify(searchValue),
            'mc_nonce': getNonceValByAction('searchAutocompleteUsers')
        };

        if( searchInput.hasClass('mc-affiliates-search') ) {
            data.role_search = 'affiliate';
        } else if( searchInput.hasClass('mc-alterists-search') ) {
            data.role_search = 'alterist';
        }

        $request = $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: data,
            success: function( response ) {
                if( !response ) return;

                checkAndFillSearchAutocompleteResults(response, searchInput);
                activateOrDeactivateElements(searchInput, 'loader-animation', false);
            }
        });

    }

    function checkAndMaybeAbortRequest() {
        if( $request != null ) {
            $request.abort();
            $request = null;
        }
    }

    function activateOrDeactivateElements( siblingElement, elementCLasses = null, needsActivation = true ) {
        if( elementCLasses == null ) {
            elementCLasses = ['loader-animation', 'as-clear-search'];
            elementCLasses.forEach(function( currentClass ) {
                activateOrDeactivateElementSingle(siblingElement, currentClass, needsActivation);
            });

            return;
        }

        activateOrDeactivateElementSingle(siblingElement, elementCLasses, needsActivation);
    }

    function activateOrDeactivateElementSingle( searchInput, elementCLass, needsActivation ) {
        let currentSiblings = searchInput.siblings('.' + elementCLass);
        if( needsActivation ) {
            currentSiblings.addClass(elementCLass + '-active');
        } else {
            currentSiblings.removeClass(elementCLass + '-active');
        }
    }

    function checkAndFillSearchAutocompleteResults( response, searchInput ) {
        if( checkIfResultsAreHidden(searchInput) ) {
            showSearchResultsContainer(searchInput);
        } else {
            clearSearchResultsContainer(searchInput);
        }
        let resultsContainer = $('.mc-af-edit-existing-search-results');
        let noResultsContainer = $('.mc-af-search-autocomplete-no-results');
        if( searchInput !== null ) {
            resultsContainer = searchInput.closest('.mc-af-edit-existing-search').find('.mc-af-edit-existing-search-results');
            noResultsContainer = searchInput.closest('.mc-af-edit-existing-search').find('.mc-af-search-autocomplete-no-results');
        }
        if( response.users.length ) {
            fillSearchAutocompleteUl(response.users, searchInput);
            resultsContainer.show();
            noResultsContainer.hide();
        } else {
            resultsContainer.hide();
            noResultsContainer.show();
        }

    }

    function fillSearchAutocompleteUl( users, searchInput = null ) {
        let ulElement = $('.mc-af-edit-existing-search-results');
        if( searchInput != null ) {
            ulElement = searchInput.closest('.mc-af-edit-existing-search').find('.mc-af-edit-existing-search-results')
        }

        users.forEach(function( liElement ) {
            ulElement.append(createSearchResultsLi(liElement));
        });
    }

    function createSearchResultsLi( LiElement ) {
        return "<li><a href='#' class='mc-user-search-result' data-mc-user-id='" + LiElement.id + "'>" + LiElement.name + "</a></li>";
    }

    function hideSearchResultsContainer( currentElement ) {
        if( checkIfResultsAreHidden(currentElement) ) return;
        $('.mc-af-edit-existing-search-results').removeClass('mc-af-edit-existing-search-results-active');
        clearSearchResultsContainer(currentElement);
    }

    function clearSearchResultsContainer( searchInput = null ) {
        if( searchInput !== null ) {
            searchInput.closest('.mc-af-edit-existing-search').find('.mc-af-edit-existing-search-results').html('');
        } else {
            $('.mc-af-edit-existing-search-results').html('');
        }
    }

    function showSearchResultsContainer( searchInput ) {
        let searchResultsContainers = $('.mc-af-edit-existing-search-results');
        if( searchInput !== null ) {
            searchResultsContainers = searchInput.closest('.mc-af-edit-existing-search').find('.mc-af-edit-existing-search-results');
        }
        searchResultsContainers.addClass('mc-af-edit-existing-search-results-active');
        checkAndMoveSearchResultsContainer(searchInput);
    }

    function checkAndMoveSearchResultsContainer( currentSearchInput = null ) {
        let searchContainers = $('.mc-af-edit-existing-search');
        if( currentSearchInput !== null ) {
            searchContainers = currentSearchInput.closest('.mc-af-edit-existing-search');
        }
        if( !searchContainers.length ) return;
        searchContainers.each(function() {
            let currentContainer = $(this);
            if( checkIfResultsAreHidden(currentContainer) ) return;
            let resultsContainer = currentContainer.find('.mc-af-edit-existing-search-results');
            let currentSearchContainer = currentContainer.find('.mc-af-edit-existing-search-input-container');
            let currentOffset = currentSearchContainer.offset();
            let newTopPosition = currentOffset.top + currentSearchContainer.outerHeight();

            resultsContainer.css({
                // top: newTopPosition,
                // left: currentOffset.left,
                width: currentSearchContainer.width()
            })
        });
    }

    function checkIfResultsAreHidden( currentElement ) {
        let currentContainer = currentElement.closest('.mc-af-edit-existing-search');
        return !currentContainer.find('.mc-af-edit-existing-search-results').hasClass('mc-af-edit-existing-search-results-active');
    }
});