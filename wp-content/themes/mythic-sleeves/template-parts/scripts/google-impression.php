<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;
use Mythic_Core\System\MC_WP;

return;

if( !is_singular( 'product' ) ) return;
$product = get_queried_object();
if( empty( $product ) ) return;
$idProduct = $product->ID;
if( !has_term( 'alter', 'product_group', $idProduct ) ) return;
$product  = wc_get_product( $idProduct );
$idDesign = MC_Alter_Functions::design( $idProduct );
if( empty( $idDesign ) ) return;
$type = MC_Alter_Functions::type( $idProduct, 'name' );

$printing_id  = isset( $_GET['printing_id'] ) ? $_GET['printing_id'] : MC_Alter_Functions::printing( $idProduct );
$printing     = new MC_Mtg_Printing( $printing_id );
$namePrinting = $printing->name;
$nameAlterist = MC_WP::authorName( $idProduct );

?>

<script>
    gtag('event', 'view_item', {
        "items": [
            {
                "id": <?= $idProduct ?>,
                "name": "<?= $namePrinting ?>",
                "list_name": "<?= $type ?>",
                "brand": '<?= $nameAlterist ?>',
                "category": "Alter",
                "variant": <?= $idDesign ?>,
                "quantity": 1,
                "price": <?= $product->get_price() ?>
            }
        ]
    });
</script>
