<?php

use Mythic_Core\Objects\MC_Mtg_Printing;

if( empty( $map_printing_id ) ) $map_printing_id = 0;

$printing = new MC_Mtg_Printing( $map_printing_id );
if( empty( $printing ) ) return;

$printing_framecode = $printing->framecode;

?>

<div id="cutter-selected-printing">
    <?php if( !empty( $map_printing_id ) ) : ?>
        <h3>Selected Printing</h3>
        <div class="w-100">
            <img src="<?= $printing->imgJpgNormal ?>" style="max-width:150px;">
        </div>
        <p class="py-2"><strong>Framecode: <br></strong> <span id="cutter-printing-framecode"><?= $printing_framecode->name ?></span></p>
        <input type="hidden" id="input-map-printing" name="map_printing" value="<?= $map_printing_id ?>">
        <input type="hidden" id="input-map-framecode" name="map_framecode" value="<?= $printing_framecode->term_id; ?>">
    <?php endif; ?>
</div>