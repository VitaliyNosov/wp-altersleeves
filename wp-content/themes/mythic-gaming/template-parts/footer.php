<?php

use Mythic_Core\Display\MC_Render;

$social_args = [
    'socials' => [
        'facebook'  => 'https://www.facebook.com/MythicGamingCo/',
        'instagram' => 'https://www.instagram.com/mythicgaming_co/',
        'twitter'   => 'https://twitter.com/MythicGaming_Co',
        'twitch'    => 'https://www.twitch.tv/MythicGamingTV',
    ],
];

?>
<footer>

    <?php
    ob_start();
    do_action( 'mc_disclaimer' );
    $disclaimer = ob_get_clean();
    if( !empty( $disclaimer ) ) : ?><?php echo $disclaimer ?><?php endif; ?>

    <div class="copyright-social py-2 align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-sm order-2 order-sm-1 text-center text-sm-start">
                    <?php do_action( 'mc_copyright' ); ?>
                </div>
                <div class="col-sm-auto order-1 order-sm-2">
                    <div class="social-icons d-flex justify-content-center">
                        <?php MC_Render::component( 'social-icons', '', $social_args ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer>
