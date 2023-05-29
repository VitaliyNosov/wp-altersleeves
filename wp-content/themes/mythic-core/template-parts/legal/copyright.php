<?php

$copyright = !empty( $copyright ) ? $copyright : 'Mythic Gaming'.' - All Rights Reserved';
$copyright = '&#169; '.date( "Y" ).' '.$copyright;
?>

<div class="copyright py-2"><?= apply_filters( 'mc_copyright_filter', $copyright ) ?> - <a href="/privacy-policy">Privacy Policy</a> - <a href="https://www.mythicgaming.com/end-user-license-agreement/">EULA</a> - <a href="https://help.mythicgaming.com/portal/en/newticket">Contact Us</a></div>

