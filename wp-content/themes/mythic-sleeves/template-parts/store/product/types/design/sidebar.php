<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\System\MC_WP;

if( !isset( $idAlter ) ) return;

if( is_user_logged_in() ) {
    $idUser    = wp_get_current_user()->ID;
    $idCreator = MC_WP::authorId( $idAlter );
    if( $idUser && $idUser == $idCreator ) $price = 2.5;
}
$printing_id = isset( $_GET['printing_id'] ) ? $_GET['printing_id'] : MC_Alter_Functions::printing( $idAlter );

$is_publisher    = MC_User_Functions::isAdmin() || MC_Licensing_Functions::userPublisherOfProduct( $idAlter );
$add_unavailable = get_post_status( $idAlter ) != 'publish' && !$is_publisher;

$cookie = $_COOKIE['coupon'] ?? '';
if( !empty($cookie) ) {
    $coupon_id    = MC_Affiliate_Coupon::getPromotionIdByCode( $cookie );
    $promotion_id = MC_Affiliate_Coupon::getPromotionIdByCouponId( $coupon_id );
    $purchasable = MC_Affiliate_Coupon::promotionProductsBuyableById( $promotion_id );
}
?>
<aside class="bg-blue cas-product-sidebar cas-product-alter-sidebar cas-product-action col-md-3">
    <div class="cas-product-sidebar-content-container cas-product-alter-sidebar-content-container">
        <div class="cas-product-sidebar-content cas-product-alter-sidebar-content">
            <?php if( $add_unavailable && empty($purchasable) ) : ?>
                <h3 class="text-white my-4">This product is currently featured but not for sale.</h3>
            <?php else : ?>
                <div class="cas-product-sidebar-price"><?php echo MC_Product_Functions::getPrices($idAlter); ?></div>
                <p class="cas-product-sidebar-info">Quantity</p>
                <p>
                    <input id="quantity-<?= $idAlter ?>" class="cas-product-sidebar-input--text cas-product-sidebar-input cas-product-quantity"
                           type="number" value="1">
                </p>
                <button class="cas-add-to-cart cas-button" data-printing-id="<?= $printing_id ?>" data-alter-id="<?= $idAlter ?>">Add to cart</button>
                <?php Mythic_Core\Ajax\Store\Cart\MC_Add_Alter::render_nonce(); ?>
            <?php endif; ?>
        </div>
    </div>
</aside>
