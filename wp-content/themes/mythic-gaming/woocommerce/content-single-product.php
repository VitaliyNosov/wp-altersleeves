<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */

global $design_team, $design_team_credits;

$team_id = MC_Mythic_Frames_Functions::getProductDesignTeam( $product );

?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

    <div class="row my-4 bg-white align-items-start">

        <div class="col-md-6 block order-2 order-md-1">
            <div class="image-wrapper text-center">
                <?php
                /**
                 * Hook: woocommerce_before_single_product_summary.
                 *
                 * @hooked woocommerce_show_product_sale_flash - 10
                 * @hooked woocommerce_show_product_images - 20
                 */
                do_action( 'woocommerce_before_single_product_summary' );
                ?>

            </div>
        </div>

        <div class="col-md-6 block  order-1 order-md-2">
            <div class="summary-wrapper">
            <?php
            /**
             * Hook: woocommerce_single_product_summary.
             *
             * @hooked woocommerce_template_single_title - 5
             * @hooked woocommerce_template_single_rating - 10
             * @hooked woocommerce_template_single_price - 10
             * @hooked woocommerce_template_single_excerpt - 20
             * @hooked woocommerce_template_single_add_to_cart - 30
             * @hooked woocommerce_template_single_meta - 40
             * @hooked woocommerce_template_single_sharing - 50
             * @hooked WC_Structured_Data::generate_product_data() - 60
             */
            do_action( 'woocommerce_single_product_summary' );
            ?>
            </div>
        </div>

        <?php

        $team = MC_Mythic_Frames_Functions::getTeam( $team_id );
        if( !empty( $team ) ) : ?>
            <div class="col-12 pb-3 px-3 order-3">
                <div class="row">
                    <div class="col order-2 order-sm-1">
                        <h3 class="brand-color">#<?= $team['design_name'] ?></h3>
                        <?= $team['description'] ?>
                    </div>

                    <?php
                    $team_data        = MC_Mythic_Frames_Functions::getTeamCredits( $team_id );
                    $team_set_credits = $team_data['set'];
                    $stretch_1_image  = isset( $team['commander_2_data'][0]['fileCombinedJpgLo'] ) ? $team['commander_2_data'][0]['fileCombinedJpgLo'] : '/resources/mythic-frames/kickstarter/blank-card.jpg';

                    if( $team_set_credits >= 300 && strpos($product->get_name(), 'laymat') === false ) : ?>
                        <div class="col-sm-6 order-1 order-sm-2">
                        <h3>Stretch Goal bonuses</h3>
                        <p>This design has met <a
                                    href="https://www.mythicgaming.com/campaign/?team_id=<?= $team_id ?>">stretch goals!</a>. Purchase <strong>the set</strong> of this design to receive:</p>

                        <ul>
                        <?php if( $team_set_credits >= 400 ) : ?>
                            <li>An exclusive deckbox based on this design (image to follow soon)</li>
                        <?php endif; ?>
                            <li>A free exclusive Commander Alter Sleeve:</li>
                    </ul>

                            <?php
                            if( $stretch_1_image != '/resources/mythic-frames/kickstarter/blank-card.jpg' && !empty( $stretch_1_image ) ) : ?>
                                <img loading=lazy src="<?= $stretch_1_image ?>" class="card-display"
                                     alt="The Stretch Goal Commander design"
                                     style="max-height:150px;">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>


                </div>
        <?php endif; ?>

    </div>

    <?php
    /**
     * Hook: woocommerce_after_single_product_summary.
     *
     * @hooked woocommerce_output_product_data_tabs - 10
     * @hooked woocommerce_upsell_display - 15
     * @hooked woocommerce_output_related_products - 20
     */
    do_action( 'woocommerce_after_single_product_summary' );
    ?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>

<?php MC_Render::templatePart( 'campaign/mythic-frames', 'team-products' ); ?>

<style>

    h1.product_title {
        font-size: 24px;
        margin-bottom: 1rem;
    }

    p.price {
        font-size: 24px;
        font-weight: bold;
    }

    .single-product .image-wrapper,
    .single-product .summary-wrapper {
        margin: 0 auto;
    }

    @media only screen and (min-width: 768px) {
        .single-product .image-wrapper,
        .single-product .summary-wrapper {
            padding: 0 1rem;
        }

        .single-product .image-wrapper {
            margin: 0 0 0 auto;
        }

    }

    .single-product .image-wrapper {
        max-width: 400px;
    }

    .single-product .image-wrapper img {
        height: auto !important;
    }


    .single-product .summary-wrapper {
        max-width: 600px;
    }
</style>
