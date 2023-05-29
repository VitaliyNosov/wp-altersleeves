<?php

use Mythic_Core\Objects\Store\MC_Affiliate_Coupon;
use Mythic_Core\Users\MC_Wp_User;

if( empty( $alter_id ) ) return;
$alter           = new MC_Alter( $alter_id );
$combined_image  = $alter->getFileCombinedJpgLo();
$artist          = new MC_WP_User( $alter->artist_id );
$artist_name     = $artist->user->display_name;
$artist_nicename = $artist->user->user_nicename;
$printing_id     = $_GET['printing_id'] ?? $printing_id ?? 0;

if( empty( $printing_id ) ) $printing_id = MC_Alter_Functions::printing( $alter_id );
$url            = get_the_permalink( $alter_id ).'?printing_id='.$printing_id;
$is_publisher   = MC_User_Functions::isAdmin() || MC_Licensing_Functions::userPublisherOfProduct( $alter_id );
$link_available = ( empty( $patreon ) && empty( $promotion ) ) || $is_publisher || in_array( $alter_id,
                                                                                             MC_Licensing_Functions::getAllSharedProductIds() );
if( (int) $printing_id !== $alter->linkedPrinting ) {
    $printing    = new MC_Mtg_Printing( $printing_id );
    $imgPrinting = $printing->imgJpgNormal;
    $imgAlter    = MC_Alter_Functions::image( $alter_id );
}

?>

<div id="alter-<?= $alter_id ?>" class="alter py-2">
    <?php if( $link_available ) : ?><a href="<?= $url ?>" <?php if( !empty($promotion_id) ) : ?>target="_blank"<?php endif; ?>><?php endif ?>
        <div class="card-image-front position-relative">
            <?php if( empty( $_GET['framecode_id'] ) ) : ?>
                <?php if( (int) $printing_id !== $alter->linkedPrinting ) : ?>
                    <img class="card-display" src="<?= $imgPrinting ?>" alt="Alter for <?= $alter_id ?> by <?= $artist_name ?>">
                    <img class="card-display__alter" src="<?= $imgAlter ?>">
                <?php else : ?>
                    <img class="card-display" src="<?= $combined_image ?>" alt="Alter for <?= $alter_id ?> by <?= $artist_name ?>">
                <?php endif; ?>
            <?php else :
                
                $printing = new MC_Mtg_Printing( $printing_id );
                $printing_name = $printing->name;
                $alter_image = $alter->getFilePngLo();
                
                $card_image = $printing->imgJpgNormal;
                ?>
                <img style="position:absolute;top:0;
    left:0;" src="<?= $alter_image ?>" alt="Alter for <?= $printing_name ?> by <?= $artist_name ?>">
                <img src="<?= $card_image ?>" alt="Card image for <?= $printing_name ?>">
            <?php endif; ?>
        </div>
        <?php if( $link_available ) : ?></a><?php endif; ?>
    
    <?php if( !empty( $patreon ) ) : ?>
    <p class="text-danger py-2"><?php echo !empty( $disabled_text ) ? $disabled_text : 'Patreon Only' ?></p>
    
    <?php elseif( !empty( $promotion ) && !empty( $promotion_id ) ) :
    if( empty( $promotion_in_cart ) ) $promotion_in_cart = MC_Affiliate_Coupon::promotionalProductsInCart( $promotion_id );
    ?>
    <div class="">
        <button type="submit" class="btn btn-success mb-2 as-select-promotion-product" data-promotion-id="<?= $promotion_id ?>"
                data-product-id="<?= $alter_id ?>" <?php if( !empty( $promotion_in_cart ) ) : ?>style="display:none;"<?php endif; ?>>
            Select Product
        </button>

        <button type="submit" class="btn btn-danger mb-2 as-remove-promotion-product" data-product-id="<?= $alter_id ?>"
                <?php if( empty( $promotion_in_cart ) ) : ?>style="display:none;"<?php endif; ?>>
            Reset Selection
        </button>
    </div>
    <?php elseif( in_array( $alter_id, MC_Product_Functions::snapboltIds() ) )  : ?>
    <p>Not available</p>
    <?php else : ?>
    <div class="row pt-2">
        <?php echo MC_Product_Functions::getPrices($alter_id); ?>
        <div class="col-auto">
            <a class="cas-add-to-cart" href="javascript:void(0);" data-printing-id="<?= $printing_id ?>" data-alter-id="<?= $alter_id ?>"><i
                        class="fas fa-shopping-cart"></i></a>
            <?php Mythic_Core\Ajax\Store\Cart\MC_Add_Alter::render_nonce(); ?>
        </div>
    </div>
    <div class="pb-2">
        <a href="/alterist/<?= $artist_nicename ?>" title="Visit <?= $artist_name ?>'s profile"><small><?= $artist_name ?></small></a>
    </div>
    <?php endif; ?>

</div>