<?php

if (empty($team_id)) {
	global $product;
	if (!empty($product)) $team_id = MC_Mythic_Frames_Functions::getProductDesignTeam($product);
}
$reassign = $product;
if (empty($team_id)) $team_id = rand(1, 12);
$team_name = MC_Mythic_Frames_Functions::getTeamName($team_id);
$is_playmat = !empty($product) && has_term('playmat', 'product_cat');
$is_mf = !empty($product) && has_term('mythic frames', 'product_cat');
$products = [];

// First get playmat
$playmat_id = MC_Mythic_Frames_Functions::getPlaymatIdByTeam($team_id);
$mf_id = MC_Mythic_Frames_Functions::getMythicFramesProductByTeamId($team_id);
if (!$is_playmat && !empty($playmat_id) && get_post_status($playmat_id) == 'publish') {
	$playmat_product = wc_get_product($playmat_id);
	$playmat_url = $playmat_product->get_permalink();
	if (empty($playmat_url) && !empty($playmat_slug = $playmat_product->get_slug())) {
		$playmat_url = '/product/' . $playmat_slug;
	}
	$products[] = [
		'id' => $playmat_product->get_id(),
		'url' => $playmat_url,
		'image' => $playmat_product->get_image(),
		'name' => $playmat_product->get_title(),
		'price' => $playmat_product->get_price_html()
	];
}

// Get remaining products
$remaining = 4 - count($products);

$parent_product = new WC_Product_Variable($mf_id);
$product = wc_get_product($mf_id);
$url = $product->get_permalink();
$variations = $parent_product->get_children();
shuffle($variations);
$count = 0;
foreach ($variations as $product) {
	if ($count >= $remaining) break;
	$single_variation = new WC_Product_Variation($product);
	$single_variation_url = $single_variation->get_permalink();
	if (
		(empty($single_variation_url) || substr($single_variation_url, 0, 1) == '?') &&
		!empty($single_variation_slug = $parent_product->get_slug())
	) {
		$single_variation_url = '/product/' . $single_variation_slug . $single_variation_url;
	}
	$products[] = [
		'id' => $parent_product->get_id(),
		'variation_id' => $single_variation->get_id(),
		'url' => $single_variation_url,
		'image' => $single_variation->get_image(),
		'name' => $single_variation->get_title(),
		'price' => $single_variation->get_price_html()
	];
	$count++;
}
if (!empty($products)) : ?>
	<div class="row products">
		<h2 class="mb-3 col-12"><a href="/campaign?team_id=<?= $team_id ?>">Products by Team
				<?= $team_name ?></a>
		</h2>
		<?php foreach ($products as $product) :
			if (empty($product['url'])) {
				$product['url'] = get_permalink($product['id']);
			}

			$name = $product['name'];
			if( isset($product['variation_id'])) {
                if (has_term('land', 'pa_pack-type', $product['variation_id'])) {
                    $name = 'Mythic Frames Land Booster';
                } else if (has_term('creature', 'pa_pack-type', $product['variation_id'])) {
                    $name = 'Mythic Frames Creature Booster';
                } else if (has_term('non-creature', 'pa_pack-type', $product['variation_id'])) {
                    $name = 'Mythic Frames Non-Creature Booster';
                }
            }

			?>
			<div class="col-sm-6 col-md-3 p-0 p-sm-3 mb-3 pt-sm-0">
				<div class="bg-white p-3">
					<a href="<?= $product['url'] ?>"
					   class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
						<h3 class="woocommerce-loop-product__title" style="height: 38px;">
							<?= $name ?>
						</h3>
						<div class="loop-thumbnail">
							<?= $product['image'] ?>
						</div>
						<?= $product['price'] ?>
                    </a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif;

$product = $reassign;