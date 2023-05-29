<?php

use Mythic_Core\Functions\MC_Mask_Cutter_Functions;
use Mythic_Core\Functions\MC_Mtg_Card_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;

$mask_maps = MC_Mask_Cutter_Functions::getMaskMaps();
?>

<div class="mb-3">
    <a href="/cutter-management?role=edit&target=map">
        <button>Create Mask Map</button>
    </a>
</div>

<div class="row align-items-start justify-content-start">
    
    <?php
    foreach( $mask_maps as $mask_map ) :
        $mask_map_id = $mask_map->id;
        $name = $mask_map->name;
        $printing_id = $mask_map->printing ?? 0;
        $printing = new MC_Mtg_Printing( $printing_id );
        $printing_image = !empty( $printing_id ) ? $printing->imgJpgNormal : MC_Mtg_Card_Functions::unavailableCardImg();
        ?>
        <div id="target-<?= $mask_map_id ?>" class="col-6 col-sm-4  mb-3">
            <div class="card">
                <div class="text-center py-2 fw-bold"><a href="/cutter-preview?printing_id=<?= $printing_id ?>">Link to cutter</a></div>

                <div class="p-2 text-center"><img class="card-img-top" src="<?= $printing_image ?>" alt="Card image cap" style="max-width:150px;">
                </div>

                <div class="card-body">
                    <p class="card-title fw-bold"><?= $name ?></p>
                    <div class="d-flex justify-content-between">
                        <a href="/cutter-management?role=edit&target=map&id=<?= $mask_map_id ?>"
                           class="btn btn-primary blue--button w-100 mx-2">Edit</a>
                        <a href="javascript:void(0);" data-target="<?= $mask_map_id ?>"
                           class="action-init-delete delete-hide btn btn-primary red--button w-100 mx-2">Delete</a>
                        <a href="javascript:void(0);" data-target="<?= $mask_map_id ?>" data-type="map"
                           class="action-confirm-delete delete-check btn btn-primary green--button w-100 mx-2" style="display: none;">Confirm</a>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endforeach; ?>
</div>