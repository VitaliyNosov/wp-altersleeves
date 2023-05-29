<?php

include 'greeting.php';
$content       = !empty( $content ) ? $content : '';
$imageAttached = !empty( $attachment ) ? ' We have attached an image highlighting the issue.' : '';
?>
    <p>Hello <?= $firstName ?>,</p><p>Thanks for all your great work contributing to Alter Sleeves, however a design appears to have an issue.<?= $imageAttached ?></p>
<?php echo wpautop( $content );
include 'signature-flagging.php';


