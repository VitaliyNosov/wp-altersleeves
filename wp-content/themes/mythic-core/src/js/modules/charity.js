
    $(document).on('submit', '.charity', function( e ) {
        e.preventDefault();
        let currentForm = $(this);
        let currentFormNotices = currentForm.find('.mc-styled-form-notices').removeClass('mc-styled-form-notices-success').text('');
        let charityData = mcGetCharityFormData();
        if( !mcCheckFormRequiredFields(currentForm) ) {
            currentFormNotices.text('You need to fill all required fields!');
            return
        }

        let data = {
            action: "mc_charity",
            data: charityData,
            mc_nonce: getNonceValByAction('mc_charity_data')
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
                $('.mc-styled-form-notices').text(response.message);
            }
        });
    });

    function mcGetCharityFormData() {
        var user_id = findGetParameter('affiliate_id');

        return {
            charity_status: $('#mc-charity_status').is(':checked')==1?1:0,  
            charity_name: $('#mc-charity_name').val(),
            charity_url: $('#mc-charity_url').val(),
            charity_image: $('#mc-charity_image').val(),
            charity_reason: $('#mc-charity_reason').val(),
            user_id: user_id != undefined ? user_id : ''
        };
    }

    function mcCheckFormRequiredFields( currentForm ) {
        let currentRequiredFields = currentForm.find('.mc-required');
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

    function findGetParameter(parameterName) {
        var result = null,
            tmp = [];
        location.search
            .substr(1)
            .split("&")
            .forEach(function (item) {
              tmp = item.split("=");
              if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
            });
        return result;
    }
