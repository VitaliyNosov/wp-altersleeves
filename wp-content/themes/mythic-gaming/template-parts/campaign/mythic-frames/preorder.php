<?php

MC_Render::templatePart( 'campaign/mythic-frames', 'team-products' );
return;

$has_credits   = \Mythic_Core\Functions\MC_Mythic_Frames_Functions::hasRemainingCredits();
$selected_team = $_GET['attribute_pa_design-team'] ?? rand( 1, 12 );
$title         = $title ?? false;
?>
<?php if( !empty( $title ) ) : ?>
    <h2 class="text-center">Pre-order Mythic Frames</h2>
<?php endif; ?>
<?php if( !empty( $has_credits ) ) : ?>
    <div class="text-center container">
        <h2 class="text-danger">Pre-order unavailable</h2>
        <p class="fw-bold"><a href="/campaign" title="Allocate pre-existing credits">Click here to allocate pre-existing credits before you can
                pre-order</a></p>
    </div>
<?php else :
    for( $x = 1; $x <= 12; $x++ ) : ?>
        <div id="preorder-products-team-<?= $x ?>" class="row preorder-products-team-row" <?= MC_Inline::displayNone( $selected_team != $x ) ?>>
            <div class="col-sm-6 col-md-3 text-center">
                <a href="/product/mythic-frames-set-credit/?attribute_pa_design-team=<?= $x ?>" title="Preorder a set of Mythic Frames">

                    <h4>Mythic Frames Set</h4>
                    <img src="/resources/img/products/mythic-frames/teams/<?= $x ?>-set.jpg" alt="Image of ">
                    <button class="button">Preorder</button>
                </a>
            </div>

            <div class="col-sm-6 col-md-3 text-center">
                <a href="/product/mythic-frames-booster-pack-credit/?attribute_pa_design-team=<?= $x ?>&attribute_pa_pack-type=creature"
                   title="Preorder a pack of Creature Mythic Frames">
                    <h4>Creature Pack</h4>
                    <img src="/resources/img/products/mythic-frames/teams/<?= $x ?>-Creature-Logo.jpg" alt="Image of ">
                    <button class="button">Preorder</button>
                </a>
            </div>

            <div class="col-sm-6 col-md-3 text-center">
                <a href="/product/mythic-frames-booster-pack-credit/?attribute_pa_design-team=<?= $x ?>&attribute_pa_pack-type=non-creature"
                   title="Preorder a pack of Non-Creature Mythic Frames">
                    <h4>Non-Creature Pack</h4>
                    <img src="/resources/img/products/mythic-frames/teams/<?= $x ?>-NonCreature-Logo.jpg" alt="Image of ">
                    <button class="button">Preorder</button>
                </a>
            </div>

            <div class="col-sm-6 col-md-3 text-center">
                <a href="/product/mythic-frames-booster-pack-credit/?attribute_pa_design-team=<?= $x ?>&attribute_pa_pack-type=land"
                   title="Preorder a pack of Land Mythic Frames">
                    <h4>Land Pack</h4>
                    <img src="/resources/img/products/mythic-frames/teams/<?= $x ?>-Land-Logo.jpg" alt="Image of ">
                    <button class="button">Preorder</button>
                </a>
            </div>
        </div>
    <?php
    endfor;
endif; ?>