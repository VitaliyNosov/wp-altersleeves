<?php

use Mythic_Core\Display\MC_Render;

$parts = apply_filters( 'mc_header_sections', [] );

if( empty( $parts ) ) return;

ob_start();

foreach( $parts as $part ) do_action( 'mc_header_element', $part );

$header = ob_get_clean();
if( empty( $header ) ) return;

if( !is_front_page() ) : ?>
    <div class="w-100 bg-danger py-1 text-white text-center">Season Sale - 10% off all orders sent as a gift. Send as gift at checkout!</div>
<?php endif; ?>
<header id="header" class="<?= apply_filters( 'mc_header_class', 'header' ) ?>">
    <div class="container position-relative">
        <?php MC_Render::row( $header, 'align-items-center controls' ); ?>
    </div>
</header>