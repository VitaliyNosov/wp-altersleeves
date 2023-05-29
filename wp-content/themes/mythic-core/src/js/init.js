$(function() {
    // Add Responsive wrapper to iframes
    $('iframe').each(function() {
        wrapIframe($(this));
    })

    function wrapIframe( element ) {
        if( element === undefined ) return;
        element.data('ratio', this.height / this.width).removeAttr('width').removeAttr('height');
        let vidSrc = element.attr('src');
        if( vidSrc && vidSrc.length ) {
            if( vidSrc.substr(0, 42) === "https://www.facebook.com/plugins/video.php" ) {
                element.wrap('<div class="iframe-wrapper facebook"></div>');
            } else if( vidSrc.substr(0, 29) === "https://www.youtube.com/embed" ) {
                element.wrap('<div class="iframe-wrapper youtube"></div>');
            }
        }
    }

    $('.woocommerce').on('change', 'input.qty', function() {
        $("[name='update_cart']").trigger("click");
    });

})