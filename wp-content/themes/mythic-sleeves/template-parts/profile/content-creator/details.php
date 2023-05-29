<?php

use Mythic_Core\Functions\MC_Artist_Functions;

if( empty( $idUser ) ) return;

include 'avatar.php';
echo MC_Artist_Functions::getAlteristSocialIcons();