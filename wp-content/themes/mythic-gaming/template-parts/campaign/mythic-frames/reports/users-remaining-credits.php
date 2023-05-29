<?php

use Mythic_Gaming\Functions\MC_Mythic_Frames_Functions;

$users = MC_Mythic_Frames_Functions::getUsersWithCredits();

$counts = [
    'users' => 0,
    'sets'  => 0,
    'packs' => 0,
    'lands' => 0,
];
foreach( $users as $user_id ) {
    $user = get_user_by( 'ID', $user_id );

    if( empty( $user ) ) continue;
    $first_name = $user->first_name;
    $last_name  = $user->last_name;

    if( empty( $first_name ) && strpos( $last_name, ' ' ) !== false ) {
        $parser = new TheIconic\NameParser\Parser();

        $name = $parser->parse( $last_name );
        wp_update_user( [
            'ID'         => $user_id,
            'first_name' => $name->getFirstname() ?? '',
            'last_name'  => $name->getLastname() ?? '',
        ] );
    }

    $email = $user->user_email;

    $backer_id    = MC_Mythic_Frames_Functions::getBackerId( $user_id );
    $user_credits = MC_Mythic_Frames_Functions::backerCredits( $backer_id );
    extract( $user_credits );
    $password = get_user_meta( $user_id, 'mc_mf_temp_pass', true );
    if(empty($password) ) $password = '';

    if( $set_credits == 0 && $pack_credits == 0 && $super_land_credits == 5 ) continue;

    ob_start(); ?>
    <tr>
        <td><?= $backer_id ?></td>
        <td><?= $email ?></td>
        <td><?= $first_name ?></td>
        <td><?= $last_name ?></td>
        <td><span id="init-set-credits-<?= $backer_id ?>"><?= $set_credits = $set_credits ?? 0 ?></span></td>

        <td><span id="init-pack-creature-credits-<?= $backer_id ?>"><?= $pack_credits = $pack_credits ?? 0 ?></span>
        </td>
        <td><span id="init-super-land-credits-<?= $backer_id ?>"><?= $super_land_credits = $super_land_credits ?? 0 ?></span></td>
        <td><?= $password ?></td>
    </tr>
    <?php
    $user_output .= ob_get_clean();

    $counts['users'] = $counts['users'] + 1;
    $counts['sets']  += $set_credits;
    $counts['packs'] += $pack_credits;
    $counts['lands'] += $super_land_credits;

}
if( empty( $user_output ) ) return;
?>

<h2>Remaining Users: <?= $counts['users'] ?><br>
Remaining Sets: <?= $counts['sets'] ?><br>
Remaining Packs: <?= $counts['packs'] ?><br>
Remaining Super Lands: <?= $counts['lands'] ?></h2>
<table class="table table-dark">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Email</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th>Set</th>
        <th>Packs</th>
        <th>Super Land</th>
    </tr>
    </thead>
    <tbody>
    <?= $user_output ?>
    </tbody>
</table>
