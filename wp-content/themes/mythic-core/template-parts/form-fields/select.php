<?php

use Mythic_Core\Utils\MC_Vars;

if( empty( $id ) || empty( $options ) || !is_array( $options ) ) return;

$field_class = $field_class ?? '';
$field_class = !empty( $hide_label ) || empty( $label ) ? ' sr-only' : '';
$label       = empty( $label ) ? $id : $label;
$multi       = !empty( $multi ) ? ' multiple ' : '';
$disabled    = !empty( $disabled ) ? ' disabled' : '';
$required    = !empty( $required ) ? ' required' : '';

?>

<div class="mb-3">
        <label for="input-<?= $id ?>"><?= $label ?></label>
        <select id="input-<?= $id ?>" name="<?= $id ?>" class="form-control"
                id="input-<?= $id ?>" <?= $multi.$disabled.$required ?> <?php if( !empty( $description ) ) echo 'aria-describedby="desc-'.$id.'">'; ?>>
            <?php
            foreach( $options as $option ) :
                $option_label = is_array( $option ) ? $option['label'] ?? '' : $option;
                if( empty( $option_label ) ) continue;
                $option_value = is_array( $option ) ? $option['value'] : MC_Vars::readableToKey( $option_label );
                ?>
                <option value="<?= $option_value ?>"><?= $option['label'] ?></option>
            <?php endforeach; ?>
        </select>

    <?php if( !empty( $description ) ) : ?>
        <small id="desc-<?= $id ?>" class="form-text text-muted"><?= $description ?></small>
    <?php endif; ?>
</div>
