
$(document).ready(function($){
    $(".pass-view-toggler").on('click', function() {
        var passwordField = $(this).parents('.input-group').find('input');
        if ($(this).hasClass('show-password')) {
            passwordField.attr("type", "password");
        } else {
          passwordField.attr("type", "text");
        }
        $(this).toggleClass('show-password');
    });

    $('.login-box .btn-change-action').on('click', function() {
        var action = $(this).data('target');
        console.log(action);
        $('.login-box').attr('active-action', action);
    });
});