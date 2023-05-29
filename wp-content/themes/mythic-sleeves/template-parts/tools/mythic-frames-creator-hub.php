<?php

$count = 1;

?>

<div class="tab-pane fade show p-3 artist-pane" id="nav-<?= $team_id ?>" role="tabpanel" aria-labelledby="nav-<?= $team_id ?>-tab">
    
    <?php
    
    include DIR_THEME_TEMPLATE_PARTS.'/mythic-frames/fields.php';
    include DIR_THEME_TEMPLATE_PARTS.'/mythic-frames/team-details.php';
    include DIR_THEME_TEMPLATE_PARTS.'/mythic-frames/creature.php';
    include DIR_THEME_TEMPLATE_PARTS.'/mythic-frames/non-creature.php';
    include DIR_THEME_TEMPLATE_PARTS.'/mythic-frames/land.php';
    include DIR_THEME_TEMPLATE_PARTS.'/mythic-frames/commander.php';
    
    ?>

</div>
<style>
    .frame-wrapper {
        max-width: 200px;
        margin: 1rem auto;
    }

    .display-frame:nth-child(odd) {
        background: #fbfbfb;
    }

    .mythic-tabs > a {
        width: 50%;
        clear: both;
    }
</style>