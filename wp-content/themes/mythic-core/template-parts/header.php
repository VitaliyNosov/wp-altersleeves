<?php

use Mythic_Core\Display\MC_Render;

$parts = apply_filters( 'mc_header_sections', [] );

if( empty( $parts ) ) return;

ob_start();

foreach( $parts as $part ) do_action( 'mc_header_element', $part );

$header = ob_get_clean();
if( empty( $header ) ) return;

?>
<header id="header" class="<?= apply_filters( 'mc_header_class', 'header' ) ?>">
    <div class="container position-relative">
        <?php MC_Render::row( $header, 'align-items-center controls' ); ?>
    </div>
</header>
