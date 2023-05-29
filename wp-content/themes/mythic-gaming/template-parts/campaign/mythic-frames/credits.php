<?php

$user_id   = !empty( $user ) ? $user->ID : 0;
$backer_id = MC_Mythic_Frames_Functions::getBackerId();
$backer_id = MC_User_Functions::isAdmin() && !empty( $_GET['backer_id'] ) ? $_GET['backer_id'] : $backer_id;
if( empty( $backer_id ) ) return;
$backer_name = !empty( $user ) ? $user->display_name : '';

$access_granted = !empty( $backer_id ) && !empty( $backer_name );
$user_credits   = MC_Mythic_Frames_Functions::backerCredits( $backer_id );
if( empty( $user_credits ) ) return;
extract( $user_credits );

$count         = 0;
$credit_output = '';
$count++;
ob_start();
?>
<tr>
    <th scope="row">
        Initial
    </th>
    <?php if( !empty( $init_set_credits ) ) : ?>
        <td><span id="init-set-credits-<?= $count ?>"><?= $init_set_credits ?></span></td>
    <?php endif; ?>
    <?php if( !empty( $init_pack_credits ) ) : ?>
        <td><span id="init-pack-credits-<?= $count ?>"><?= $init_pack_credits ?></span>
        </td>
    <?php endif; ?>
    <?php if( !empty( $init_super_land_credits ) ) : ?>
        <td><span id="init-super-land-credits-<?= $count ?>"><?= $init_super_land_credits ?></span></td>
    <?php endif; ?>
</tr>

<tr>
    <th scope="row">
        Available
    </th>
    <?php if( !empty( $init_set_credits ) ) : ?>
        <td><span id="init-set-credits-<?= $count ?>"><?= $set_credits ?></span></td>
    <?php endif; ?>
    <?php if( !empty( $init_pack_credits ) ) : ?>
        <td><span id="init-pack-creature-credits-<?= $count ?>"><?= $pack_credits ?></span>
        </td>
    <?php endif; ?>
    <?php if( !empty( $init_super_land_credits ) ) : ?>
        <td><span id="init-super-land-credits-<?= $count ?>"><?= $super_land_credits ?></span></td>
    <?php endif; ?>
</tr>

<tr>
    <th scope="row">
        Spent
    </th>
    <?php if( !empty( $init_set_credits ) ) : ?>
        <td><span id="init-set-credits-<?= $count ?>"><?= $spent_set_credits ?></span></td>
    <?php endif; ?>
    <?php if( !empty( $init_pack_credits ) ) : ?>
        <td><span id="init-pack-credits-<?= $count ?>"><?= $spent_pack_credits ?></span>
        </td>
    <?php endif; ?>
    <?php if( !empty( $init_super_land_credits ) ) : ?>
        <td><span id="init-super-land-credits-<?= $count ?>"><?= $spent_super_land_credits ?></span></td>
    <?php endif; ?>
</tr>

<?php
$credit_output .= ob_get_clean();

$count        = 0;
$spend_output = '';
foreach( MC_Mythic_Frames_Functions::sorted_teams() as $team ) :
    $count++;
    extract( $user_credits['teams'][ $count ] );
    ob_start();
    ?>
    <tr>
        <th scope="row">
            <a class="d-none d-sm-block" href="/campaign?team_id=<?= $count ?><?= !empty($backer_id) ? '&backer_id='.$backer_id : ''?>"
               title="Click to go to <?= $team['design_name'] ?>"><?= $team['design_name']; ?></a>
            <a class="d-sm-none" href="/campaign?team_id=<?= $count ?><?= !empty($backer_id) ? '&backer_id='.$backer_id : ''?>" title="Click to go to <?= $team['design_name'] ?>"
               target="_blank"><?= $count; ?></a>
        </th>
        <?php if( !empty( $init_set_credits ) ) : ?>
            <td><span id="spent-set-credits-<?= $count ?>"><?= $spent_set_credits ?></span></td>
        <?php endif; ?>
        <?php if( !empty( $init_pack_credits ) ) : ?>
            <td><span id="spent-non-creature-credits-<?= $count ?>"><?= $spent_non_creature_credits ?></span>
            </td>
            <td><span id="spent-creature-credits-<?= $count ?>"><?= $spent_creature_credits ?></span></td>
            <td><span id="spent-land-credits-<?= $count ?>"><?= $spent_land_credits ?></span></td>
        <?php endif; ?>
        <?php if( !empty( $init_super_land_credits ) ) : ?>
            <td><span id="spent-super-land-credits-<?= $count ?>"><?= $spent_super_land_credits ?></span></td>
        <?php endif; ?>
    </tr>

    <?php
    $spend_output .= ob_get_clean();
endforeach; ?>

<h3>Credit Totals</h3>
<p>These are your remaining credit balances.</p>
<table class="table mb-3">
    <thead>
    <tr>
        <th scope="col">#</th>
        <?php if( !empty( $init_set_credits ) ) : ?>
            <th scope="col">Set</th>
        <?php endif; ?>
        <?php if( !empty( $init_pack_credits ) ) : ?>
            <th scope="col">Pack</th>
        <?php endif; ?>
        <?php if( !empty( $init_super_land_credits ) ) : ?>
            <th scope="col">Super Land</th>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?= $credit_output ?>
    </tbody>
</table>

<h3>Allocated Credits</h3>
<p>This is how you've chosen to spend your credits</p>
<table class="table">
    <thead>
    <tr>
        <th scope="col">#</th>
        <?php if( !empty( $init_set_credits ) ) : ?>
            <th scope="col">Set</th>
        <?php endif; ?>
        <?php if( !empty( $init_pack_credits ) ) : ?>
            <th scope="col">Non-Creature</th>
            <th scope="col">Creature</th>
            <th scope="col">Land</th>
        <?php endif; ?>
        <?php if( !empty( $init_super_land_credits ) ) : ?>
            <th scope="col">Super Land</th>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?= $spend_output ?>
    </tbody>
</table>