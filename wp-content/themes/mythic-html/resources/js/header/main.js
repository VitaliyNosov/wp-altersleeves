$(document).ready(function($){
    $( "#header-search-input" ).focusin(function() {
        $('#header-search').addClass('active');
    }).focusout(function() {
        $('#header-search').removeClass('active');
    });

    $(".site-header li.menu-item-has-mega-menu").mouseenter( function () {
        $(this).children(".mega-menu-container").slideDown('fast');
    }).mouseleave( function () {
        $(this).children(".mega-menu-container").slideUp('fast');
    });
});