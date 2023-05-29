<?php

if( !MC_WOO_ACTIVE ) return;

?>

<div id="header-control-cart" class="col-auto order-3 order-md-4 pl-0 hvr-push">
    <span class="cart-count"></span>
    <a id="header-cart" href="<?= wc_get_cart_url() ?>" class="cart-icon <?php do_action( 'mc_class_cart_has_items' ) ?>">
        <i class="fas fa-shopping-cart"></i>
    </a>
</div>