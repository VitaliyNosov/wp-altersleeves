<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

$code = $_GET['af_code'] ?? '';
if( empty( $code ) ) return;
$coupon_id    = MC_Affiliate_Coupon::getPromotionIdByCode( $code );
$promotion_id = MC_Affiliate_Coupon::getPromotionIdByCouponId( $coupon_id );
$products     = MC_Affiliate_Coupon::getAffiliatePromotionProductsFromId( $promotion_id );
$purchasable  = MC_Affiliate_Coupon::promotionProductsBuyableById( $promotion_id );

if( count( $products ) < 2 ) return;
?>

<!-- Modal -->
<div class="modal fade show" id="promotionProductsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width:900px">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row justify-content-between align-items-center w-100">
                    <div class="col-sm-auto text-center text-sm-start">
                        <h5 class="modal-title">Select your Reward</h5>
                    </div>
                    <div class="col-sm-auto text-center text-sm-end next-action" style="display:none;">
                        <a href="/cart" title="Go to the cart page to confirm your order">
                            <button type="button" class="btn btn-success">Checkout</button>
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Keep Shopping</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <p>Congratulations! You've earned a reward, make your selection below:</p>
                <div class="row">
                    <?php foreach( $products as $product_id ) : ?>
                        <div id="189798" class="browsing-item col-12 col-sm-6 col-md-4 my-3">
                            <?php MC_Alter_Functions::render( [ 'alter_id' => $product_id, 'promotion' => true, 'promotion_id' => $promotion_id ] ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if( $purchasable ) : ?>
                    <p>To purchase copies of these designs in addition to your reward, click on their image after selection to add more to your
                        cart</p>
                <?php endif; ?>
            </div>
            <!--
            <div class="modal-footer">
                <a href="/cart" title="Go to the cart page to confirm your order">
                    <button type="button" class="btn btn-success">Checkout</button>
                </a>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Keep Shopping</button>
            </div>
            -->
        </div>
    </div>
</div>

<?php if( empty( $_GET['mod'] ) ) return; ?>

<script type="text/javascript">
    $(window).on('load', function() {
        $('#promotionProductsModal').modal('show');
    });
</script>