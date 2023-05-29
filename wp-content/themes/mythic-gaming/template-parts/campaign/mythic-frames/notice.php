<?php if( MC_User_Functions::isAdmin() ) : ?>
    <form action="<?= MC_Url::current() ?>">
        <label class="sr-only" for="backer_id">Backer ID</label><br>
        <input type="text" id="backer_id" name="backer_id" placeholder="Backer ID"><br>
        <input type="checkbox" value="1" name="credit_reset"> Reset credits
        <input type="submit" value="Submit">
    </form>
<?php endif; ?>

<?php

$teams     = MC_Mythic_Frames_Functions::sorted_teams();
$user_id   = !empty( $user ) ? $user->ID : 0;
$backer_id = MC_Mythic_Frames_Functions::getBackerId();
$backer_id = MC_User_Functions::isAdmin() && !empty( $_GET['backer_id'] ) ? $_GET['backer_id'] : $backer_id;
if( empty( $backer_id ) ) return;
$backer_name = !empty( $user ) ? $user->display_name : '';

$credits        = MC_Mythic_Frames_Functions::hasRemainingCredits( $backer_id );
$address        = MC_Mythic_Frames_Functions::hasAddress( $backer_id );
$fee_product_id = MC_Mythic_Frames_Functions::feeProductId( $backer_id );
$needs_to_pay   = !empty( $fee_product_id ) && !MC_Woo_Order_Functions::userIdHasBoughtProduct( $fee_product_id );
?>

<hr>

<h2>Kickstarter Information</h2>

<p>Thank you <?= $backer_name ?> for supporting our Kickstarter! You can find information on your credits below. We are hoping to ship by end of
    end of September.</p>

<p>Have questions? Send a message to <span class="credits-allocated">support@mythicgaming.com</span></p>

<?php if( $credits || !$address || $needs_to_pay ) : ?>
    <h3 class="text-danger">Action Required</h3>
    <p class="text-danger">We require you to take action to complete your pledge and unlock the ability to pre-order more Mythic Frames items. Please
        may you do the
        following:</p>
    <ul>
        <?php if( $credits ) : ?>
            <li>We need to ensure all credits are allocated to designs by the end of the pre-order period to see which will be put into production (<a
                        href="https://www.kickstarter.com/projects/altersleeves/mythic-frames" target="_blank"
                        title="Link to the Mythic Frames Kickstarter">as described in the Kickstarter</a>). So please click the button below to go to
                the credit allocation page
            </li>
        <?php endif; ?>

        <?php if( !$address ) : ?>
            <li>To ship your Mythic Frames, we require your Shipping Address. <a href="/dashboard/edit-address/"
                                                                                 title="Click to allocate credits to design">You add can it
                    here</a>, or
                when adding additional items by pre-ordering below.
            </li>
        <?php endif; ?>

        <?php if( $needs_to_pay ) : ?>
            <li>Unfortunately, it appears that Kickstarter was unable to take your payment; however you can now do so here at Mythic Gaming, as well
                as adding
                additional items. Please click the button below to add the fee to cart.
            </li>
        <?php endif; ?>
    </ul>

    <div class="row align-items-center">

        <?php MG_Render::campaign( 'mythic-frames', 'to-pay' ); ?>

        <?php if( $credits ) : ?>
            <div class="col-sm-auto">
                <a class="button" href="/campaign?backer_id=<?= $backer_id ?>" title="Click to allocate credits to design">Allocate credits</a>
            </div>
        <?php endif; ?>

        <?php if( !$address ) : ?>
            <div class="col-sm-auto">
                <a class="button" href="/mythic-frames" title="Click to pre-order Mythic Sleeves">Pre-Order</a>
            </div>
        <?php endif; ?>

    </div>
<?php endif; ?>

<hr>
