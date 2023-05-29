<?php

if( empty( $input_name ) || empty( $setting_name ) ) {
    return;
}
$value       = $value ?? '';
$rows        = $rows ?? 10;
$placeholder = $placeholder ?? '';
$type        = $type ?? '';

?>

    <label for="<?= $input_name ?>" style="display: none;"></label>
<?php if( $type == 'file' ) : ?>
    <input type="file" id="<?= $input_name ?>" name="<?= $setting_name ?>[<?= $input_name ?>]" /><?= $value ?>
<?php else : ?>
    <input class="regular-text" type="<?= $type ?>" id="<?= $input_name ?>" name="<?= $setting_name ?>[<?= $input_name ?>]" value="<?= $value ?>"
           placeholder="<?= $placeholder ?>" />
<?php endif; ?>
