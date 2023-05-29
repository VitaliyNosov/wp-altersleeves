<?php


use Mythic_Gaming\Functions\MC_Mythic_Frames_Functions;

if( MC_User_Functions::isAdmin() ) :
    $json_file = ABSPATH.'/files/campaigns/backers.json';
    $json_file = file_get_contents( $json_file );
    $json = json_decode( $json_file, ARRAY_A );

    $backer_output                    = '';
    $set_credits_used                 = 0;
    $set_credits_remaining            = 0;
    $pack_credits_used                = 0;
    $pack_credits_remaining           = 0;
    $super_land_credits_used          = 0;
    $super_land_credits_remaining     = 0;

    foreach( $json as $backer_id => $backer ) :
        $backer_data = MC_Mythic_Frames_Functions::backerCreditData( $backer['id'] );
        $backer_set_credits_used      = $backer_data['set_credits']['spent'];
        $backer_set_credits_used      = $backer_set_credits_used < 0 ? 0 : $backer_set_credits_used;
        $backer_set_credits_init      = $backer_data['set_credits']['init'];
        $backer_set_credits_available = 0;
        if( is_numeric( $backer_set_credits_used ) && is_numeric( $backer_set_credits_init ) ) {
            $backer_set_credits_available = $backer_set_credits_init - $backer_set_credits_used;
            $backer_set_credits_available = $backer_set_credits_available < 0 ? 0 : $backer_set_credits_available;
        }

        $backer_pack_credits_used      = $backer_data['pack_credits']['spent'];
        $backer_pack_credits_used      = $backer_pack_credits_used < 0 ? 0 : $backer_pack_credits_used;
        $backer_pack_credits_init      = $backer_data['pack_credits']['init'];
        $backer_pack_credits_available = 0;
        if( is_numeric( $backer_pack_credits_used ) && is_numeric( $backer_pack_credits_init ) ) {
            $backer_pack_credits_available = $backer_pack_credits_init - $backer_pack_credits_used;
            $backer_pack_credits_available = $backer_pack_credits_available < 0 ? 0 : $backer_pack_credits_available;
        }

        $backer_land_credits_used      = $backer_data['land_credits']['spent'];
        $backer_land_credits_used      = $backer_land_credits_used < 0 ? 0 : $backer_land_credits_used;
        $backer_land_credits_init      = $backer_data['land_credits']['init'];
        $backer_land_credits_init      = is_numeric( $backer_land_credits_init ) ? $backer_land_credits_init : 0;
        $backer_land_credits_available = 0;
        if( is_numeric( $backer_land_credits_used ) && is_numeric( $backer_land_credits_init ) ) {
            $backer_land_credits_available = ( $backer_land_credits_init * 5 ) - $backer_land_credits_used;
            $backer_land_credits_available = $backer_land_credits_available < 0 ? 0 : $backer_land_credits_available;
        }

        $set_credits_used             = $set_credits_used + $backer_set_credits_used;
        $set_credits_remaining        = $set_credits_remaining + $backer_set_credits_available;
        $pack_credits_used            = $pack_credits_used + $backer_pack_credits_used;
        $pack_credits_remaining       = $pack_credits_remaining + $backer_pack_credits_available;
        $super_land_credits_used      = $super_land_credits_used + $backer_land_credits_used;
        $super_land_credits_remaining = $super_land_credits_remaining + $backer_land_credits_available;
        ob_start();
        ?>
        <tr>
            <th scope="col"><?= $backer['id'] ?></th>
            <th scope="col"><?= $json[ $backer_id ]['name']; ?></th>
            <th scope="col">
                    <span class="<?= $backer_set_credits_available > 0 ? 'text-danger' : 'text-success' ?>"><?= $backer_set_credits_available ?> / <?= $backer_set_credits_init ?>
            </th>
            <th scope="col">
                    <span class="<?= $backer_pack_credits_available > 0 ? 'text-danger' : 'text-success' ?>"><?= $backer_pack_credits_available ?> / <?= $backer_pack_credits_init ?>
            </th>
            <th scope="col">
                    <span class="<?= $backer_land_credits_available > 0 ? 'text-danger' : 'text-success' ?>"><?= $backer_land_credits_available ?> / <?= $backer_land_credits_init ?>
            </th>
        </tr>
        <?php
        $backer_output .= ob_get_clean();
    endforeach; ?>
    <div class="py-3 text-center">
        <h3><a href="javascript:void(0)" class="show-mythic-frames-stats">Show Mythic Frames Stats</a></h3>
    </div>

    <div id="mythic-frames-stats" class="container" style="display:none;">

        <table class="table table-dark">
            <thead>
            <tr>
                <th scope="col">Set Used</th>
                <th scope="col">Set Remaining</th>
                <th scope="col">Pack Used</th>
                <th scope="col">Pack Remaining</th>
                <th scope="col">Land Used</th>
                <th scope="col">Land Remaining</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><span class="text-success"><?= $set_credits_used ?></span></td>
                <td><span class="text-danger"><?= $set_credits_remaining ?></span></td>
                <td><span class="text-success"><?= $pack_credits_used ?></span></td>
                <td><span class="text-danger"><?= $pack_credits_remaining ?></span></td>
                <td><span class="text-success"><?= $super_land_credits_used ?></span></td>
                <td><span class="text-danger"><?= $super_land_credits_remaining ?></span></td>
            </tr>
            </tbody>
        </table>

        <!-- Teams -->
        <table class="table table-dark">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Set</th>
                <th scope="col">Non-Creature</th>
                <th scope="col">Creature</th>
                <th scope="col">Land</th>
                <th scope="col">Super Land</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $count                 = 1;
            $teams = MC_Mythic_Frames_Functions::sorted_teams();

            foreach( $teams as $team ) :
                $team_data = MC_Mythic_Frames_Functions::getTeamCredits( $count );
                $design_name       = $team['design_name'];
                $team_set_credits  = $team_data['set'];
                $team_non_creature = $team_data['non-creature'];
                $team_creature     = $team_data['creature'];
                $team_land         = $team_data['land'];
                $team_super_land   = $team_data['super-land'];
                ?>
                <tr>
                    <td><?= $design_name ?></td>
                    <td><?= $team_set_credits ?></td>
                    <td><?= $team_non_creature ?></td>
                    <td><?= $team_creature ?></td>
                    <td><?= $team_land ?></td>
                    <td><?= $team_super_land ?></td>
                </tr>
                <?php
                $count++;
            endforeach ?>
            </tbody>
        </table>

        <!-- Backers -->
        <table class="table table-dark">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Set</th>
                <th scope="col">Pack</th>
                <th scope="col">Super Land</th>
            </tr>
            </thead>
            <tbody>
            <?= $backer_output ?>
            </tbody>
        </table>
    </div>

<?php
endif;