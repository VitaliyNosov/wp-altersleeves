<?php

use Mythic_Core\Objects\MC_User;

if( !is_user_logged_in() ) return;
?>

    <p>We have introduced stricter regulations for submitting alters and you have not yet been approved by our
        moderators to submit an alter to the Alter Sleeves store.</p>

    <p>Please join <a href="https://discord.gg/4KHwZhn">our discord</a> and provide examples of work to a moderator, and
        they will grant you permission to submit.</p>

<?php
$discordUsername = MC_User::meta( 'discord_username' );
if( !empty( $discordUsername ) ) : ?>
    <p>We have your current Discord username as <strong><?= $discordUsername ?></strong>. If that is incorrect, please
        update it using the form below.</p>
<?php
else : ?>
    <p>So our moderators can verify you, please let us know your Discord username using the form below.</p>
<?php
endif;
echo do_shortcode( '[gravityform id="35" title="false" description="false" ajax="true"]' );