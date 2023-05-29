
<?php

$alter_id = $_GET['alter_id'] ?? 0;

?>

    <p>You're looking to <?= !empty( $alter_id ) ? 'edit alter '.$alter_id : 'submit an alter' ?>. Wonderful! Get started by pressing the button
        below:</p>

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#alter_submit_modal">
        <?= !empty( $alter_id ) ? 'Edit' : 'Submit' ?> Alter
    </button>
    <hr>
<?php include 'modal.php';
