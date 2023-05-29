<?php

if( empty( $input_name ) || empty( $setting_name ) || empty( $options ) ) {
    return;
}
$settings = $settings ?? [];
?>
    <select class="regular-text" id="<?= $input_name ?>" name="<?= $setting_name ?>[<?= $input_name ?>]">
        <?php foreach( $options as $option )  :
            if( !isset( $option['value'] ) ) {
                $option['value'] = sanitize_key( $option['label'] );
            }
            $selected = isset( $settings[ $input_name ] ) && $settings[ $input_name ] == $option['value'] ? ' selected' : '';
            ?>
            <option value="<?= $option['value'] ?>"<?= $selected ?>><?= $option['label'] ?></option>
        <?php endforeach ?>
    </select>
