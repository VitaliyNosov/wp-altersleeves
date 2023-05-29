<?php

use Mythic_Core\System\MC_WP;;

$backer_id   = MC_Mythic_Frames_Functions::backerId();
$user_id     = MC_Mythic_Frames_Functions::backerIdToUserId( $backer_id );
$user        = get_user_by( 'ID', $user_id );
$backer_name = !empty( $user ) ? $user->display_name : '';
$get_team    = $_GET['team_id'] ?? 0;

$paid           = MC_WP::meta( 'mc_mf_paid', $user_id, 'user' );
$access_granted = !empty( $backer_id ) && !empty( $backer_name );
$user_data      = MC_Mythic_Frames_Functions::backerCreditData( $backer_id );
$user_credits   = MC_Mythic_Frames_Functions::backerCredits( $backer_id ?? 0 );
$preorder_text  = empty( $backer_id ) ? 'Preorder' : 'Addon';
extract( $user_credits );

?>

<div class="py-4 campaign">
    <!-- Header -->
    <div class="container mb-3 campaign-header">
        <div class="row">
            <div class="col info">
                <h1>MYTHIC FRAMES</h1>
                <p>Stunning frame art printed on removable inner sleeves giving a uniform enhancement for your entire deck.</p>
                <p>
                    <a href="https://www.kickstarter.com/projects/altersleeves/mythic-frames" target="_blank">Check out the successful
                        Kickstarter!</a>
                </p>
                <p>
                    <small><strong>Pre-order is now open!</strong> Check out a design team to preorder their Mythic Frames.</small>
                </p>

                <p>To view your currently allocated credits, click below:</p>
                <a href="/dashboard?backer_id=<?= $backer_id ?>">View credit spend</a>
            </div>
            <div class="col-sm-4">
                <div class="credits p-3">
                    <?php if( $access_granted ) : ?>
                        <h3>YOUR CREDITS</h3>
                        <p>Hello <?= $backer_name ?> these are your credits</p>
                        <ul>
                            <?php if( !empty( $init_set_credits ) ) : ?>
                                <li>Set Credits:
                                    <span class="available"><span class="user-set-credits"><?= $init_set_credits ?></span> Credits</span>
                                </li>
                            <?php endif; ?>
                            <?php if( !empty( $init_pack_credits ) ) : ?>
                                <li>Pack Credits:
                                    <span class="available"><span class="user-pack-credits"><?= $init_pack_credits ?></span> Credits</span>
                                </li>
                            <?php endif; ?>
                            <?php if( !empty( $init_super_land_credits ) ) : ?>
                                <li>Super Land Credits*:
                                    <span class="available"><span
                                                class="user-super-land-credits"><?= $init_super_land_credits ?></span> Credits</span>
                                </li>
                            <?php endif; ?>
                        </ul>

                        <p>You do not need to check out to lock in your credits!</p>

                        <?php if( MC_Mythic_Frames_Functions::hasRemainingCredits( $backer_id ) ) : ?>
                            <button class="reset-credits button my-3">Reset session credits</button>
                        <?php endif; ?>
                    <?php endif; ?>
                    <p>Having difficulty retrieving your credits? Send a message to
                        <span class="credits-allocated">support @ mythicgaming.com</span></p>
                    <?php if( !empty( $super_land_credits ) ) : ?>
                        <small>* A super land set pack on the Kickstarter translates to five set credits in the credit portal</small>
                    <?php endif; ?>
                    <hr>
                    <?php if( !is_user_logged_in() ) : ?>
                        <div class="py-3">
                            <p>Login to retrieve your credits</p>
                            <a class="button" href="/login" title="Login to retreive your credits">Login</a>
                        </div>
                    <?php endif ?>
                    <?php if( MC_User_Functions::isAdmin() ) : ?>
                        <form action="<?= MC_Url::current() ?>">
                            <label class="sr-only" for="backer_id">Backer ID</label><br>
                            <input type="text" id="backer_id" name="backer_id" placeholder="Backer ID"><br>
                            <input type="checkbox" value="1" name="credit_reset"> Reset credits
                            <input type="submit" value="Submit">
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php

    if( !empty( $get_team ) && $get_team < 13 ) {
        $team                   = \Mythic_Core\Functions\MC_Mythic_Frames_Functions::getTeam( $get_team );
        $team_data              = MC_Mythic_Frames_Functions::getTeamCredits( $get_team );
        $team_set_credits       = $team_data['set'];
        $team_progress_positive = $team_set_credits / 2;
        $team_progress_negative = 100 - ( 100 - $team_progress_positive );
        $stretch_progress       = 25 + ( ( $team_set_credits - 200 ) / 4 );
        $stretch_progress       = number_format( $stretch_progress, 2 );
        $design_name            = $team['design_name'];
        $artist_name            = $team['alterist_name'];
        $artist_img             = '/files/mythic-frames/profiles/'.$get_team.'-a.png';
        $content_creator_name   = $team['content_creator_name'];
        $content_creator_img    = '/files/mythic-frames/profiles/'.$get_team.'-cc.png';
        $pledged                = $user_data['spent'][ $get_team ]['timestamp'] ?? 0;
        $pledge_show            = !empty( $pledged ) ? '' : 'style="display:none;"';
        $allocated_set          = $user_data['spent'][ $get_team ]['set'] ?? 0;
        $allocated_non_creature = $user_data['spent'][ $get_team ]['non-creature'] ?? 0;
        $allocated_creature     = $user_data['spent'][ $get_team ]['creature'] ?? 0;
        $allocated_land         = $user_data['spent'][ $get_team ]['land'] ?? 0;
        $allocated_super_land   = $user_data['spent'][ $get_team ]['super-land'] ?? 0;
        $stretch_1_image        = isset( $team['commander_2_data'][0]['fileCombinedJpgLo'] ) ? $team['commander_2_data'][0]['fileCombinedJpgLo'] : '/resources/mythic-frames/kickstarter/blank-card.jpg';
        $stretch_2_image        = isset( $team['commander_3_data'][0]['fileCombinedJpgLo'] ) ? $team['commander_3_data'][0]['fileCombinedJpgLo'] : '/resources/mythic-frames/kickstarter/blank-card.jpg';
        ?>
        <div class="container">
            <h3><a href="/campaign<?= !empty( $backer_id ) ? '?backer_id='.$backer_id : '' ?>" title="Click to go back to all designs">View all designs</a></h3>
        </div>
        <div id="campaign-group-<?= $get_team ?>" class="campaign-group mb-5">

            <div class="container">
                <div class="main pb-3">
                    <div class="credits-allocated credits-allocated-<?= $get_team ?> top text-center py-2" <?= $pledge_show ?>>You've pledged to this
                        team!
                    </div>
                    <!-- Title -->
                    <div class="title row align-items-top pt-3 pb-2 g-0">
                        <div class="col px-3">
                            <h3><?= $get_team.'. '.$artist_name.' - '.$content_creator_name ?></h3>
                        </div>
                        <div class="col-sm-auto px-3">
                            <h4>#<?= $design_name ?></h4>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="row align-items-center g-0">
                        <?php if( $team_set_credits >= 200 ) : ?>
                            <div class="pledge-bar unlocked text-center col py-3">
                                UNLOCKED
                                <span id="campaign-group-credits-<?= $get_team ?>"
                                      class="sr-only campaign-group-credits-<?= $get_team ?>"><?= $team_set_credits ?></span>
                            </div>
                            <div class="col-auto campaign-lock unlocked px-3">
                                <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                            </div>
                        <?php else : ?>
                            <div id="pledge-bar-<?= $get_team ?>" class="pledge-bar text-center col py-3"
                                 style="background-image: linear-gradient(to right, #FC6A29 0% <?= $team_progress_positive ?>%, #f7f7f7 <?= $team_progress_negative ?>% 100%);">
                                <span id="campaign-group-credits-<?= $get_team ?>"
                                      class="campaign-group-credits-<?= $get_team ?>"><?= $team_set_credits ?></span>/200
                                <span class="d-none d-sm-inline-block">Set Credits Pledged</span>
                            </div>
                            <div class="col-auto campaign-lock px-3">
                                <i class="fas fa-lock"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Content -->
                    <div class="row align-items-start mb-3">
                        <!-- Campaign Group Image -->
                        <div class="offset-xl-1 col-md-4 col-md-auto main-image py-3 text-center">

                            <?php if( empty( $total_credits ) ) : ?>
                                <div class="p-3">
                                    <a href="/product/mythic-frames-set-credit/?attribute_pa_design-team=<?= $get_team ?>">
                                        <h2>Full Set</h2>
                                        <img loading=lazy src="/resources/img/products/mythic-frames/teams/<?= $get_team ?>-set.jpg"><br>
                                        <button class="button my-3"><?= $preorder_text ?></button>
                                    </a>
                                </div>
                            <?php else : ?>
                                <div class="p-3">
                                    <img loading=lazy
                                         src="https://www.altersleeves.com/resources/mythic-frames/kickstarter/<?= $get_team ?>-bottom-min.png">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-sm">
                            <div class="info p-3">
                                <p><?= $team['description'] ?></p>
                                <div class="row">
                                    <div class="col-6 align-items-center contributor text-center text-sm-start">

                                        <div class="image mx-auto mx-sm-0"
                                             style="background-image: url('https://www.mythicgaming.com<?= $artist_img ?>);"></div>
                                        <span class="name"><?= $artist_name ?></span>
                                        <h5>Artist</h5>
                                    </div>
                                    <div class="col-6 align-items-center contributor text-center text-sm-start">

                                        <div class="image mx-auto mx-sm-0"
                                             style="background-image: url('https://www.mythicgaming.com<?= $content_creator_img ?>);"></div>
                                        <span class="name"><?= $content_creator_name ?></span>
                                        <h5>Content Creator</h5>
                                    </div>
                                </div>
                            </div>

                            <?php if( !empty( $backer_id ) ) : ?>

                                <?php if( !empty( $init_set_credits ) ) : ?>
                                    <div class="p-3 px-sm-0 selection">
                                        <div class="row align-items-center">
                                            <!-- Quantity -->
                                            <div class="col-auto">
                                                <div id="field-set-quantity-<?= $get_team ?>"
                                                     class="row quantity g-0 align-items-center <?php if( !empty( $pledged ) ) : ?>allocated<?php endif ?>">
                                                    <div class="col-auto">
                                                        <label for="input-set-quantity-<?= $get_team ?>" class="sr-only">Quantity</label>
                                                        <input id="input-set-quantity-<?= $get_team ?>" name="input-set-quantity-<?= $get_team ?>"
                                                               max="<?= $set_credits + $allocated_set ?>" class="number" type="number"
                                                               value="<?= $allocated_set ?>" min="<?= $allocated_set ?>" disabled>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="action plus" data-campaign-group="<?= $get_team ?>" data-credit-type="set">+
                                                        </div>
                                                        <div class="action minus" data-campaign-group="<?= $get_team ?>" data-credit-type="set">-
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                Set Credits
                                                <span class="countable-credits">(<span
                                                            class="user-remaining-set-credits"><?= $set_credits ?></span> remaining)</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;

                                $allocation_groups = [
                                    'non-creature' => [
                                        'label'       => 'Non-Creature Pack Credit',
                                        'credit_type' => 'pack',
                                        'inline'      => true,
                                        'condition'   => !empty( $init_pack_credits ),
                                        'default'     => $allocated_non_creature,
                                    ],
                                    'creature'     => [
                                        'label'       => 'Creature Pack Credit',
                                        'credit_type' => 'pack',
                                        'inline'      => true,
                                        'condition'   => !empty( $init_pack_credits ),
                                        'default'     => $allocated_creature,
                                    ],
                                    'land'         => [
                                        'label'       => 'Land Pack Credit',
                                        'credit_type' => 'pack',
                                        'inline'      => true,
                                        'condition'   => !empty( $init_pack_credits ),
                                        'default'     => $allocated_land,
                                    ],
                                    'super-land'   => [
                                        'label'       => 'Super Land Pack Credit',
                                        'credit_type' => 'super-land',
                                        'inline'      => true,
                                        'condition'   => !empty( $init_super_land_credits ),
                                        'default'     => $allocated_super_land,
                                    ],
                                ];
                                ob_start();
                                foreach( $allocation_groups as $key => $allocation_group ) :

                                    if( empty( $allocation_group['condition'] ) ) continue;
                                    $label              = $allocation_group['label'];
                                    $credit_type        = $allocation_group['credit_type'];
                                    $allocation_credits = $key == 'super-land' ? $super_land_credits : $pack_credits;
                                    ?>
                                    <div class="col-sm-6 mb-3">
                                        <div class="row align-items-center">
                                            <!-- Quantity -->
                                            <div class="col-auto">
                                                <div id="field-<?= $key ?>-quantity-<?= $get_team ?>"
                                                     class="row quantity g-0 align-items-center <?php if( !empty( $pledged ) ) : ?>allocated<?php endif ?>">
                                                    <div class="col-auto">
                                                        <label for="input-<?= $key ?>-quantity-<?= $get_team ?>" class="sr-only">Quantity</label>
                                                        <input id="input-<?= $key ?>-quantity-<?= $get_team ?>"
                                                               name="input-<?= $key ?>-quantity-<?= $get_team ?>" class="number" type="number"
                                                               value="<?= $allocation_group['default'] ?>"
                                                               max="<?= $allocation_credits + $allocation_group['default'] ?>"
                                                               min="<?= $allocation_group['default'] ?>" disabled>
                                                    </div>
                                                    <?php if( !empty( $allocation_credits ) ) : ?>
                                                        <div class="col-auto">
                                                            <div class="action plus" data-campaign-group="<?= $get_team ?>"
                                                                 data-credit-type="<?= $key ?>">
                                                                +
                                                            </div>
                                                            <div class="action minus" data-campaign-group="<?= $get_team ?>"
                                                                 data-credit-type="<?= $key ?>">
                                                                -
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <?= $label ?>
                                                <span class="countable-credits">(<span
                                                            class="user-remaining-<?= $credit_type ?>-credits"><?= $allocation_credits ?></span> remaining)</span>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach;
                                $output = ob_get_clean();

                                if( !empty( $output ) ) : ?>
                                    <div class="px-3 px-sm-0 selection">
                                        <div class="row">
                                            <?= $output; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if( !empty( MC_Mythic_Frames_Functions::hasRemainingCredits( $backer_id ) ) ) : ?>
                                    <div class="my-2 text-center text-sm-start ">
                                        <button id="use-credits-<?= $get_team ?>" class="use-credits button" data-campaign-group="<?= $get_team ?>">
                                            USE
                                            CREDITS
                                        </button>
                                        <p class="my-2 credits-allocated credits-allocated-<?= $get_team ?>" style="display:none;">Credits
                                            Allocated</p>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                        </div>

                        <div class="col-xl-1 d-none d-xl-block"></div>
                    </div>

                    <!-- Stretch -->
                    <div id="stretch-bar-<?= $get_team ?>" class="row align-items-center g-0 stretch-bar"
                         style="background-image: linear-gradient(to right, #FBD63B 0% <?= $stretch_progress ?>%, #f7f7f7 <?= $stretch_progress ?>% 100%);">
                        <div class="text-center col-3 py-2 stretch-start">
                            STRETCH REWARDS
                        </div>
                        <div class="text-center col-3 py-2">
                            300 <span class="d-none d-md-inline-block">Set Credits</span>
                        </div>
                        <div class="text-center col-3 py-2 border-left">
                            400 <span class="d-none d-md-inline-block">Set Credits</span>
                        </div>
                        <div class="text-center col-3 py-2 border-left">
                            500 <span class="d-none d-md-inline-block">Set Credits</span>
                        </div>
                    </div>

                    <div class="row align-items-start g-0 stretch-rewards">
                        <div class="text-center col-sm-3 py-3">
                            <span class="pledged campaign-group-credits-<?= $get_team ?>"><?= $team_set_credits ?></span> Credits Pledged
                        </div>
                        <div class="text-center col-sm-3 py-3">
                            <h5 class="mt-0">Commander</h5>
                            <?php if( $stretch_1_image != '/resources/mythic-frames/kickstarter/blank-card.jpg' && !empty( $stretch_1_image ) ) : ?>
                                <img loading=lazy src="<?= $stretch_1_image ?>" class="card-display"
                                     alt="The Stretch Goal Commander design"
                                     style="max-height:250px;">
                            <?php else : ?>
                                <img loading=lazy src="/resources/img/kickstarter/blank-card.jpg"
                                     alt="Commander stretch goal image - coming soon">
                            <?php endif ?>
                        </div>
                        <div class="text-center col-sm-3 py-3">
                            <h5 class="mt-0">Deckbox</h5>
                            <img loading=lazy src="/resources/img/kickstarter/blank-deckbox.jpg" alt="Deckbox stretch goal image - coming soon">
                        </div>
                        <div class="text-center col-sm-3 py-3">
                            <h5 class="mt-0">Stretch Design</h5>
                            <?php if( $stretch_2_image != '/resources/mythic-frames/kickstarter/blank-card.jpg' && !empty( $stretch_2_image ) ) : ?>
                                <a href="/product/mythic-frames-booster-pack-credit/?attribute_pa_design-team=<?= $get_team ?>&attribute_pa_pack-type=stretch-goal">
                                                                <img loading=lazy src="<?= $stretch_2_image ?>" class="card-display"
                                                                     alt="Preorder the stretch goal design"
                                                                     style="max-height:250px;"><br>
                                    <button class="button">Preorder</button>

                            </a>

                            <?php else : ?>
                                <img loading=lazy src="/resources/img/kickstarter/blank-card.jpg"
                                     alt="Commander stretch goal image - coming soon">
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if( empty( $total_credits ) ) : ?>
        <div class="container">
            <?php MC_Render::templatePart( 'campaign/mythic-frames', 'team-products', [ 'team_id' => $get_team ] ); ?>
            </div>
        <?php endif; ?>

        <!-- Modal -->


        <div class="modal fade" id="creditModal" tabindex="-1" role="dialog" aria-labelledby="creditModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <?php if( empty( $backer_id ) ) : ?>
                        <div class="modal-header">
                            <h5 class="modal-title" id="creditModalLabel">Retrieve your credits</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <div class="modal-body">
                        <p><small>By clicking
                                <strong>'Confirm Credits'</strong> you are confirming you want to use these credits for these designs. We are not
                                able
                                to
                                edit or remove these credits after they are allocated. In the event your selected design does not achieve the
                                required
                                200
                                credits by the end of the preorder period, the credits for that specific design will be returned to you and you'll
                                be
                                able
                                to reallocate to a greenlit design of your choice</small>
                        </p>

                        <button id="confirm-credits" type="button" class="button">Confirm Credits</button>
                        <a href="javascript:void(0);" id="allocating-credits" class="button" style="display: none;">ALLOCATING</a>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <?php
    } else { ?>
        <div class="container">
            <h2 class="brand-color">Select a team</h2>
            <p>Click on a team below to see about their, allocate credits and preorder their packs.</p>
            <div class="row">

                <?php
                $count                = 1;
                foreach( MC_Mythic_Frames_Functions::sorted_teams() as $team ) :
                    $team_name = $team['design_name'];
                    $team_data        = MC_Mythic_Frames_Functions::getTeamCredits( $count );
                    $team_set_credits = $team_data['set'];
                    ?>
                    <div class="col-sm-6 col-md-4 mb-3">
                        <a href="/campaign?team_id=<?= $count ?><?= !empty( $backer_id ) ? '&backer_id='.$backer_id : '' ?>">
                            <h3 class="brand-color"><?= $team_name ?></h3>
                            <h4>Credits pledged: <?= $team_set_credits ?></h4>
                            <img src="https://www.altersleeves.com/resources/mythic-frames/kickstarter/<?= $count ?>-bottom-min.png"
                                 alt="Image for <?= $team_name ?>"
                                 loading="lazy">
                        </a>
                    </div>
                    <?php
                    $count++;
                endforeach;
                ?>
            </div>
        </div>
        <?php
    } ?>

</div>
