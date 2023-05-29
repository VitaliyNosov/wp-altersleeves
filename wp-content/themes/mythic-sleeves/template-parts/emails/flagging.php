<?php include 'greeting.php'; ?><?php $content = !empty( $content ) ? $content : ''; ?>
    <p>Thanks for all your great work contributing to Alter Sleeves, we have flagged some of your content on the Alter Sleeves store. Please see why
        below:</p>
<?php echo wpautop( $content ); ?>
    <p>If you have any further questions please reply to this email or contact us at <?= EMAIL_SUPPORT ?></p>
<?php include 'signature.php';
