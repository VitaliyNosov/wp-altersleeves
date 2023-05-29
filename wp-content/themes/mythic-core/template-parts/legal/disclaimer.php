<?php

$disclaimer = apply_filters( 'mc_disclaimer_filter', $disclaimer ?? '' );
if( empty( $disclaimer ) ) return;
?>

<div id="disclaimer" class="disclaimer">
    <div class="container py-2 px-3">
        <?= $disclaimer ?><br>
    </div>
</div>
