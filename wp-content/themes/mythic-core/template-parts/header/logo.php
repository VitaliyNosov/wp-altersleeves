<?php

use Mythic_Core\Utils\MC_Server;

$is_frames = MC_Server::primaryPath() == 'mythic-frames';
$logo_id   = $is_frames ? 'frames-logo' : 'header-logo';
$url       = is_front_page() ? 'javascript:void(0);' : get_site_url();

?>
<div id="<?= $logo_id ?>" class="logo py-2 col">
    <?php
    $path = MC_URI_IMG.'/logos/mythic-gaming/logo/orange-gradient/';
    ?>

    <a href="<?= $url ?>" title="Access the Mythic Gaming home page">
        <img src="<?= $path.'125.png' ?>" srcset="<?= $path.'125.png' ?> 1x,<?= $path.'250.png' ?> 2x" alt="The Mythic Gaming logo">
    </a>
</div>