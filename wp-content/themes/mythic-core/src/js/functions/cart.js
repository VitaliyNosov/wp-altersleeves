/**
 * Adds promotional product to cart
 * @param productId
 * @param promotionId
 */
function addPromotionalItemToCart( productId, promotionId ) {
    ajaxPost('mc-add-promotional-item-to-cart', {  product_id: productId, promotion_id: promotionId }, function( response ) {
        showHideCartNotice();
        $('#promotionProductsModal .next-action').show();
        return response;
    })
}


/**
 * Removes Woocommerce cart item by product id
 * @param productId
 * @param success
 * @param error
 */
function removeCartItemByProductId( productId, success, error ) {
    if( !productId ) return;
    ajaxPost('mc-remove-cart-item-by-product-id', {  product_id: productId }, function( response ) {
        if( typeof success === 'function' ) success(response);
    })
}

/**
 *
 * @param args
 */
function showHideCartNotice( args = {down: 400, delay:5000, up:400, message : '', color: ''}) {
    let cartNotice = $('.notice-cart');
    if( !cartNotice.length ) return;
    cartNotice.slideDown(args.down).delay(args.delay).slideUp(args.up);
    if( !args.message.length ) return;
    cartNotice.html(args.message);
}