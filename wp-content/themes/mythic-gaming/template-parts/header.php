<?php

use Mythic_Core\Display\MC_Render;

ob_start();
do_action( 'mc_header_logo' );


do_action( 'mc_header_nav' );

$header = ob_get_clean();
if( empty( $header ) ) return;

?>

<header id="header" class="<?= apply_filters( 'mc_header_class', 'header' ) ?>">
    <div class="container position-relative">
        <?php MC_Render::row( $header, 'align-items-center controls' ); ?>
    </div>
</header>
<?php MG_Render::campaign('mythic-frames', 'alert-bar' );