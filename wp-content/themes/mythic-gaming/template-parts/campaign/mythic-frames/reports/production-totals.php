<?php

wp_schedule_single_event( time(), 'mc_calculate_production_totals' );


$teams = get_option( 'mc_teams_production_totals', [] );

?>
<h2>Totals</h2>
<table>
    <thead>
    <th scope="col">Design Name</th>
    <th scope="col">Set</th>
    <th scope="col">Creature</th>
    <th scope="col">Non-Creature</th>
    <th scope="col">Land</th>
    <th scope="col">Playmat 1</th>
    <th scope="col">Playmat 2</th>
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
            <td><?= $team['playmat_1'] ?></td>
            <td><?= $team['playmat_2'] ?></td>
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
        <th scope="col"><?= $totals['playmat_1'] ?></th>
        <th scope="col"><?= $totals['playmat_2'] ?></th>
        <th scope="col"><?= $totals['total_creature'] ?></th>
        <th scope="col"><?= $totals['total_non_creature'] ?></th>
        <th scope="col"><?= $totals['total_land'] ?></th>
        <th scope="col"><?= $totals['stretch'] ?></th>

    </tr>
    </tfoot>
</table>
<hr>
<h2>United States</h2>

<table>
    <thead>
    <th scope="col">Design Name</th>
    <th scope="col">Set</th>
    <th scope="col">Creature</th>
    <th scope="col">Non-Creature</th>
    <th scope="col">Land</th>
    <th scope="col">Playmat 1</th>
    <th scope="col">Playmat 2</th>
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
            <td><?= $team['US']['set'] ?></td>
            <td><?= $team['US']['creature'] ?></td>
            <td><?= $team['US']['non-creature'] ?></td>
            <td><?= $team['US']['land'] ?></td>
            <td><?= $team['US']['playmat_1'] ?></td>
            <td><?= $team['US']['playmat_2'] ?></td>
            <td><?= $team['US']['total_creature'] ?></td>
            <td><?= $team['US']['total_non_creature'] ?></td>
            <td><?= $team['US']['total_land'] ?></td>
            <td><?= $team['US']['stretch'] ?></td>
        </tr>
        <?php
        
        $totals['set']                += $team['US']['set'];
        $totals['creature']           += $team['US']['creature'];
        $totals['non-creature']       += $team['US']['non-creature'];
        $totals['land']               += $team['US']['land'];
        $totals['total_creature']     += $team['US']['total_creature'];
        $totals['total_non_creature'] += $team['US']['total_non_creature'];
        $totals['total_land']         += $team['US']['total_land'];
        $totals['stretch']            += $team['US']['stretch'];
        
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
        <th scope="col"><?= $totals['playmat_1'] ?></th>
        <th scope="col"><?= $totals['playmat_2'] ?></th>
        <th scope="col"><?= $totals['total_creature'] ?></th>
        <th scope="col"><?= $totals['total_non_creature'] ?></th>
        <th scope="col"><?= $totals['total_land'] ?></th>
        <th scope="col"><?= $totals['stretch'] ?></th>

    </tr>
    </tfoot>
</table>
<hr>

<h2>Netherlands</h2>

<table>
    <thead>
    <th scope="col">Design Name</th>
    <th scope="col">Set</th>
    <th scope="col">Creature</th>
    <th scope="col">Non-Creature</th>
    <th scope="col">Land</th>
    <th scope="col">Playmat 1</th>
    <th scope="col">Playmat 2</th>
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
            <td><?= $team['NL']['set'] ?></td>
            <td><?= $team['NL']['creature'] ?></td>
            <td><?= $team['NL']['non-creature'] ?></td>
            <td><?= $team['NL']['land'] ?></td>
            <td><?= $team['NL']['playmat_1'] ?></td>
            <td><?= $team['NL']['playmat_2'] ?></td>
            <td><?= $team['NL']['total_creature'] ?></td>
            <td><?= $team['NL']['total_non_creature'] ?></td>
            <td><?= $team['NL']['total_land'] ?></td>
            <td><?= $team['NL']['stretch'] ?></td>
        </tr>
        <?php
        
        $totals['set']                += $team['NL']['set'];
        $totals['creature']           += $team['NL']['creature'];
        $totals['non-creature']       += $team['NL']['non-creature'];
        $totals['land']               += $team['NL']['land'];
        $totals['total_creature']     += $team['NL']['total_creature'];
        $totals['total_non_creature'] += $team['NL']['total_non_creature'];
        $totals['total_land']         += $team['NL']['total_land'];
        $totals['stretch']            += $team['NL']['stretch'];
        
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
        <th scope="col"><?= $totals['playmat_1'] ?></th>
        <th scope="col"><?= $totals['playmat_2'] ?></th>
        <th scope="col"><?= $totals['total_creature'] ?></th>
        <th scope="col"><?= $totals['total_non_creature'] ?></th>
        <th scope="col"><?= $totals['total_land'] ?></th>
        <th scope="col"><?= $totals['stretch'] ?></th>

    </tr>
    </tfoot>
</table>
<?php

$users = MC_Mythic_Frames_Functions::getUsersWithCredits();

$counts = [
    'users' => 0,
    'sets'  => 0,
    'packs' => 0,
    'lands' => 0,
];
foreach( $users as $user_id ) {
    $user         = get_user_by( 'ID', $user_id );
    $backer_id    = MC_Mythic_Frames_Functions::getBackerId( $user_id );
    $user_credits = MC_Mythic_Frames_Functions::backerCredits( $backer_id );
    extract( $user_credits );

    if( $set_credits == 0 && $pack_credits == 0 && $super_land_credits == 5 ) continue;

    $counts['users'] = $counts['users'] + 1;
    $counts['sets']  += $set_credits;
    $counts['packs'] += $pack_credits;
    $counts['lands'] += $super_land_credits;
}
?>
<h2><a href="https://www.mythicgaming.com/remaining-backers/">Remaining Credits:</a></h2>
<p>Remaining Users: <?= $counts['users'] ?><br>
    Remaining Sets: <?= $counts['sets'] ?><br>
    Remaining Packs: <?= $counts['packs'] ?><br>
    Remaining Super Lands: <?= $counts['lands'] ?></p>