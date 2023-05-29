<?php

$idCollection = isset( $_GET['collection_id'] ) ? $_GET['collection_id'] : 0;
$singles      = MC_Collection_Functions::singles( $idCollection );
$countSingles = !empty( $singles ) ? count( $singles ) : 3;
?>

<p><strong>Designs in Collection:</strong> <span class="design-count"><?= $countSingles ?></span></p>
<div class="row align-items-start">
    <div class="col-9">
        <div class="designs-sortable row">
            <?php
            if( !empty( $singles ) ) {
                foreach( $singles as $target => $single ) {
                    $idAlter  = $single['default_id'];
                    $idDesign = MC_Alter_Functions::design( $idAlter );
                    include 'design.php';
                }
            } else {
                for( $target = 0; $target <= 2; $target++ ) include 'design.php';
            }
            ?>
        </div>
    </div>
    <div class="col-3">
        <div class="row">
            <div class="design-add col-12">
                <h5>Add a design</span></h5>
                <div class="inner card-display">
                    <img class="design-placeholder" src="<?= AS_URI_IMG ?>/creator/design-add.png">
                </div>
            </div>
            <div class="design-less  col-12">
                <h5>Remove design</span></h5>
                <div class="inner card-display">
                    <img class="design-placeholder" src="<?= AS_URI_IMG ?>/creator/design-less.png">
                </div>
            </div>
        </div>
    </div>
</div>
<input id="input-selected-position" type="hidden" value="0">

<style>
    .designs-sortable {
        align-items: start;
        justify-content: start;
        position: relative;
    }

    .design-add,
    .design-less,
    .design-sortable {
        margin: 1rem 0;
        text-align: center;
        padding-bottom: 2rem;
    }

    .design-sortable .inner {
        width: 140px;
        position: relative;
        background: #f7f7f7;
        margin: 0 auto;
    }


    .design-sortable .inner,
    .design-sortable img {
        border-radius: 5px;
    }

    .design-less .inner,
    .design-add .inner {
        max-width: 140px;
        background: #e0e0e0;
        border-radius: 5px;
        position: relative;
    }

    .design-sortable .remove {
        position: absolute;
        width: 100%;
        text-align: center;
        bottom: 0;
        left: 0;
        padding: 0.25rem 0;
        color: #F95C6C;
    }

    .design-sortable .remove:hover {
        cursor: pointer;
    }
</style>