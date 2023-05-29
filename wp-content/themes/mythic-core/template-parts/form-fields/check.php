<?php

if( empty( $id ) || empty( $value ) ) return;

$field_class = $field_class ?? '';
$field_class = !empty( $hide_label ) || empty( $label ) ? ' sr-only' : '';
$label       = empty( $label ) ? $id : $label;
$checked     = !empty( $checked ) ? ' checked' : '';
$disabled    = !empty( $disabled ) ? ' disabled' : '';
$required    = !empty( $required ) ? ' required' : '';

?>

<div class="form-check <?= $field_class ?>">
    <input id="input-<?= $id ?>" class="form-check-input <?= $input_class ?? '' ?>" type="<?= $type ?? 'checkbox' ?>>" name="<?= $id ?>"
           value="<?= $value ?>" <?= $checked.$disabled.$required ?>>
    <label class="form-check-label <?= $label_class ?? '' ?>" for="input-<?= $id ?>">
        <?= $label ?? '' ?>
    </label>
</div>
