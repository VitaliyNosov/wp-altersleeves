<?php

if( empty( $team_id ) ) {
    global $product;
    if( !empty( $product ) ) $team_id = MC_Mythic_Frames_Functions::getProductDesignTeam( $product );
}

$playmats = \Mythic_Core\Functions\MC_Mythic_Frames_Functions::getPlaymatProducts( 2, $team_id ?? 0 );

?>

<!-- Modal -->
<div class="modal fade" id="preCart" tabindex="-1" aria-labelledby="preCartLabel" aria-hidden="true">
    <div class="modal-dialog my-0 modal-dialog-scrollable min-vh-100 d-inline-block end-0 float-end">
        <div class="modal-content min-vh-100">
            <div class="modal-header">
                <h4 class="modal-title" id="preCartLabel">Added to Cart!</h4>
            </div>
            <div class="modal-body d-flex aligns-items-center">

                <div>
                    <h5>You might also like</h5>
                <?php foreach( $playmats as $playmat_id ) :
                    $playmat_product = wc_get_product($playmat_id);
                    if( empty($playmat_product) ) continue;
                    $playmat_url = $playmat_product->get_permalink();
                    if (empty($playmat_url) && !empty($playmat_slug = $playmat_product->get_slug())) {
                        $playmat_url = '/product/' . $playmat_slug;
                    }

                    ?>
                <div class="w-100">
                    <a href="<?= $playmat_url ?>"
                       class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                        <h3 class="woocommerce-loop-product__title" style="height: 38px;">
                            <?= $playmat_product->get_title() ?>
                        </h3>
                        <div class="loop-thumbnail">
                            <?= $playmat_product->get_image() ?>
                        </div>
                        <?= $playmat_product->get_price_html() ?>
                        <?php if ($playmat_product->get_id() != 673) :
                            ?>
                        <div>
                            <a href="javascript:void(0);"
                               data-quantity="1"
                               class="button product_type_variation add_to_cart_button ajax_add_to_cart"
                               data-product_id="<?= $playmat_product->get_id() ?>" data-product_sku=""
                               aria-label="Add this product to your cart" rel="nofollow">Add to cart</a></div>

                        <?php elseif( $playmat_product ) : ?>
                            <a href="<?= $playmat_product->get_permalink() ?>"
                               class="button"
                               aria-label="Add this product to your cart" rel="nofollow">View product</a>
                        <?php endif ?>
                    </a>
                </div>
                <?php endforeach; ?></div>
            </div>
            <div class="modal-footer">
                <a href="/cart" title="Proceed to cart" class="button" style="width:auto;">Go to Cart</a>
            </div>
        </div>
    </div>
</div>
