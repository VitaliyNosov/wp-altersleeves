<?php

$preconfigurations = MC_Mask_Cutter_Functions::getPreconfigurations();
if( empty( $preconfigurations ) ) return;
?>

    <div class="mb-3">
        <a href="/cutter-management?role=edit&target=preconfiguration">
            <button>Create Preconfiguration</button>
        </a>
    </div>

<?php
foreach( $preconfigurations as $preconfiguration ) :
    $preconfiguration_id = $preconfiguration->id;
    $name          = $preconfiguration->name;
    $description   = $preconfiguration->description;
    $elements      = MC_Mask_Cutter_Functions::getElementIdsByPreconfiguration( $preconfiguration_id );
    ?>
    <div id="target-<?= $preconfiguration_id ?>" class="card w-100 mb-3">
        <div class="card-body">
            <div class="row align-items-start">
                <div class="col-sm">
                    <div class="text">
                        <h5 class="card-title"><?= $name ?></h5>
                        <p><?= $description ?></p>
                    </div>
                    <?php
                    if( !empty( $elements ) ) : ?>
                        <div class="elements"><p>
                                <?php
                                $i = 1;
                                foreach( $elements as $element_id ) :
                                    $element = MC_Mask_Cutter_Functions::getElementById( $element_id );
                                    if( empty( $element ) ) continue;
                                    $name = $element->name;
                                    if( $i !== 1 ) $name = ', '.$name;
                                    $i++;
                                    ?>
                                    <span><?= $name ?></span>
                                <?php
                                endforeach; ?></p>
                        </div>
                    <?php
                    endif; ?>
                </div>
                <div class="col-sm-auto">
                    <a href="/cutter-management?role=edit&target=preconfiguration&id=<?= $preconfiguration_id ?>"
                       class="btn btn-primary blue--button w-100 mx-2">Edit</a>
                    <br><br>
                    <a href="javascript:void(0);" data-target="<?= $preconfiguration_id ?>"
                       class="action-init-delete delete-hide btn btn-primary red--button w-100 mx-2">Delete</a>
                    <a href="javascript:void(0);" data-target="<?= $preconfiguration_id ?>" data-type="preconfiguration"
                       class="action-confirm-delete delete-check btn btn-primary green--button w-100 mx-2" style="display: none;">Confirm</a>
                </div>
            </div>
        </div>
    </div>
<?php
endforeach; ?>