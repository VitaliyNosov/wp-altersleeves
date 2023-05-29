<?php

if( empty( $input_name ) || empty( $setting_name ) ) {
    return;
}
$value = $value ?? '';
$rows  = $rows ?? 10;

?>

    <label for="<?= $input_name ?>" style="display: none;"></label>
    <textarea id="<?= $input_name ?>" name="<?= $setting_name ?>[<?= $input_name ?>]" cols="100" rows="<?= $rows ?>"><?= $value ?></textarea>
