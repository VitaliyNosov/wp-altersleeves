$(document).ready(function() {

    /** General **/
    var productId,
        promotionId,
        quantity,
        featuredQuantity;

        $("[name='update_cart']").removeAttr('disabled');
        $("[name='update_cart']").trigger("click");

    /** Add Alters to Cart **/
    $(document).on('click', '.cas-add-to-cart', function() {
        productId = $(this).data('alter-id');
        let idPrinting = $(this).data('printing-id');
        quantity = 1;
        featuredQuantity = $('.cas-product-quantity');
        if( featuredQuantity.length ) quantity = featuredQuantity.val();
        addAlter(productId, idPrinting, quantity);
        $(this).removeData('alter-id');
    });

    $(document).on('click', '.cas-add-to-cart i', function() {
        $(this).replaceWith('<i class="clicked fa fa-circle-notch fa-spin fa-1g fa-fw"></i>');
    });

    $(document).on('click', '.cas-add-to-cart.cas-button', function() {
        if ( $('.cas-add-to-cart.cas-button #spinLoading').length === 0 ) {
            $('.cas-add-to-cart.cas-button').prepend('<i id="spinLoading" class="fa fa-circle-notch fa-spin fa-1g fa-fw"></i>');
        }
    });

    function addAlter( productId, idPrinting, quantity ) {
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-cart-add-alter",
                alter_id: productId,
                printing_id: idPrinting,
                quantity: quantity,
                mc_nonce: getNonceValByAction('as-cart-add-alter')
            },
            success: function( response ) {
                gtag('event', 'add_to_cart', {
                    "content_type": "product",
                    "items": [
                        {
                            "id": productId,
                            "name": response.name,
                            "list_name": response.type,
                            "brand": response.alterist,
                            "category": "Alter",
                            "variant": response.design_id,
                            "quantity": quantity,
                            "price": response.price
                        }
                    ]
                });
                showHideCartNotice();
                $('#header-cart i').css('color', '#F95C6C');
                $('.cas-add-to-cart.cas-button').html('Added');
                $('a.cas-add-to-cart .clicked').replaceWith('<i class="fas fa-check"></i>');

                const cartItems = $(".woocommerce-cart-form__cart-item.cart_item");
                for (let i = 0; i < cartItems.length; i++) {
                    const currElem = cartItems[i];
                    const elemImg = currElem.getElementsByClassName("card-display")[0];
                    const splitImgUrl = elemImg.src.split('/');
                    const cartProductId = splitImgUrl[splitImgUrl.length - 2];
                    if (cartProductId == productId) {
                        const quantityInput =  currElem.getElementsByClassName("input-text qty text")[0]
                        quantityInput.value = parseInt(quantityInput.value) + parseInt(quantity);
                        break;
                    }
                }
                if ( $(".woocommerce .cart-empty.woocommerce-info").length > 0 )
                    location.reload();
                $("[name='update_cart']").removeAttr('disabled');
                $("[name='update_cart']").trigger("click");
            }
        })
    }

    $(document).on('click', '.as-select-promotion-product', function() {
        productId = $(this).data('product-id');
        promotionId = $(this).data('promotion-id')
        addPromotionalItemToCart(productId, promotionId);
        $('.as-select-promotion-product').hide();
        $('.as-remove-promotion-product').show().removeData( 'product-id').data( 'product-id', productId );
    });

    $(document).on('click', '.as-remove-promotion-product', function() {
        productId = $(this).data('product-id');
        removeCartItemByProductId(productId);
        $('.as-select-promotion-product').show();
        $('.as-remove-promotion-product').hide();
    });

    // Change printings
    if( $('#design-0').length ) {
        $(document).on('click', '.cas-product-info-printing', function() {
            let idPrinting = $(this).data('value'),
                idAlter = $(this).data('alter-id'),
                idTarget = $(this).data('target');
            $('#design-' + idTarget + ' .cas-add-to-cart')
                .attr('data-alter-id', idAlter)
                .attr('data-printing-id', idPrinting);
            $('#design-' + idTarget + ' .as-confirm')
                .attr('data-alter-id', idAlter)
                .attr('data-printing-id', idPrinting);
        })
    }

    if( $('#select2-billing_country-container').length ) {
        updateShipping();

        $("#select2-billing_country-container").on('DOMSubtreeModified', function() {
            updateShipping();
        });
        $("#select2-shipping_country-container").on('DOMSubtreeModified', function() {
            updateShipping();
        });
    }

    function updateShipping() {
        $(document.body).trigger('update_checkout');

        let untracked = $('input[name="nl_untracked"]:checked'),
            tracked = $('input[name="nl_untracked"]:checked');

        if( !untracked.length && !tracked.length ) {
            $("#shipping_method_0_nl_untracked").attr('checked', 'checked');
            $("#shipping_method_0_nl_untracked").prop("checked", true);
        }

    }

    $(document).on('click', '.cas-add-gift-card-to-cart', function() {
        productId = $('#input-gift-card-value').val();
        quantity = 1;
        featuredQuantity = $('.cas-product-quantity');
        if( featuredQuantity.length ) quantity = featuredQuantity.val();
        addGiftCard(productId, quantity);
        $(this).removeData('alter-id');
    });

    function addGiftCard( productId, quantity ) {
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            async: false,
            data: {
                action: "as-cart-add-gift-card",
                product_id: productId,
                quantity: quantity
            },
            success: function() {
                window.location.replace('/cart');
            }
        })
    }

    $(document).on('change', 'select[name=giftcard_type]', function() {
        let digital = $(this).val(),
            product_id = $('select[name=giftcard_value]').val();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            async: false,
            data: {
                action: "as-giftcard-type",
                digital: digital,
                product_id: product_id
            },
            success: function( response ) {
                $('#field-gift-card-type').replaceWith(response.output);
                $('.cas-add-gift-card-to-cart').attr('data-product-id', response.product_id);
                $('.gift-card-price').text(response.price);
                if( digital === 1 ) {
                    $('.gift-card-type-info').hide();
                } else {
                    $('.gift-card-type-info').show();
                }
            }
        })
    });

    $(document).on('change', 'select[name=giftcard_value]', function() {
        let digital = $('select[name=giftcard_type]').val();
        productId = $(this).val();
        $('.cas-add-gift-card-to-cart').attr('data-product-id', productId);
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            async: false,
            data: {
                action: "as-cart-get-giftcard-value",
                product_id: productId,
                digital: digital
            },
            success: function( response ) {
                $('.gift-card-price').text(response.price);
                $('.cas-add-gift-card-to-cart').attr('data-product-id', response.product_id);
            }
        })
    });

    let collectionRequired = $('#as-collection-required').length ? parseInt($('#as-collection-required').val()) : 0,
        collectionAlters = [],
        collectionKey,
        collectionSelection;

    function collectionSelected() {
        collectionSelection = $('#as-collection-selected').length ? $('#as-collection-selected').val() : 0
        return collectionSelection;
    }

    /** Collections **/
    // Change printings
    if( $('#design-0').length ) {
        $(document).on('click', '.cas-product-info-printing', function() {
            let idPrinting = $(this).data('value'),
                idAlter = $(this).data('alter-id'),
                idTarget = $(this).data('target');
            $('#design-' + idTarget + ' .cas-add-to-cart')
                .attr('data-alter-id', idAlter)
                .attr('data-printing-id', idPrinting);
            $('#design-' + idTarget + ' .as-confirm')
                .attr('data-alter-id', idAlter)
                .attr('data-printing-id', idPrinting);
        })
    }

    // Initiate collection confirmation
    $(document).on('click', '.as-cart-collection', function() {
        $(this).hide();
        $('.as-notice-collection').show();
        $('.cas-add-to-cart').hide();
        $('.as-confirm').show();
        $('.product-slider--shake').removeClass('product-slider--shake');
        $('#as-collection-alters').val('');
        $('#as-collection-flash-active').val(1);
        $('body').addClass('confirming');
    });
    // Confirm Alter for collection
    $(document).on('click', '.as-confirm', function() {
        $(this).hide();

        // Data
        collectionKey = $(this).data('collection-key');
        collectionAlters = $('#as-collection-alters').val().length ? JSON.parse($('#as-collection-alters').val()) : [];
        collectionAlters[collectionKey] = {
            alter_id: $(this).data('alter-id'),
            printing_id: $(this).data('printing-id'),
        };
        $('#as-collection-alters').val(JSON.stringify(collectionAlters));

        // Visual
        $('#design-' + collectionKey + ' .as-confirm').hide();
        $('#design-' + collectionKey + ' .as-undo').show();
        $('#design-' + collectionKey + ' .as-confirmed').show();
        collectionSelection = parseInt(collectionSelected()) + 1;
        $('#as-collection-selected').val(collectionSelection);

        let isFlash = 0;
        if( $('#as-collection-flash-sale').length && $('#as-collection-flash-active').length ) {
            if( $('#as-collection-flash-sale').val() === 1 && $('#as-collection-flash-active').val() === 1 ) {
                isFlash = 1;
            }

        }

        if( collectionSelection === collectionRequired ) {
            if( $('.add2Cart--set').length ) {
                var idCollection = $('.add2Cart--set').data('product-id'),
                    choices = $('#as-collection-alters').length ? $('#as-collection-alters').val() : [];
                jQuery.ajax({
                    type: "post",
                    dataType: "json",
                    url: vars.ajaxurl,
                    data: {
                        action: "cas-add-collection",
                        alter_id: idCollection,
                        choices: choices
                    },
                    success: function( response ) {
                        console.log(response);
                        window.location.replace('/cart');
                    }
                })
            } else {
                var length = collectionAlters.length,
                    element = null;
                for( var i = 0; i < length; i++ ) {
                    element = collectionAlters[i];
                    addAlter(element['alter_id'], element['printing_id'], 1, isFlash);
                }
                reloadCartOutput();
            }

            $('.as-notice-collection').hide();
            $('.as-confirm').hide();
            $('.as-undo').hide();
            $('.as-confirmed').hide();
            $('.as-cart-collection').show();
            $('.cas-add-to-cart').show();
            $('body').removeClass('confirming');
        }
    });

    // UNDO Confirm Alter for collection
    $(document).on('click', '.as-undo', function() {
        $(this).hide();
        collectionKey = $(this).data('collection-key');
        $('#design-' + collectionKey + ' .as-confirm').show();
        $('#design-' + collectionKey + ' .as-undo').hide();
        $('#design-' + collectionKey + ' .as-confirmed').hide();
        collectionSelection = parseInt(collectionSelected()) - 1;
        $('#as-collection-selected').val(collectionSelection);
    });

    $(window).on("beforeunload", function( e ) {
        collectionSelection = $('#as-collection-selected').val();
        if( $('body').hasClass('confirming') && $('#as-collection-selected').length && $('#as-collection-required').length ) {
            if( collectionSelection !== collectionRequired ) {
                return 'You haven\'t confirmed all your alters';
            }
        }
    });

});



