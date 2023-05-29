<?php

$elements = MC_Mask_Cutter_Functions::getElements();
if( empty( $elements ) ) return;
?>

<div class="mb-3">
    <a href="/cutter-management?role=edit&target=element">
        <button>Create Element</button>
    </a>
</div>
<div class="row">
    <?php
    foreach( $elements as $element ) :
        $element_id = $element->id;
        $name = $element->name;
        $description = $element->description;
        ?>
        <div class="col-sm-6">
            <div id="target-<?= $element_id ?>" class="card w-100 mb-3">
                <div class="card-body">
                    <div class="row align-items-start">
                        <div class="col-sm">
                            <h5 class="card-title"><?= $name ?></h5>
                            <p><?= $description ?></p>
                        </div>
                        <div class="col-sm-auto">
                            <a href="/cutter-management?role=edit&target=element&id=<?= $element_id ?>"
                               class="btn btn-primary blue--button w-100 mx-2">Edit</a>
                            <br><br>
                            <a href="javascript:void(0);" data-target="<?= $element_id ?>"
                               class="action-init-delete delete-hide btn btn-primary red--button w-100 mx-2">Delete</a>
                            <a href="javascript:void(0);" data-target="<?= $element_id ?>" data-type="element"
                               class="action-confirm-delete delete-check btn btn-primary green--button w-100 mx-2" style="display: none;">Confirm</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endforeach; ?>
</div>
