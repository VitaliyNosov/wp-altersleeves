<?php

if( empty( $idAlter ) ) return;

if( !isset( $admin ) ) $admin = MC_User_Functions::isAdmin();

$options = [
    'copyright'             => 'Copyright/Clone Tool',
    'cropping-tight'        => 'Cropping Tight',
    'cropping-insufficient' => 'Cropping Insufficient',
    'resolution'            => !empty( $admin ) ? 'Incorrect Resolution' : '',
    'opacity'               => !empty( $admin ) ? 'White blobs/Opacity blending issue' : '',
    'white-transparent'     => !empty( $admin ) ? 'Pure White: Transparent' : '',
    'alter-type'            => 'Incorrect Design Type',
    'design-type'           => 'Incorrect Crop type',
];

foreach( $options as $key => $option ) :
    if( empty( $option ) ) continue;
    ?>
    <div class="form-check">
        <input class="form-check-input form-label" type="checkbox" value="" id="option-flag-<?= $key ?>-<?= $idAlter ?>">
        <label class="form-check-label" for="option-flag-<?= $key ?>-<?= $idAlter ?>">
            <?= $option ?>
        </label>
    </div>
<?php
endforeach; ?>
