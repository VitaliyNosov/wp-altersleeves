<?php

if( !is_singular( 'product' ) ) return;
$product = get_queried_object();
if( empty( $product ) ) return;
$idProduct = $product->ID;
$product = wc_get_product( $idProduct );

?>

<script>
    gtag('event', 'view_item', {
        "items": [
            {
                "id": <?= $idProduct ?>,
                "name": "<?= get_the_title( $idProduct ) ?>",
                "brand": 'Mythic Gaming',
                "category": '<?= has_term( 'Playmat', 'product_category', $idProduct ) ? 'Playmat' : 'Mythic Frames' ?>',
                "variant": <?= $idProduct ?>,
                "quantity": 1,
                "price": <?= $product->get_price() ?>
            }
        ]
    });
</script>
