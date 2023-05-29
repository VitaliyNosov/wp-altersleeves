jQuery(document).ready(function($) {

    var charity_creator_id = findGetParameter('charity_creator_id');
    if(charity_creator_id){
         setCookie('wdgk_product_price', 10, 1);
    }

    jQuery('body').on('click', '.product-remove .remove', function() {
        var product_sku = $(this).data('product_sku')
        var matches = product_sku.match(/donation/);
        if(matches!=null){
            setCookie('wdgk_product_price', 0, 1);
            setCookie('charity_user_id', 0, 1);
        }
    });

    jQuery('body').on('change', '.charity_select', function() {
        var optionSelected = $('option:selected', this);
        var charity_user_id = $(optionSelected).data('charity_user_id');
        setCookie('charity_user_id', charity_user_id, 1);
        $("[name='update_cart']").removeAttr('disabled');
        $("[name='update_cart']").trigger("click");
    });

    jQuery('body').on('click', '.charity_support', function() {
        var charity_user_id = $(this).data('charity_user_id');
        setCookie('charity_user_id', charity_user_id, 1);
        $(this).text('Charity Selected');
    });

    jQuery(".wdgk_donation").on('keyup', function(e) {
        if (e.keyCode == 13) {
            jQuery(this).closest('.wdgk_donation_content').find(".wdgk_add_donation").trigger("click");
        }
    });
    
    jQuery('body').on("click", ".wdgk_add_donation", function() {

        var note = "";

        var price = jQuery(this).closest('.wdgk_donation_content').find("input[name='donation-price']").val();

        if (jQuery(this).closest('.wdgk_donation_content').find('.donation_note').val()) {
            var note = jQuery(this).closest('.wdgk_donation_content').find('.donation_note').val();
        }
        var ajaxurl = jQuery('.wdgk_ajax_url').val();
        var product_id = jQuery(this).attr('data-product-id');
        var redirect_url = jQuery(this).attr('data-product-url');

        if (price == "") {
            jQuery(this).closest('.wdgk_donation_content').find(".wdgk_error_front").text("Please enter a value!!");
            return false;
        } else {
            var pattern = new RegExp(/^[0-9.*]/);
            if (!pattern.test(price) ) {
                jQuery(this).closest('.wdgk_donation_content').find(".wdgk_error_front").text("Please enter valid value!!");
                return false;
            }
        }
        if (!jQuery.isNumeric(price)) {
            jQuery(this).closest('.wdgk_donation_content').find(".wdgk_error_front").text("Please enter numeric value!!");
            return false;
        }
        jQuery(this).closest('.wdgk_donation_content').find('.wdgk_loader').removeClass("wdgk_loader_img");

        var old_price = getCookieCustom('wdgk_product_price');
        old_price = parseFloat(old_price);
        price   = parseFloat(price)

        if( old_price > 0 ){
            price = old_price + price; 
        }      

        setCookie('wdgk_product_price', price, 1);
        setCookie('wdgk_donation_note', note, 2);

        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'wdgk_donation_form',
                product_id: product_id,
                price: price,
                note: note,
                redirect_url: redirect_url
            },
            type: 'POST',
            success: function(data) {
                var redirect = jQuery.parseJSON(data);
                if (redirect.error == "true") {
                    jQuery(this).closest('.wdgk_donation_content').find(".wdgk_error_front").text("Please enter valid value!!");
                    jQuery(this).closest('.wdgk_donation_content').find('.wdgk_loader').addClass("wdgk_loader_img");
                    return false;
                } else {
                    document.location.href = redirect.url;
                }
            }
        });
    });



});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookieCustom(cname) {    
    var name = cname + "=";
    var ca = document.cookie.split(";");

    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
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