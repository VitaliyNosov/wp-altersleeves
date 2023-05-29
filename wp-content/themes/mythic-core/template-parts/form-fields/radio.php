<?php

$radio_checked  = !empty( $radio_checked ) ? ' checked' : '';
$radio_disabled = !empty( $radio_disabled ) ? ' disabled' : '';
$radio_required = !empty( $radio_required ) ? ' required' : '';
?>

<div class="form-check <?= $radio_field_class ?? '' ?>">
    <input class="form-check-input <?= $radio_input_class ?? '' ?>" type="radio" name="<?= $radio_id ?>" id="<?= $radio_id ?>_<?= $radio_value ?>"
           value="<?= $radio_value ?>" <?= $radio_checked.$radio_disabled.$radio_required ?>>
    <label class="form-check-label <?= $radio_label_class ?? '' ?>" for="<?= $radio_id ?>_<?= $radio_value ?>">
        <?= $radio_label ?? '' ?>
    </label>
</div>