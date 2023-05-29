$(document).ready(function($){
    var featuredWorks      = $('.featured-works-slider'),
        otherArtists       = $('.profile-artists-slider');

    featuredWorks.slick({
        slidesToShow: 1.4,
        arrows: false,
        dots: false,
        infinite: false,
        centerMode: false,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    fade: false,
                    slidesToShow: 1.2
                }
            }
        ]
    });

    otherArtists.slick({
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