$(document).ready(function() {

    let args,
        productInfoDataActiveClass = window.asLib.product.info.data.active.class,
        alterOptionsContainerFrameEras$Selector = window.asLib.product.info.options.frameEra.container.$selector,
        alterOptionsChevronFrameEras$Selector = window.asLib.product.info.options.frameEra.chevron.$selector,
        alterOptionsWrapperFrameEras$Selector = window.asLib.product.info.options.frameEra.wrapper.$selector,
        alterOptionsContainerPrintings$Selector = window.asLib.product.info.options.printing.container.$selector,
        alterOptionsChevronPrintings$Selector = window.asLib.product.info.options.printing.chevron.$selector,
        alterOptionsWrapperPrintings$Selector = window.asLib.product.info.options.printing.wrapper.$selector,
        alterSidebarButtonAddCart$Selector = window.asLib.product.sidebar.button.addToCart.$selector,
        alterSliderSelector = window.asLib.product.slider.slider.selector,
        alterSliderDownClass = window.asLib.product.slider.slider.status.down.class,
        alterSliderUpClass = window.asLib.product.slider.slider.status.up.class,
        alterSliderImageAlterSelector = window.asLib.product.slider.images.alter.selector,
        alterShakeSelector = window.asLib.product.slider.images.shake.selector;

    /** Core Functions **/
    function casAjax( args ) {
        window.asLib.ajax.post(args);
    }

    /** Internal Functions **/
    // Alter - Hide alter selection options
    window.asLib.functions.alter.hideOptions = function( e ) {
        if( !alterOptionsWrapperPrintings$Selector.is(e.target)
            && alterOptionsWrapperPrintings$Selector.has(e.target).length === 0 ) {
            alterOptionsContainerPrintings$Selector.slideUp();
            alterOptionsChevronPrintings$Selector.removeClass(productInfoDataActiveClass);
        }
        if( !alterOptionsWrapperFrameEras$Selector.is(e.target)
            && alterOptionsWrapperFrameEras$Selector.has(e.target).length === 0 ) {
            alterOptionsContainerFrameEras$Selector.slideUp();
            alterOptionsChevronFrameEras$Selector.removeClass(productInfoDataActiveClass);
        }
    };
    // Alter - switch additional printings
    window.asLib.functions.alter.switchAdditionalPrinting = function( idAlter, idPrinting, target ) {
        args = {};
        args.data = {
            action: "cas-product-switch-additional-printing",
            alter_id: idAlter,
            printing_id: idPrinting
        };
        let targetSelector = '#design-' + target;
        let infoSetName = targetSelector + ' #selected-printings',
            sliderImageAlter = targetSelector + ' .product-slider-image__alter',
            sliderImagePrinting = targetSelector + ' .product-slider-image__printing';

        args.response = function( response ) {
            $(infoSetName).text(response.set_name);
            $(sliderImageAlter).attr('src', response.image_alter);
            $(sliderImagePrinting).attr('src', response.image_printing);

            let targetButtonAdd = $(targetSelector + ' .cas-add-to-cart'),
                targetButtonConfirm = $(targetSelector + ' .as-confirm');

            if( targetButtonAdd.length || targetButtonConfirm.length ) {
                targetButtonAdd.data('alter-id', idAlter)
                targetButtonAdd.data('printing-id', idPrinting);
                targetButtonConfirm.data('alter-id', idAlter)
                targetButtonConfirm.data('printing-id', idPrinting);
            } else {
                alterSidebarButtonAddCart$Selector.attr('data-alter-id', idAlter);
                alterSidebarButtonAddCart$Selector.attr('data-printing-id', idPrinting);
            }
            if( $(targetSelector + ' .alter-id').length ) $(targetSelector + ' .alter-id').text(idAlter);
            $('#options-printings').slideUp();
        };
        casAjax(args);
    };
    // Alter - Shake
    $.fn.alterShake = function() {
        this.each(function( i ) {
            for( let x = 1; x <= 2; x++ )
                $(this).animate({ top: -20 }, 80).animate({ top: 0 }, 90);
        });
        return this;
    };

    // Alter - Slider
    function alterSlide() {
        $(document).on('click', alterSliderSelector, function() {
            let blurRadiusInit = 1,
                blurRadiusResult = 0.92,
                target = $(this),
                targetData = $(this).data('target'),
                targetSelect,
                targetId = '#as-slider-0',
                targetSelector = targetId + ' .product-slider';
            if( targetData !== 0 ) {
                targetSelect = $('#as-slider-' + targetData);
                if( targetSelect.length ) {
                    target = $('#as-slider-' + targetData + ' .product-slider');
                    targetId = '#as-slider-' + targetData;
                    targetSelector = targetId + ' .product-slider';
                }
            }

            if( target.hasClass(alterSliderUpClass) ) {
                target.addClass(alterSliderDownClass);
                target.removeClass(alterSliderUpClass);
                $(targetSelector + ' ' + alterSliderImageAlterSelector).animate({ top: '0%' });
                blurRadiusInit = 0.92;
                blurRadiusResult = 1;
            } else if( target.hasClass(alterSliderDownClass) ) {
                target.addClass(alterSliderUpClass);
                target.removeClass(alterSliderDownClass);
                $(targetSelector + ' ' + alterSliderImageAlterSelector).animate({ top: '-100%' });
            }

            $({
                blurRadius: blurRadiusInit
            }).animate({
                blurRadius: blurRadiusResult
            }, {
                duration: 400,
                step: function() {
                    target.css("opacity", this.blurRadius);
                }
            });

            targetSelector = ".product-slider-wrapper:not(" + targetId + ") ";
            if( $(targetSelector + ' .product-slider').hasClass(alterSliderUpClass) ) {
                $(targetSelector + ' .product-slider').addClass(alterSliderDownClass);
                $(targetSelector + ' .product-slider').removeClass(alterSliderUpClass);
                $(targetSelector + ' ' + alterSliderImageAlterSelector).animate({ top: '0%' });
            }

        });
    }

    // AJAX
    window.asLib.ajax = {
        post: function( args ) {
            var async = args.async === undefined ? false : args.async;
            if( args.data === undefined ) return;
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: vars.ajaxurl,
                async: async,
                data: args.data,
                success: function( response ) {
                    args.response(response);
                }
            })
        }
    };
    // Animations
    window.asLib.animations = {
        product: {
            slider: {
                shake: function() {
                    setInterval(function() {
                        $(alterShakeSelector).alterShake();
                    }, 5000);
                },
                slide: function() {
                    alterSlide();
                }
            }
        }
    }

});
