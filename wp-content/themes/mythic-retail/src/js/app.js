$(document).ready(function() {


    $(document).on('click', '.add-to-cart', function() {
        let productId = $(this).data('product-id');
        let quantity = $('#product-'+productId).val();

        var data = {
            action: 'woocommerce_ajax_add_to_cart',
            product_id: productId,
            quantity: quantity,
            product_sku: '',
            variation_id: 0
        };

        $(document.body).trigger('adding_to_cart', [data]);

        $.ajax({
            type: 'post',
            url: wc_add_to_cart_params.ajax_url,
            data: data,
            success: function (response) {
                console.log(response);
                $('#added-'+productId).show();
            },
        });
    });


});

