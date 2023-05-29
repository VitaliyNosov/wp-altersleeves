<?php

$cartTotal = WC()->cart->cart_contents_total;
?>

<div class="cart__total">$<span class="cart__total-amount"><?= $cartTotal ?></span></div>