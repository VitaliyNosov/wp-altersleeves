<?php

use Mythic_Core\Functions\MC_Mask_Cutter_Functions;

$component           = 'element';
$element_id          = isset( $_GET['save'] ) ? MC_Mask_Cutter_Functions::saveElement( $_GET ) : $_GET['id'] ?? 0;
$element             = !empty( $element_id ) ? MC_Mask_Cutter_Functions::getElementById( $element_id ) : '';
$element_name        = !empty( $element->name ) ? $element->name : '';
$element_description = !empty( $element->description ) ? $element->description : '';

$variations = !empty( $element_id ) ? MC_Mask_Cutter_Functions::getVariationsByElementId( $element_id ) : [];
$locked     = !empty( $element->locked ) ? 'checked' : '';

if( !empty( $element ) ) : ?>
    <h3 class="mb-4">You are Editing: <?= $element_name ?> (<?= $element_id ?>)</h3>
<?php
else : ?>
    <h3 class="mb-4">Create new Element</h3>
<?php
endif; ?>

<form action="/cutter-management" method="GET">
    <div class="mb-3 row align-items-start">
        <label for="input-element-name" class="col-sm-2 col-form-label font-weight-bold">Name</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="input-element-name" placeholder="Element Name" value="<?= $element_name ?>"
                   name="element_name">
        </div>
    </div>

    <div class="mb-3 row align-items-start">
        <label for="input-element-description" class="col-sm-2 col-form-label font-weight-bold">Description</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="input-element-description" rows="3" placeholder="Enter description here"
                      name="description"><?= $element_description ?></textarea>
        </div>
    </div>
    <div class="mb-3 row align-items-start">
        <label class="col-sm-2 font-weight-bold form-check-label" for="input-element-locked">
            Locked Element
        </label>
        <div class="col-sm-10 position-relative">
            <input class="form-check-input ml-0" type="checkbox" value="1" id="input-element-locked" name="locked" <?= $locked ?>>
        </div>
    </div>

    <h2>Element Variations</h2>
    <div>
        <a href="javascript:void(0);" id="action-create-element-variation" data-target="<?= $element_id ?>"
           class="btn btn-primary blue--button my-2">Create Variation</a>
    </div>
    <div id="element-variations" class="row align-items-start justify-content-start">
        <?php
        foreach( $variations as $variation ) : ?>
            <div class="col-6 col-sm-4">
                <?php
                MC_Mask_Cutter_Functions::templatePartVariation( $element_id, $variation->id ); ?>
            </div>
        <?php
        endforeach; ?>
    </div>
    <input type="hidden" name="target" value="element">
    <input type="hidden" name="role" value="edit">
    <input type="hidden" id="input-element-id" class="form-control" name="id" value="<?= $element_id ?>">
    <input type="hidden" class="form-control" name="save" value="1">
    <?php
    MC_Mask_Cutter_Functions::templatePartButton( $component, $element_id ); ?>
</form>