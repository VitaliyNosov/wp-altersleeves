$(document).ready(function() {

    let titles = $('.woocommerce-loop-product__title'),
        products = $('.loop-thumbnail');

    $(document).on('click', '.newsletter .submit', function() {

        let permission = document.getElementById('input-marketing-permission').checked ? 1 : 0;
        let fieldEmail = $('#input-email'),
            fieldName = $('#input-name');
        if( !fieldEmail.length || !fieldName.length ) return;
        let valueEmail = fieldEmail.val(),
            valueName = fieldName.val();
        if( !valueEmail.length || !valueName.length ) return;

        let data = {
            'email': valueEmail,
            'name': valueName,
            'marketing_permission': permission,
            'listIds': [$(this).data('sib-list')],
            'mc_nonce': getNonceValByAction('newsletter-signup')
        };
        ajaxPost('newsletter-signup', data, function( response ) {
            $('.newsletter').fadeOut();
            $('.mythic-frames .youtube').fadeOut();
            $('.confirmation').fadeIn();
        });
    });

    if( titles !== undefined ) {
        titles.matchHeight();
    }

    if( products !== undefined ) {
        products.matchHeight();
    }

    $( document ).on( 'click', '.single_add_to_cart_button, .add_to_cart_button', function(e) {
        e.preventDefault();
        if( $('#preCart').is(':visible') ) return;
        const myModal = new bootstrap.Modal(document.getElementById('preCart'));
        myModal.show();
    });

    $(document).on( 'click', '#preCart .add_to_cart_button', function(e) {
        $(this).replaceWith('<strong class="brand-color">Added to cart</strong>');
    })

});

