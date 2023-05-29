<?php

$options = [
    'design-type' => 'Incorrect Design Type',
    'alter-type'  => 'Incorrect Crop type',
    'tags'        => 'Incorrect/Inappropraite tags',
    'printing'    => 'Incorrect Printing',
];

foreach( $options as $key => $option ) :
    ?>
    <div class="form-check">
        <input class="form-check-input form-label" type="checkbox" value="" id="option-flag-<?= $key ?>-<?= $idAlter ?>">
        <label class="form-check-label" for="option-flag-<?= $key ?>-<?= $idAlter ?>">
            <?= $option ?>
        </label>
    </div>
<?php
endforeach; ?>
