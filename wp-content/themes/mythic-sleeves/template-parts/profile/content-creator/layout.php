<?php

use Mythic_Core\Display\MC_Render;
use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;

$user        = get_queried_object();
$idUser      = $user->ID;
$name        = $user->display_name;
$description = get_the_author_meta( 'description', $idUser );
$notice      = MC_Affiliate_Coupon::checkAndApplyAffiliatesCoupons();

if( !empty( $_COOKIE['mc_af_code'] ) || !empty( $_GET['af_code'] ) ) :
    $products = MC_Affiliate_Coupon::getCurrentFreeAffiliateProducts();
    if( count( $products ) < 2 && empty( $_GET['mod'] ) ) :
        ?>
        <div class="w-100 bg-success text-center py-2 text-white pulse-banner"><a
                    href="/cart" class="text-white">Your free product has been added to your cart. Continue browsing or go to the cart to
                continue</a></div>
    <?php endif;endif ?>
    <br>

    <div class="py-3 header <?php if( !empty( $description ) ) : ?>d-md-none<?php endif; ?>">
        <h1><?= $name ?></h1>
    </div>
    <div class="info row align-items-start">
        <div class="details col-md-4">
            <?php include DIR_THEME_TEMPLATE_PARTS.'/profile/content-creator/details.php'; ?>
        </div>
        <?php if( !empty( $description ) ) : ?>
            <div class="col-md-8 info">
                <h1 class="d-none d-md-block"><?= $name ?></h1>
                <?php
                include DIR_THEME_TEMPLATE_PARTS.'/profile/content-creator/description.php';
                ?>
            </div>
        <?php endif; ?>
    </div>
<?php

ob_start();
include DIR_THEME_TEMPLATE_PARTS.'/profile/content-creator/favs.php';
$portfolio = ob_get_clean();
if( !empty( $portfolio ) ) : ?>

    <div class="row">
        <div class="bg-white">
            <?= $portfolio ?>
        </div>
    </div>
<?php endif;

MC_Render::templatePart( 'profile/alterists' );
MC_Render::templatePart( 'profile/bestselling' );
