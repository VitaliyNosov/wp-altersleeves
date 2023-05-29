<?php

return;

use Mythic_Core\Functions\MC_Artist_Functions;

$creators = get_option( 'mc_alterists_index', [] );
shuffle( $creators );
?>
<div class="py-3">
    <h2>Check out these alterists</h2>
    <?php
    $output = '<div class="alterists row">';
    $count  = 0;
    foreach( $creators as $idCreator ) {
        if( $count == 4 ) break;
        $alterist = MC_Artist_Functions::displayAlteristPreview( $idCreator, true );
        if( empty( $alterist ) ) continue;
        $output .= $alterist;
        $count++;
    }
    $output .= '</div>';
    echo $output;
    ?>
</div>
