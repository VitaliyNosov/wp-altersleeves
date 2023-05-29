<?php

$firstName = get_userdata( $creator )->first_name;
$content   = !empty( $content ) ? $content : '';
?>
    <p>Hello <?= $firstName ?>,</p><p>Thanks for all your great work contributing to Alter Sleeves, however a design appears to have an issue, so please check your <a href="https://www.altersleeves.com/dashboard/alterist/manage-alters">dashboard</a> to resolve this.
<?php
echo wpautop( $content );
include 'signature-flagging.php';

