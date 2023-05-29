<?php

$design_team_id = rand( 1, 12 );
?>
<div class="row">
    <div class="col-sm-6 text-center">
        <a href="/product/mythic-frames-set-credit/?attribute_pa_design-team=<?= $design_team_id ?>" title="Preorder a set of Mythic Frames">

            <h4>Mythic Frames Set</h4>
            <img src="/resources/img/products/mythic-frames/teams/<?= $design_team_id ?>-set.jpg" alt="Image of ">
            <button class="button">Preorder</button>
        </a>
    </div>

    <div class="col-sm-6 text-center">
        <a href="/product/mythic-frames-booster-pack-credit/?attribute_pa_design-team=<?= $design_team_id ?>&attribute_pa_pack-type=creature"
           title="Preorder a pack of Creature Mythic Frames">
            <h4>Booster Pack</h4>
            <img src="/resources/img/products/mythic-frames/teams/<?= $design_team_id ?>-Creature-Logo.jpg" alt="Image of ">
            <button class="button">Preorder</button>
        </a>
    </div>

</div>
