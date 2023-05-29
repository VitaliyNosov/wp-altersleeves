<?php

use Mythic_Core\Utils\MC_Pagination; ?>

<div id="pagination" class="text-center col-auto">
    <?= MC_Pagination::get( [ 'quantity' => 100 ] ) ?>
</div>