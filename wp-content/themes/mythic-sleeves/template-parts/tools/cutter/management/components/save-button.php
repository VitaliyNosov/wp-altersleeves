<?php

if( empty( $component ) ) return;
$id = $id ?? 0;
?>
<div id="field-save" class="field-wrapper">
    <button id="cutter-component-save" data-component="<?= $component ?>" data-target="<?= $id ?>" class="btn btn-primary blue--button">
        <?php if( !empty( $id ) ) : ?>
            Save
        <?php else : ?>
            Create
        <?php endif; ?>
    </button>
    <div class="loading-confirm loading" style="display: none;"></div>
    <span class="notice-saved text-success px-3" style="display: none;">Saved Changes</span>
</div>

