/**
 *
 * @param action
 */
function onVariationProductChange( action ) {
    action = action.toString();
    $(".single_variation_wrap").on("show_variation", function( event, variation ) {
        if( window[action] instanceof Function ) window[action](event, variation);
    });
}
