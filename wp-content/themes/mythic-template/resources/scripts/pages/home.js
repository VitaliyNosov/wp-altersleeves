$(document).ready(function($){
    var heroSlider     = $('.hero-slider'),
        sectionSlider = $('.section-slider');

    heroSlider.slick({
        slidesToShow: 3,
        arrows: true,
        dots: false,
        infinite: false,
        centerMode: false,
        variableWidth: true,
        appendArrows: $('.hero-slider-controls'),
        prevArrow: $('.slide-prev'),
        nextArrow: $('.slide-next'),
        speed: 500,
        initialSlide: 1,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 1,
                    centerMode: true,
                    centerPadding:'30px',
                    variableWidth: false,
                    initialSlide: 2
                }
            }
        ]
    });

    sectionSlider.slick({
        slidesToShow: 4,
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
                    slidesToShow: 1.2
                }
            }
        ]
    });

});
