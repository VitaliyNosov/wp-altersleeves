<?php

if( !MC_User_Functions::isAdmin() ) return;

$creature    = $team['creature'] ?? $team['Creature'] ?? '';
$noncreature = $team['non-creature'] ?? $team['Non-Creature'] ?? '';
$land        = $team['land'] ?? $team['Land'] ?? '';
$commander_1 = $team['commander_1'] ?? [];
$commander_2 = $team['commander_2'] ?? [];
$commander_3 = $team['commander_3'] ?? [];

?>
<h4 class="my-3 text-center"><a href="/files/mythic-frames/<?= $team_id ?>.png" target="_blank">Generated Kickstarter Image</a></h4>

<div id="accordion">
    <div class="card">
        <div class="card-header" id="heading<?= $team_id ?>">
            <h5 class="mb-0">
                <button class="btn btn-link  collapsed" data-bs-toggle="collapse" data-bs-target="#collapse<?= $team_id ?>" aria-expanded="true"
                        aria-controls="collapse<?= $team_id ?>">
                    Team Configuration
                </button>
            </h5>
        </div>

        <div id="collapse<?= $team_id ?>" class="collapse" aria-labelledby="heading<?= $team_id ?>" data-parent="#accordion">
            <div class="card-body">
                <p>To upload images and get URL, <a href="https://www.altersleeves.com/wp-admin/upload.php" target="_blank">do it here</a></p>
                <div class="row my-3">

                    <form>
                        <div class="form-row align-items-start justify-content-start my-3">
                            <div class="col-sm-4">
                                <div class="w-100 my-2">
                                    <label class="form-label" for="input-team-image-<?= $team_id ?>">Alterist Image (Url)</label>
                                    <input type="text" class="form-control mb-2 mr-sm-2" id="input-team-image-<?= $team_id ?>"
                                           placeholder="Alterist image url here"
                                           value="<?= $team['alterist_image'] ?? MC_URI_IMG.'/user/profile.png'; ?>">
                                </div>
                                <div class="w-100 my-2">
                                    <label class="form-label" for="input-cc-image-<?= $team_id ?>">Content Creator Image (Url)</label>
                                    <input type="text" class="form-control mb-2 mr-sm-2" id="input-cc-image-<?= $team_id ?>"
                                           placeholder="Content Creator image url here"
                                           value="<?= $team['content_creator_image'] ?? MC_URI_IMG.'/user/profile.png'; ?>">
                                </div>
                                <div class="w-100 my-2">
                                    <label class="form-label" for="input-design-name-<?= $team_id ?>">Design Name</label>
                                    <input type="text" class="form-control mb-2 mr-sm-2" id="input-design-name-<?= $team_id ?>"
                                           placeholder="Content Creator image url here" value="<?= $team['design_name'] ?? '' ?>">
                                </div>
                            </div>

                            <div class="col-sm-8">
                                <label class="form-label" for="input-team-description-<?= $team_id ?>">Team Description</label>
                                <?php
                                wp_editor( $team['description'] ?? '', 'input-team-description-'.$team_id,
                                           [ 'editor_class' => 'form-control mb-2 mr-sm-2' ] );
                                ?>
                            </div>
                        </div>

                        <div class="form-row align-items-end justify-content-start">
                            <div class="col-auto">
                                <label class="form-label" for="input-creature-<?= $team_id ?>">Creature Alter ID</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" id="input-creature-<?= $team_id ?>" placeholder="Alter ID here"
                                       value="<?= $creature ?>">
                            </div>

                            <div class="col-sm-auto">
                                <label class="form-label" for="input-noncreature-<?= $team_id ?>">Non-Creature Alter ID</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" id="input-noncreature-<?= $team_id ?>"
                                       placeholder="Alter ID here" value="<?= $noncreature ?>">
                            </div>

                            <div class="col-sm-auto">
                                <label class="form-label" for="input-land-<?= $team_id ?>">Land Alter ID</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" id="input-land-<?= $team_id ?>" placeholder="Alter ID here"
                                       value="<?= $land ?>">
                            </div>

                            <div class="col-sm-auto">
                                <label class="form-label" for="input-commander-1-<?= $team_id ?>">Commander 1 IDs</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" id="input-commander-1-<?= $team_id ?>"
                                       placeholder="Alter IDs here (comma separated)" value="<?= implode( ',', $commander_1 ) ?>">
                            </div>

                            <div class="col-sm-auto">
                                <label class="form-label" for="input-commander-2-<?= $team_id ?>">Commander 2 IDs</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" id="input-commander-2-<?= $team_id ?>"
                                       placeholder="Alter IDs here (comma separated)" value="<?= implode( ',', $commander_2 ) ?>">
                            </div>

                            <div class="col-sm-auto">
                                <label class="form-label" for="input-commander-3-<?= $team_id ?>">Commander 3 IDs</label>
                                <input type="text" class="form-control mb-2 mr-sm-2" id="input-commander-3-<?= $team_id ?>"
                                       placeholder="Alter IDs here (comma separated)" value="<?= implode( ',', $commander_3 ) ?>">
                            </div>

                            <div class="col">
                                <button class="frame-update btn btn-primary mb-2" data-team="<?= $team_id ?>">Submit</button>
                                <?php Mythic_Core\Ajax\Marketing\MC_Mythic_Frames_Config::render_nonce(); ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>