<?php

wp_schedule_single_event( time(), 'mc_calculate_production_payout' );


$teams = get_option( 'mc_teams_kickstarter_totals', [] );

?>
<h2>Kickstarter Allocations</h2>
<table>
    <thead>
    <th scope="col">Design Name</th>
    <th scope="col">Set</th>
    <th scope="col">Creature</th>
    <th scope="col">Non-Creature</th>
    <th scope="col">Land</th>
    <th scope="col">Total Creature</th>
    <th scope="col">Total Non-Creature</th>
    <th scope="col">Total Land</th>
    <th scope="col">Stretch</th>
    </thead>
    <tbody>
    <?php

    $count  = 1;
    $totals = [
        'set'                => 0,
        'creature'           => 0,
        'non-creature'       => 0,
        'land'               => 0,
        'total_creature'     => 0,
        'total_non_creature' => 0,
        'total_land'         => 0,
        'stretch'            => 0,
    ];
    foreach( $teams as $team ) :

        ?>
        <tr>
            <td><?= \Mythic_Core\Functions\MC_Mythic_Frames_Functions::getTeamName( $count ) ?></td>
            <td><?= $team['set'] ?></td>
            <td><?= $team['creature'] ?></td>
            <td><?= $team['non-creature'] ?></td>
            <td><?= $team['land'] ?></td>
            <td><?= $team['total_creature'] ?></td>
            <td><?= $team['total_non_creature'] ?></td>
            <td><?= $team['total_land'] ?></td>
            <td><?= $team['stretch'] ?></td>
        </tr>
        <?php

        $totals['set']                += $team['set'];
        $totals['creature']           += $team['creature'];
        $totals['non-creature']       += $team['non-creature'];
        $totals['land']               += $team['land'];
        $totals['total_creature']     += $team['total_creature'];
        $totals['total_non_creature'] += $team['total_non_creature'];
        $totals['total_land']         += $team['total_land'];
        $totals['stretch']            += $team['stretch'];

        $count++;
    endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <th scope="col">#</th>
        <th scope="col"><?= $totals['set'] ?></th>
        <th scope="col"><?= $totals['creature'] ?></th>
        <th scope="col"><?= $totals['non-creature'] ?></th>
        <th scope="col"><?= $totals['land'] ?></th>
        <th scope="col"><?= $totals['total_creature'] ?></th>
        <th scope="col"><?= $totals['total_non_creature'] ?></th>
        <th scope="col"><?= $totals['total_land'] ?></th>
        <th scope="col"><?= $totals['stretch'] ?></th>

    </tr>
    </tfoot>
</table>

<?php

$teams = get_option( 'mc_teams_preorder_totals', [] );

?>
<h2>Preorder Allocations (Up to July 31st)</h2>
<table>
    <thead>
    <th scope="col">Design Name</th>
    <th scope="col">Set</th>
    <th scope="col">Creature</th>
    <th scope="col">Non-Creature</th>
    <th scope="col">Land</th>
    <th scope="col">Total Creature</th>
    <th scope="col">Total Non-Creature</th>
    <th scope="col">Total Land</th>
    <th scope="col">Stretch</th>
    </thead>
    <tbody>
    <?php

    $count  = 1;
    $totals = [
        'set'                => 0,
        'creature'           => 0,
        'non-creature'       => 0,
        'land'               => 0,
        'total_creature'     => 0,
        'total_non_creature' => 0,
        'total_land'         => 0,
        'stretch'            => 0,
    ];
    foreach( $teams as $team ) :

        ?>
        <tr>
            <td><?= \Mythic_Core\Functions\MC_Mythic_Frames_Functions::getTeamName( $count ) ?></td>
            <td><?= $team['set'] ?></td>
            <td><?= $team['creature'] ?></td>
            <td><?= $team['non-creature'] ?></td>
            <td><?= $team['land'] ?></td>
            <td><?= $team['total_creature'] ?></td>
            <td><?= $team['total_non_creature'] ?></td>
            <td><?= $team['total_land'] ?></td>
            <td><?= $team['stretch'] ?></td>
        </tr>
        <?php

        $totals['set']                += $team['set'];
        $totals['creature']           += $team['creature'];
        $totals['non-creature']       += $team['non-creature'];
        $totals['land']               += $team['land'];
        $totals['total_creature']     += $team['total_creature'];
        $totals['total_non_creature'] += $team['total_non_creature'];
        $totals['total_land']         += $team['total_land'];
        $totals['stretch']            += $team['stretch'];

        $count++;
    endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <th scope="col">#</th>
        <th scope="col"><?= $totals['set'] ?></th>
        <th scope="col"><?= $totals['creature'] ?></th>
        <th scope="col"><?= $totals['non-creature'] ?></th>
        <th scope="col"><?= $totals['land'] ?></th>
        <th scope="col"><?= $totals['total_creature'] ?></th>
        <th scope="col"><?= $totals['total_non_creature'] ?></th>
        <th scope="col"><?= $totals['total_land'] ?></th>
        <th scope="col"><?= $totals['stretch'] ?></th>
    </tr>
    </tfoot>
</table>
<br>