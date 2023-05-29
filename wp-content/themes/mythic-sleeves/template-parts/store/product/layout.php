<?php

use Mythic_Core\Functions\MC_Product_Functions;

$object = get_queried_object();
if( empty( $object ) ) return;
$idProduct = $object->ID;
$type      = MC_Product_Functions::type( $idProduct );
if( empty( $type ) ) return;
$file = DIR_THEME_TEMPLATE_PARTS.'/store/product/types/'.$type.'/content.php';
if( !file_exists( $file ) ) return; ?>
<div id="product" class="product">
    
    <?php
    if( $type == 'collection' ) : ?>
        <div class="wing bg-white-wing"></div>
        <div class="container content bg-white-content">
            <?php include $file; ?>
        </div>
        <div class="wing bg-white-wing"></div>
    <?php else : ?>
        
        <?php include $file; ?>
    
    <?php endif; ?>

</div>