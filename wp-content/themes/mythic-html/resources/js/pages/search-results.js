
$(document).ready(function($){
    $(".btn-toggle-filters").on('click', function() {
        var filtersContainer = $('.filter-container');
        if ($(this).hasClass('active')) {
            filtersContainer.slideUp();
        } else {
            filtersContainer.slideDown();
        }
        $(this).toggleClass('active');
    });
});