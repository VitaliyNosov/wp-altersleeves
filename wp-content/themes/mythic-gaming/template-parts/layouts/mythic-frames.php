<?php

use Mythic_Core\Display\MC_Render;

$args = [
    'title'        => 'Get notified with Mythic Gaming news',
    'email_text'   => 'Enter your email',
    'button_text'  => 'Get Notified',
    'disclaimer'   => NEWSLETTER_DISCLAIMER,
    'name_field'   => 1,
    'confirmation' => 'Thank you for signing up',
    'sib_list_id'  => 26,
];

ob_start();
?>

    <h1 class="text-center">Mythic Frames</h1>
    <div id="about">
        <div class="container">
            <div class="row align-items-start mt-3">

                <div class="row align-items-center">
                    <div class="col-md-8 order-2 order-md-1 pb-3">
                        <h2>About Mythic Frames</h2>
                        <?php MC_WP_Post_Functions::defaultLoop( true, false ); ?>

                    </div>
                    <div class="col-md-4 text-center order-1 order-md-2">
                        <img loading="lazy" src="<?= MC_URI_RESOURCES_IMG_PRODUCTS_MF ?>examples.png" style="max-width:400px;"
                             alt="Example image of Mythic Frames"><br>
                        <small>Credits: Aaron Miller and InklinCustoms</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="block mb-0">
        <div class="container py-3">

            <div id="preorder">
                <h2>Pre-Order is now open!</h2>
                <p>Order now to be amongst the first to receive Mythic Frames. We aim to ship by September, which will include your rewards
                    if you backed our <a href="https://www.kickstarter.com/projects/altersleeves/mythic-frames" target="_blank"
                                         rel="nofollow noopener">Kickstarter</a>.
                </p>
                <p>If you pledged to the Kickstarter have not yet allocated your credits, you can do so by <a href="/login">logging in</a> using the
                    email
                    you
                    pledged with. You will find instructions on the dashboard. For all other users, you can create a Mythic Gaming account during the
                    checkout
                    process; if you already have an <a href="https://www.altersleeves.com" target="_blank">Alter Sleeves account</a> you can login
                    using
                    those
                    details.</p>
                <?php MC_Render::templatePart( 'campaign/mythic-frames', 'team-products' ); ?>

            </div>

        </div>
    </div>

    <div id="newsletter" class="py-4">
        <div class="container text-center">
            <div class="newsletter-container px-2 mx-auto" style="max-width:500px">
                <div class="mx-auto" style="max-width:500px">
                    <?php MC_Render::form( 'newsletter', $args ); ?>
                </div>
            </div>
        </div>
    </div>

    <style>

        #newsletter {
            background-image: url('/wp-content/themes/mythic-gaming/src/img/mythic-frames/background.jpg') !important;
            background-position: center;
            background-attachment: fixed;
            background-size: cover;
        }

        #newsletter label {
            color: #fff;
        }

        .newsletter h3 {
            text-align: center;
            padding: 0.5rem;
            margin: 1rem 0;
            color: #fff;
        }

        label {
            color: #212529;
        }


        .name input,
        .email input {
            border-radius: 20rem;
            border: 0;
            color: #000000;
            text-align: center;
            padding: 0;
            margin: 0;
            font-size: 14px;
        }

        .newsletter .permission {
            line-height: 14px;
            font-size: 16px;
        }

        .newsletter .disclaimer {
            line-height: 14px;
            font-size: 12px;
        }

        #newsletter a {
            color: #FCD820;
        }

    </style>
<?php
$output = ob_get_clean();
echo $output;