$(document).ready(function($){
    var productGalleryNav  = $('.product-gallery-nav'),
        productGalleryMain = $('.product-gallery-main'),
        productsSlider     = $('.products-slider'),
        resultsSlider      = $('.results-slider');

    productGalleryMain.slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        asNavFor: '.product-gallery-nav',
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    fade: false,
                    slidesToShow: 1
                }
            }
        ]
    });

    productGalleryNav.slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.product-gallery-main',
        dots: false,
        arrows: false,
        vertical: true,
        focusOnSelect: true,
        initialSlide: 1,
        responsive: [
            {
                breakpoint: 1500,
                settings: {
                    slidesToShow: 3,
                }
            }
        ]
    });

    productGalleryNav.on('wheel', (function(e) {
        e.preventDefault();
      
        if (e.originalEvent.deltaY < 0) {
          $(this).slick('slickNext');
        } else {
          $(this).slick('slickPrev');
        }
    }));

    productsSlider.slick({
        slidesToShow: 4,
        arrows: false,
        dots: false,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    fade: false,
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 768,
                settings: {
                    fade: false,
                    slidesToShow: 1.1,
                    initialSlide: 1
                }
            }
        ]
    });

    resultsSlider.slick({
        slidesToShow: 3.2,
        arrows: false,
        dots: false,
        infinite: false,
        centerMode: false,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    fade: false,
                    slidesToShow: 2.1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    fade: false,
                    slidesToShow: 1.1,
                    initialSlide: 1
                }
            }
        ]
    });


    // Add to cart qty counter
    $('.qty-nav').click(function(e){
        var buttonClasses = $(e.currentTarget).prop('class'),
            input = $('.add-to-cart-qty'),
            value = +input.val();
                   
        if(buttonClasses.indexOf('increase') !== -1){
            value = value != input.prop('max') ? (value) + 1 : value;            
        } else {
            value = (value) - 1;            
        }
        value = value < 0 ? 0 : value;
        input.val(value);
    });

    $('.add-to-cart-qty').keyup(function(){
        if(this.value != ""){
            if(parseInt(this.value) < parseInt(this.min)){
                this.value = this.min;
            }
            if(parseInt(this.value) > parseInt(this.max)){
                this.value = this.max;
            }
        }
    });

});