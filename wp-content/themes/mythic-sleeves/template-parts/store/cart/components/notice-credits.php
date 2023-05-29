<?php

$pretext = 'You have credits and/or rewards';
if( is_user_logged_in() ) {
    $credits = MC_WP::meta( '_download_credits' );
    $pretext = 'You have '.$credits.' credits and/or rewards';
    if( $credits == 1 ) $creditWord = 'You have '.$credits.' credit and/or reward';
}
?>

<div class="cart__notice pr-3">
    <?= $pretext ?> on your account that must be redeemed in their own order before you can purchase additional designs
    with currency. We will combine orders for you afterwards and ship them together.
</div>
