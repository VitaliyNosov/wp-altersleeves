<?php

if( empty( $idAlterist ) ) return;
$alters = MC_Ranked_Sale::getSalesForCreator( $idAlterist, 50 );
if( empty( $alters ) ) return;

ob_start();
$designs = [];
foreach( $alters as $alter ) {
    $idAlter = $alter->product_id;
    if( !MC_Product_Functions::isAlter( $idAlter ) ) continue;
    $idDesign = MC_Alter_Functions::design( $idAlter );
    if( get_post_status( $idDesign ) !== 'publish' ) continue;
    if( in_array( $idDesign, $designs ) ) continue;
    $designs[] = $idDesign;
    $url       = get_the_permalink( $idAlter );
    $image     = MC_Alter_Functions::getCombinedImage( $idAlter );
    ?>
    <div id="<?= $idAlter ?>" class="browsing-item col-6 col-sm-3 my-3">
        <div class="text-center">
            <a href="<?= $url ?>"><img class="card-corners card-display" src="<?= $image ?>"></a>
        </div>
        <div class="row pt-2">
            <?php echo MC_Product_Functions::getPrices($idAlter); ?>
            <div class="col-auto">
                <a class="cas-add-to-cart" href="javascript:void(0);" data-printing-id="<?= MC_Alter_Functions::printingId($idAlter) ?>" data-alter-id="<?= $idAlter ?>"><i
                            class="fas fa-shopping-cart"></i></a>
                <?php Mythic_Core\Ajax\Store\Cart\MC_Add_Alter::render_nonce(); ?>
            </div>
        </div>
    </div>
    <?php
    if( count( $designs ) == 5 ) break;
}
$output = ob_get_clean();
if( empty( $output ) ) return;
?>
<hr>
<h3>Bestselling Alters</h3>
<div class="alters row mb-3 justify-content-start">
    <?php echo $output; ?>
</div>