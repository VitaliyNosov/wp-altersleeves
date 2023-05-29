<?php

if( !isset( $target ) ) return;

?>

<div id="sortable-<?= $target ?>" class="col-4 design-sortable <?php if( isset( $idDesign ) ) echo 'has-design' ?>" data-index="<?= $target ?>"
     data-design-id="<?php echo isset( $idDesign ) ? $idDesign : 0 ?>">
    <?php if( isset( $idDesign ) ) :
        $imgDesign = MC_Design_Functions::imageCombined( $idDesign );
        $urlDesign = get_the_permalink( $idDesign );
        ?>
        <h5><a href="<?= $urlDesign ?>" target="_blank"><?= $idDesign ?></a></h5>
        <div class="inner card-display" data-bs-target="#modal-design-search" data-bs-toggle="modal">
            <img class="design-placeholder" src="<?= $imgDesign ?>" alt="Image for Design: <?= $idDesign ?>">
        </div>
    <?php else : ?>
        <h5>Design <span class="sortable-index"><?= ( $target + 1 ) ?></span></h5>
        <div class="inner card-display" data-bs-target="#modal-design-search" data-bs-toggle="modal">
            <img class="design-placeholder" src="<?= AS_URI_IMG ?>/creator/design-sortable.png">
        </div>
    <?php endif; ?>
    <div class="remove" data-index="<?= $target ?>">remove</div>
</div>