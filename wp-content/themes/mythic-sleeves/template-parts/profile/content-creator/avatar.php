<?php

use Mythic_Core\Objects\MC_User;

if( empty( $idUser ) ) return;

$image = MC_User::avatar( $idUser );

?>

<div class="avatar"><img src="<?= $image ?>"></div>