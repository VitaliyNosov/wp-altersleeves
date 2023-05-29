<?php

use Mythic_Core\Functions\MC_Mask_Cutter_Functions;

if( empty( $element_id ) ) return;

global $wpdb;

if( empty( $variation_id ) ) {
    $wpdb->insert( MC_Mask_Cutter_Functions::TABLE_ELEMENT_VARIATIONS, [
        'element' => $element_id,
        'name'    => '',
    ] );
    $variation_id = $wpdb->insert_id;
}
$variation           = MC_Mask_Cutter_Functions::getElementVariationById( $variation_id );
$variation_id        = $variation->id ?? 0;
$variation_name      = $variation->name ?? '';
$variation_file      = $variation->file ?? '';
$variation_mask_maps = MC_Mask_Cutter_Functions::getMaskMapsByVariationId( $variation_id );

?>

<div id="target-<?= $variation_id ?>" class="card element-variation w-100 mb-3">
    <div class="card-body">
        <div class="mb-3 mb-3">
            <label for="input-variation-name-<?= $variation_id ?>" class="col-form-label sr-only fw-bold">Variation Name</label>
            <input type="text" class="form-control" id="input-variation-name-<?= $variation_id ?>" placeholder="Variation Name"
                   name="variation_name_<?= $variation_id ?>" value="<?= $variation_name ?>">
        </div>

        <div class="form-file">
            <input type="file" class="form-file-input variation-file-upload " data-target="<?= $variation_id ?>"
                   id="variation-file-upload-<?= $variation_id ?>">
            <label class="form-file-label form-label" for="variation-file-upload-<?= $variation_id ?>">Choose file</label>
            <input type="hidden" id="variation-file-<?= $variation_id ?>" name="variation_file_<?= $variation_id ?>" value="<?= $variation_file ?>">
        </div>

        <div id="variation-file-<?= $variation_id ?>" class="variation-file-image bg-dark rounded my-3">
            <?php
            if( !empty( $variation_file ) ) : ?>
                <img class="p-3" src="<?= $variation_file ?>" style="width:100%">
            <?php
            endif; ?>
        </div>
        
        <?php
        if( !empty( $variation_mask_maps ) ) : ?>
            <h5>Mask Maps</h5>
            <ul id="variation-mask-maps">
                <?php
                foreach( $variation_mask_maps as $variation_mask_map ) :
                    $mask_map_name = MC_Mask_Cutter_Functions::getMaskMapById( $variation_mask_map )->name;
                    ?>
                    <li><?= $mask_map_name ?></li>
                <?php
                endforeach; ?>
            </ul>
        <?php
        endif; ?>

        <div class="text-center my-2">
            <a href="javascript:void(0);" data-target="<?= $variation_id ?>" class="action-init-delete delete-hide btn btn-primary red--button w-100">Delete</a>
            <a href="javascript:void(0);" data-target="<?= $variation_id ?>" data-type="element-variation"
               class="action-confirm-delete delete-check btn btn-primary green--button w-100" style="display: none;">Confirm</a>
        </div>

    </div>
</div>