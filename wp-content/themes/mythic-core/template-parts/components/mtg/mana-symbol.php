<?php

if( !isset( $symbol ) ) return;
$size = $size ?? 'small';
?>
<span class="mana-symbol mana <?= $size.' '.$symbol ?>"></span>
