<?php

use Mythic_Core\System\MC_Social;
use Mythic_Core\Utils\MC_Vars;

if( empty( $socials ) ) return;

foreach( $socials as $key => $social ) {
    $key = MC_Vars::parseableString( $key );
    if( !in_array( $key, MC_Social::available() ) ) continue;
    $url = $social;
    if( empty( $url ) ) continue;
    ?>
    <div class="social-icon px-2">
        <a href="<?= $url ?>" title="Click to go to <?= ucfirst( $key ) ?>" rel="nofollow noopener" target="_blank">
            <i class="fab fa-<?= $key ?>"></i>
        </a>
    </div>
    <?php
}
