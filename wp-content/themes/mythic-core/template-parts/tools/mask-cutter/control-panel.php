<?php

use Mythic_Core\Display\MC_Render;

?>

<div class="row align-items-start dashboard">
    <div class="sidebar col-sm-auto">
        <a class="nav-item" href="/cutter-management?target=maps&role=list">Mask Maps</a>
        <a class="nav-item" href="/cutter-management?target=elements&role=list">Elements</a>
        <a class="nav-item" href="/cutter-management?target=preconfigurations&role=list">Preconfigurations</a>
    </div>
    <div class="col-sm">
        <?php

        ob_start();
        MC_Render::templatePart( '/tools/cutter/management/'.$role.'/'.$target );
        $output = ob_get_clean();

        if( empty( $output ) ) : ?>
            <p>Please select a management option to the left</p>
        <?php
        else : ?>
            <?= $output ?><?php
        endif; ?>
    </div>
</div>