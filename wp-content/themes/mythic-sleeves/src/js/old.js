$(document).ready(function() {

    let idAlter,
        idCard,
        idPrinting,
        optionsSet,
        productInfoDataSelector = window.asLib.product.info.data.selector,
        productInfoDataActiveClass = window.asLib.product.info.data.active.class,
        alterOptionInfoPrintingSelector = window.asLib.product.info.options.printing.selector,
        alterSliderSelector = window.asLib.product.slider.slider.selector,
        alterShakeClass = window.asLib.product.slider.images.shake.class,
        alterShakeSelector = window.asLib.product.slider.images.alter.selector;

    /**
     *  Executables
     **/
    /** Product -- Alter **/
    // Info -- Open options
    $(document).on('click', productInfoDataSelector, function() {
        optionsSet = $(this).data('options');

        let target = $(this).parents('.as-product-info').length ? $(this).parents('.as-product-info').data('target') : '';
        $('#design-' + target + ' .options-' + optionsSet).slideToggle();
        $('#design-' + target + ' #chevron-' + optionsSet).toggleClass(productInfoDataActiveClass);
    });
    $(document).mouseup(function( e ) {
        window.asLib.functions.alter.hideOptions(e);
    });
    // Info -- Select Additional Printing
    $(document).on('click', alterOptionInfoPrintingSelector,
        function() {
            let target = $(this).parents('.as-product-info').data('target'),
                idAlter = $(this).data('alter-id'),
                idPrinting = $(this).data('value');
            window.asLib.functions.alter.switchAdditionalPrinting(idAlter, idPrinting, target);
        });
    // Shake
    window.asLib.animations.product.slider.shake();
    $(document).on('click', alterSliderSelector,
        function() {
            $(alterShakeSelector).removeClass(alterShakeClass);
        });
    // Slide
    window.asLib.animations.product.slider.slide();

    $(document).on('click', '.cas-modal-product-alter-frame-card-search-result a', function() {
        idAlter = $('body').data('id');
        idCard = $(this).data('card-id');
        window.asLib.functions.alter.selectCard(idAlter, idCard);
    });

    $(document).on('click', '.cas-modal-product-alter-frame-printing-search-result a', function() {
        idAlter = $(this).data('alter-id');
        idPrinting = $(this).data('printing-id');
        window.asLib.functions.alter.selectPrinting(idAlter, idPrinting);
    });

    $(document).on('click', '.action-fav', function() {
        let _ = $(this),
            idAlter = $(this).data('alter-id');
        let buttonText = '<button data-alter-id="' + idAlter + '" class="red--button action-unfav">UNFAVORITE</button>';
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-fav-alter",
                user_id: vars.user_id,
                alter_id: idAlter
            },
            success: function() {
                _.replaceWith(buttonText);
            }
        })
    })

    $(document).on('click', '.action-unfav', function() {
        let _ = $(this),
            idAlter = $(this).data('alter-id');
        let buttonText = '<button data-alter-id="' + idAlter + '" class="green--button' +
            ' action-fav">FAVORITE</button>';
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-unfav-alter",
                user_id: vars.user_id,
                alter_id: idAlter
            },
            success: function() {
                _.replaceWith(buttonText);
            }
        })
    })

});

window.onresize = function() {
    setModalFullContentHeight();
}

function setModalFullContentHeight() {
    if( !$('div.modal-full') ) return;
    let max_height = $(window).height() - 60 - 42 - 70 - 30;
    $('div.modal-full .tab-content').css('max-height', max_height + 'px');
    $('div.modal-full #info-block').css('max-height', max_height + 'px');
}