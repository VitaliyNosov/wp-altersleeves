<?php

use Mythic_Core\Functions\MC_Mask_Cutter_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;

$component          = 'map';
$map_id             = isset( $_GET['save'] ) ? MC_Mask_Cutter_Functions::saveMap( $_GET ) : $_GET['id'] ?? 0;
$map                = !empty( $map_id ) ? MC_Mask_Cutter_Functions::getMaskMapById( $map_id ) : '';
$map_name           = !empty( $map->name ) ? $map->name : '';
$map_description    = !empty( $map->description ) ? $map->description : '';
$map_framecode_id   = $map->framecode ?? 0;
$map_framecode_name = get_term_by( 'term_id', $map_framecode_id, 'frame_code' );
$map_framecode_name = !empty( $map_framecode_name ) ? $map_framecode_name->name : '';
$map_printing_id    = !empty( $printing_id ) ? $printing_id : $map->printing ?? 0;
$map_printing       = new MC_Mtg_Printing( $map_printing_id );
$map_printing_image = $map_printing->imgJpgNormal;

$elements     = MC_Mask_Cutter_Functions::getElements();
$map_elements = !empty( $map_id ) ? MC_Mask_Cutter_Functions::getElementIdsByMaskMap( $map_id ) : [];

$preconfigurations = MC_Mask_Cutter_Functions::getPreconfigurations();

if( !empty( $map ) ) : ?>
    <h3 class="mb-4">You are Editing: <?= $map_name ?></h3>
<?php
else : ?>
    <h3 class="mb-4">Create new Element</h3>
<?php
endif; ?>

<form>

    <div class="mb-3 row align-items-start">
        <label for="input-map-name" class="col-sm-2 col-form-label fw-bold">Name</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="input-map-name" placeholder="Mask Map Name" value="<?= $map_name ?>" name="map_name">
        </div>
    </div>

    <div class="row align-items-start mt-4">
        <div class="col-sm">
            <!-- Card Search -->
            <div id="field-cutter-card-search" class="field-wrapper">
                <h2>Find a card</h2>
                <div class="form-row align-items-center">
                    <div class="col">
                        <label class="sr-only" for="cutter_card_search">Find a card</label>
                        <input id="input-cutter-card-search" autocomplete="off" class="form-control" type="text" placeholder="Enter card name"
                               name="cutter_card_search">
                    </div>
                    <div class="col-auto">
                        <div id="loading-card-search" class="loading-inline" style="display: none;"></div>
                    </div>
                </div>
                <?php
                MC_Mask_Cutter_Functions::templatePartCardResults();
                MC_Mask_Cutter_Functions::templatePartPrintingResults();
                ?>
            </div>
        </div>
        <div class="col-sm-auto text-center">
            <?php
            MC_Mask_Cutter_Functions::templatePartSelectedMapPrinting( $map_printing_id ); ?>
        </div>
    </div>

    <h3>Elements</h3>
    <div class="mb-3">
        <a href="/cutter-management?role=edit&target=element">
            <button>Create Element</button>
        </a>
    </div>
    <div class="row align-items-start">
        <div class="col-md-8">
            <div class="row align-items-center justify-content-start">
                <?php
                foreach( $elements as $element ) :
                    $element_id = $element->id;
                    $element_name = $element->name;
                    $element_selected = in_array( $element_id, $map_elements ) ? 'checked' : '';
                    $description = $element->description;
                    $variations = !empty( $element_id ) ? MC_Mask_Cutter_Functions::getVariationsByElementId( $element_id ) : [];
                    $element_selected_variation = MC_Mask_Cutter_Functions::getVariationByMapAndElement( $map_id, $element_id );
                    $element_selected_variation_id = $element_selected_variation->id ?? 0;
                    ?>
                    <div class="col-6">
                        <div id="target-<?= $element_id ?>" data-element="<?= $element_selected_variation_id ?>"
                             class="card element-variation element-variation-highlight w-100 mb-3">
                            <div class="card-body">
                                <h5><?= $element_name ?>
                                    - <a href="/cutter-management?role=edit&target=element&id=<?= $element_id ?>" target="_blank">Edit</a></h5>
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input map-element" id="input-map-element-<?= $element_id ?>"
                                           name="map_element_<?= $element_id ?>" value="1" <?= $element_selected ?>>
                                    <label class="form-check-label" for="input-map-element-<?= $element_id ?>">Selected</label>
                                </div>
                                <div class="mb-3 mb-3">
                                    <label class="fw-bold" for="input-element-variation">Element Variation</label>
                                    <select class="form-control" id="input-element-variation" name="map_element_variation_<?= $element_id ?>">
                                        <option value="0">-- Select a Variation --</option>
                                        <?php
                                        foreach( $variations as $variation ) :
                                            $variation_id = $variation->id;
                                            $variation_name = $variation->name;
                                            $variation_selected = $variation_id == $element_selected_variation_id ? 'selected' : '';
                                            ?>
                                            <option value="<?= $variation_id ?>" <?= $variation_selected ?>><?= $variation_name ?></option>
                                        <?php
                                        endforeach; ?>
                                    </select>
                                </div>
                                
                                <?php
                                if( !empty( $variation_file ) ) : ?>
                                    <div class="bg-dark rounded p-2 my-3">
                                        <img src="<?= $variation_file ?>" style="width:100%">
                                    </div>
                                <?php
                                endif; ?>
                                
                                <?php
                                if( !empty( $variation_mask_maps ) ) : ?>
                                    <h5>Mask Maps</h5>
                                    <ul>
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
                            </div>
                        </div>
                    </div>
                <?php
                endforeach; ?>
            </div>
        </div>
        <div id="map-preview-container" class="col-md-4 p-0 position-relative">
            <div id="map-affix">
                <div id="map-preview" class="bg-primary position-relative p-3">
                    <img class="" src="<?= $map_printing_image ?>" id="element-preview-0" style="left:0;top:0; width:100%;">
                    <?php
                    $count                             = 0;
                    foreach( $elements as $element ) :
                        $element_id = $element->id;
                        $element_name                  = $element->name;
                        $element_selected              = in_array( $element_id, $map_elements ) ? 'checked' : '';
                        $description                   = $element->description;
                        $variations                    = !empty( $element_id ) ? MC_Mask_Cutter_Functions::getVariationsByElementId( $element_id ) : [];
                        $element_selected_variation    = MC_Mask_Cutter_Functions::getVariationByMapAndElement( $map_id, $element_id );
                        $element_selected_variation_id = $element_selected_variation->id ?? 0;
                        if( empty( $element_selected_variation->file ) ) continue;
                        $element_selected_variation_file = $element_selected_variation->file;
                        $position                        = $count > 0 ? '' : 'position-relative';
                        ?>
                        <img class="p-3 position-absolute" src="<?= $element_selected_variation_file ?>"
                             id="element-preview-<?= $element_selected_variation_id ?>" style="left:0;top:0; width:100%;">
                        <?php
                        $count++;
                    endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="target" value="map">
    <input type="hidden" name="role" value="edit">
    <input type="hidden" id="input-map-id" class="form-control" name="id" value="<?= $map_id ?>">
    <input type="hidden" class="form-control" name="save" value="1">
    <?php
    MC_Mask_Cutter_Functions::templatePartButton( $component, $map_id ); ?>
</form>