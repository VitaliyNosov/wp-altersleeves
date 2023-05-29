<?php

if( empty( $team_id ) ) {
    global $product;
    if( !empty( $product ) ) $team_id = MC_Mythic_Frames_Functions::getProductDesignTeam( $product );
}
if( empty( $team_id ) ) $team_id = rand( 1, 12 );
$team_name  = MC_Mythic_Frames_Functions::getTeamName( $team_id );
$is_playmat = !empty( $product ) && has_term( 'playmat', 'product_cat' );
$is_mf      = !empty( $product ) && has_term( 'mythic frames', 'product_cat' );
$products   = [];

// First get playmat
$playmat_id = MC_Mythic_Frames_Functions::getPlaymatIdByTeam( $team_id );
$mf_id      = MC_Mythic_Frames_Functions::getMythicFramesProductByTeamId( $team_id );
if( !$is_playmat && !empty( $playmat_id ) && get_post_status( $playmat_id ) == 'publish' ) {
    $product    = wc_get_product( $playmat_id );
    $products[] = [
        'id'    => $product->get_id(),
        'url'   => $product->get_permalink(),
        'image' => $product->get_image(),
        'name'  => $product->get_title(),
        'price' => $product->get_price_html(),
    ];
}

// Get remaining products
$remaining = 4 - count( $products );

$parent_product = new WC_Product_Variable( $mf_id );
$product        = wc_get_product( $mf_id );
$url            = $product->get_permalink();
$variations     = $parent_product->get_children();
shuffle( $variations );
$count = 0;
foreach( $variations as $product ) {
    if( $count >= $remaining ) break;
    $single_variation = new WC_Product_Variation( $product );
    $products[]       = [
        'id'           => $parent_product->get_id(),
        'variation_id' => $single_variation->get_id(),
        'url'          => $url.$single_variation->get_permalink(),
        'image'        => $single_variation->get_image(),
        'name'         => $single_variation->get_title(),
        'price'        => $single_variation->get_price_html(),
    ];
    $count++;
}
if( !empty( $products ) ) : ?>
    <div class="row products">
        <h2 class="mb-3 col-12"><a href="/campaign?team_id=<?= $team_id ?>">Products by Team
                <?= $team_name ?></a>
        </h2>
        <?php foreach( $products as $product ) :

            $name = $product['name'];
            if( has_term( 'land', 'pa_pack-type', $product['variation_id'] ) ) {
                $name = 'Mythic Frames Land Booster';
            } else {
                if( has_term( 'creature', 'pa_pack-type', $product['variation_id'] ) ) {
                    $name = 'Mythic Frames Creature Booster';
                } else {
                    if( has_term( 'non-creature', 'pa_pack-type', $product['variation_id'] ) ) {
                        $name = 'Mythic Frames Non-Creature Booster';
                    }
                }
            }

            ?>
            <div class="col-sm-6 col-md-3 p-0 p-sm-3 mb-3 pt-sm-0">
                <div class="bg-white p-3">
                    <a href="<?= $product['url'] ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                        <h3 class="woocommerce-loop-product__title" style="height: 38px;">
                            <?= $name ?>
                        </h3>
                        <div class="loop-thumbnail">
                            <?= $product['image'] ?>
                        </div>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>