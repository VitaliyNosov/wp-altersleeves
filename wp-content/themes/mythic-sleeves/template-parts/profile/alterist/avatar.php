<?php

use Mythic_Core\Objects\MC_User;

if( empty( $idAlterist ) ) return;

$image = MC_User::avatar( $idAlterist );

?>

<div class="avatar"><img src="<?= $image ?>"></div>