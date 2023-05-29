<?php

if( empty( $id ) || empty( $value ) ) return;

$field_class = $field_class ?? '';
$field_class = !empty( $hide_label ) || empty( $label ) ? ' sr-only' : '';
$label       = empty( $label ) ? $id : $label;
$disabled    = !empty( $disabled ) ? ' disabled' : '';
$required    = !empty( $required ) ? ' required' : '';

?>

    <div class="mb-3<?= $field_class ?>">
        <label for="input-<?= $id ?>"><?= $label ?? 'Send a message' ?></label>
        <textarea id="input-<?= $id ?>" class="form-control <?= $input_class ?? '' ?>" placeholder="<?= $placeholder ?? '' ?>"
                  name="<?= $id ?>"
                  rows="<?= $rows ?? 5 ?>" <?= $disabled.$required ?> <?php if( !empty( $description ) ) echo 'aria-describedby="desc-'.$id.'">'; ?>>
            <?= $value ?>
        </textarea>
        <?php if( !empty( $description ) ) : ?>
            <small id="desc-<?= $id ?>" class="form-text text-muted"><?= $description ?></small>
        <?php endif; ?>
    </div>

<?php

unset( $description );
unset( $field_class );
unset( $id );
unset( $input_class );
unset( $label );
unset( $placeholder );
unset( $value );