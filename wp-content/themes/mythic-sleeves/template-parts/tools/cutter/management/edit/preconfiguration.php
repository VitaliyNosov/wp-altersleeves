<?php

$component                    = 'preconfiguration';
$preconfiguration_id          = isset( $_GET['save'] ) ? MC_Mask_Cutter_Functions::savePreconfiguration( $_GET ) : $_GET['id'] ?? 0;
$preconfiguration             = !empty( $preconfiguration_id ) ? MC_Mask_Cutter_Functions::getPreconfigurationById( $preconfiguration_id ) : '';
$preconfiguration_name        = !empty( $preconfiguration->name ) ? $preconfiguration->name : '';
$preconfiguration_description = $preconfiguration->description ?? '';

$elements                  = MC_Mask_Cutter_Functions::getElements();
$preconfiguration_elements = !empty( $preconfiguration_id ) ? MC_Mask_Cutter_Functions::getElementIdsByPreconfiguration( $preconfiguration_id ) : [];

if( !empty( $preconfiguration ) ) : ?>
    <h3 class="mb-4">Preconfiguration: <?= $preconfiguration_name ?> (<?= $preconfiguration_id ?>)</h3>
<?php
else : ?>
    <h3 class="mb-4">Create new Preconfiguration</h3>
<?php
endif; ?>

<form action="/cutter-management" method="GET">
    <div class="mb-3 row align-items-start">
        <label for="input-preconfiguration-name" class="col-sm-2 col-form-label fw-bold">Name</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="input-preconfiguration-name" name="preconfiguration_name" placeholder="Element Name"
                   value="<?= $preconfiguration_name ?>">
        </div>
    </div>

    <div class="mb-3 row align-items-start">
        <label for="input-preconfiguration-description" class="col-sm-2 col-form-label fw-bold">Description</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="input-preconfiguration-description" rows="3" placeholder="Enter description here"
                      name="description"><?= $preconfiguration_description ?></textarea>
        </div>
    </div>
    <h3>Preconfiguration Elements</h3>
    <div class="row justify-content-start">
        <?php
        foreach( $elements as $element ) :
            $element_id = $element->id;
            $element_name = $element->name;
            $element_selected = in_array( $element_id, $preconfiguration_elements ) ? 'checked' : '';
            ?>
            <div class="form-check col-6 col-sm-4 mb-3">
                <div class="pl-3">
                    <input type="checkbox" class="form-check-input preconfiguration-element" id="preconfiguration-element-<?= $element_id ?>"
                           name="element_<?= $element_id ?>" <?= $element_selected ?> value="<?= $element_id ?>">
                    <label class="form-check-label" for="preconfiguration-element-<?= $element_id ?>"><?= $element_name ?></label> - <a
                            href="/cutter-management?role=edit&target=element&id=<?= $element_id ?>">Edit</a>
                </div>
            </div>
        <?php
        endforeach; ?>
    </div>
    <input type="hidden" name="target" value="preconfiguration">
    <input type="hidden" name="role" value="edit">
    <input type="hidden" class="form-control" name="id" value="<?= $preconfiguration_id ?>">
    <input type="hidden" class="form-control" name="save" value="1">
    <?php
    MC_Mask_Cutter_Functions::templatePartButton( $component, $preconfiguration_id ); ?>
</form>