$(function() {
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

    $(document).on('submit', '.mc-send-promo-mailing-form', function( e ) {
        e.preventDefault();
        let currentForm = $(this);
        let currentFormNotices = currentForm.find('.mc-styled-form-notices');

        if( !mcCheckFormRequiredFields(currentForm) ) {
            currentFormNotices.text('You need to fill all required fields!');
            return
        }

        let data = {
            action: "mc_promo_mailing_send",
            data: {
                email_list: $('#mc-promo-mailing-email-list').val(),
                subject: $('#mc-promo-mailing-subject').val(),
                promotion: $('#mc-promo-mailing-promotion').val(),
                email_text: $('#mc-promo-mailing-email-text').val(),
                non_redeemers: $('#mc-promo-non-redeemers').val(),
                confirm_send: $('#mc-promo-confirm-send').val(),
                offset: $('#mc-promo-offset').val(),
                limit: $('#mc-promo-limit').val()
            },
            mc_nonce: getNonceValByAction('mc_promo_mailing_data')
        };

        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: data,
            success: function( response ) {
                if( !response ) return;
                console.log(response);

                mcChangeFormSubmitAccess(currentForm, false);

                if( response.status == 0 ) {
                    currentFormNotices.text(response.message);
                    scrollToElement(currentFormNotices);
                } else {
                    currentFormNotices.text('');
                    currentForm.find('.mc-required-fail').removeClass('mc-required-fail');
                    $('.mc-send-promo-mailing-form')[0].reset();
                    $('.mc-send-promo-mailing-form button[type="submit"]').replaceWith('<p class="text-success">' + response.message + '</p>');
                }
            }
        });
    });

    $(document).on('submit', '.mc-add-promo-mailing-form', function( e ) {
        e.preventDefault();
        let currentForm = $(this);
        let currentFormNotices = currentForm.find('.mc-styled-form-notices');

        if( !mcCheckFormRequiredFields(currentForm) ) {
            currentFormNotices.text('You need to fill all required fields!');
            return
        }

        let data = new FormData();
        let file = $('#mc_csv_file_input')[0];
        file = file.files ? file.files[0] : null;

        data.append('action', 'mc_promo_mailing_add');
        data.append('mc_nonce', getNonceValByAction('mc_promo_mailing_data'));
        data.append('name', $('#mc-promo-mailing-name').val());
        data.append('csv_file', file);

        $.ajax({
            type: "post",
            url: vars.ajaxurl,
            processData: false,
            contentType: false,
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
                    $('.mc-add-promo-mailing-form')[0].reset();
                    $('.mc-add-promo-mailing-form .mc-styled-form-notices').addClass('mc-styled-form-notices-success').text(response.message);
                }
            }
        });
    });
});