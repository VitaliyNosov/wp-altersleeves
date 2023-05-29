<?php

$firstName = !empty( $idUser ) ? ' '.get_userdata( $idUser )->first_name : ''; ?>
<p>Hello<?= $firstName ?>,</p>