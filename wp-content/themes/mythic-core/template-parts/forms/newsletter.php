<?php

use Mythic_Core\Settings\MC_Site_Settings;
use Mythic_Core\Utils\MC_Sendinblue;

$title       = $title ?? MC_Site_Settings::value( 'newsletter_title', 'Sign up to the newsletter' );
$subtitle    = isset( $subtitle ) ? $subtitle : MC_Site_Settings::value( 'newsletter_subtitle', '' );
$text        = $text ?? MC_Site_Settings::value( 'newsletter_text', '' );
$email_text  = $email_text ?? MC_Site_Settings::value( 'newsletter_email_text', 'Email Address' );
$button_text = $button_text ?? MC_Site_Settings::value( 'newsletter_button_text', 'Sign Up' );
$disclaimer  = $disclaimer ?? MC_Site_Settings::value( 'newsletter_disclaimer_text', NEWSLETTER_DISCLAIMER );
$name_field  = $name_field ?? MC_Site_Settings::value( 'newsletter_name_field', 1 );
$sib_list_id = $sib_list_id ?? MC_Sendinblue::newsletterListId();
?>

<form id="newsletter-signup" class="newsletter">
    <?php if( !empty( $title ) ) : ?>
        <h3 class="title"><?= $title ?></h3>
    <?php endif; ?>

    <?php if( !empty( $subtitle ) ) : ?>
        <h4 class="subtitle"><?= $subtitle ?></h4>
    <?php endif; ?>

    <?php if( !empty( $text ) ) : ?>
        <p class="text"><?= $text ?></p>
    <?php endif; ?>

    <?php if( !empty( $name_field ) ) : ?>
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="input-name" placeholder="John Smith" autocomplete="off" required>
            <label for="input-name">Full Name</label>
        </div>
    <?php endif; ?>

    <div class="form-floating mb-3">
        <input type="email" class="form-control" id="input-email" placeholder="name@example.com" autocomplete="off" required>
        <label for="input-email">Email address</label>
    </div>

    <div class="form-check permission">
        <input type="checkbox" class="form-check-input" id="input-marketing-permission">
        <label class="form-check-label" for="input-marketing-permission"><small>If you would like to also stay informed about upcoming Mythic Gaming,
                Mythic Frames and Alter Sleeves discounts and announcements, please click here. ( We will never sell or give your information to third
                party companies)</small></label>
    </div>

    <div class="mb-3 confirm text-center">
        <a class="button" type="submit" role="button" href="javascript:void(0);" data-sib-list="<?= $sib_list_id ?>"><?= $button_text ?></a>
    </div>
    <?php Mythic_Core\Ajax\Data\MC_Capture_Newsletter_Signup::render_nonce(); ?>

    <p class="disclaimer"><?= $disclaimer ?></p>
</form>