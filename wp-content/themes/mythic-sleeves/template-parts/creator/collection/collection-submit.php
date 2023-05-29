<?php

if( !MC_User_Functions::isAdmin() && !MC_User_Functions::isArtist() ) return;

include 'intro.php';
$idCollection = isset( $_GET['collection_id'] ) ? $_GET['collection_id'] : 0;

?>
<hr>
<div id="collection-management">
    <input type="hidden" id="input-collection-id" value="<?= $idCollection ?>">
    <p>Select a position below and search for the design you would like in that position. Click and drag your designs into the preferred order.</p>
    <?php
    include( 'designs.php' );
    include( 'confirm.php' );
    include( 'modal.php' );
    ?>
</div>
