<?php

if( empty( $content ) ) return;
$action = !empty( $action ) ? "action='$action' " : '';
$id     = !empty( $id ) ? "id='$id' " : '';
$method = !empty( $method ) ? "method='$method' " : '';

?>
<form <?= $action.$id.$method ?> autocomplete="off" autocapitalize="none" accept-charset="UTF-8">
    <?= $content ?>
</form>
