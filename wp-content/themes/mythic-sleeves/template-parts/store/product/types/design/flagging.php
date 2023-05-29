<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;
use Mythic_Core\System\MC_WP;


if( empty( $idAlter ) || !is_user_logged_in() ) return;
$idUser = wp_get_current_user()->ID;
if( !MC_User_Functions::isMod() ) return;
$admin = MC_User_Functions::isAdmin();

$url           = get_the_permalink( $idAlter );
$image         = MC_Alter_Functions::image( $idAlter );
$fileIndicator = 'success';
$printing_id   = MC_Alter_Functions::printing( $idAlter );
$printing      = new MC_Mtg_Printing( $printing_id );
$cardImage     = $printing->imgJpgNormal;
$designImage   = MC_WP::meta( 'mc_lo_res_alter_png', $idAlter );
$photoImage    = MC_WP::meta( 'mc_approval_image', $idAlter );
?>
<div id="acceptance-<?= $idAlter ?>" class="acceptance m-0">
    <div>
        <?php if( !empty( $admin ) ) : ?>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#acceptance-files" aria-expanded="false"
                    aria-controls="acceptance-files">Check Files
            </button>
        <?php endif; ?>
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#acceptance-flagging" aria-expanded="false"
                aria-controls="acceptance-flagging">Flag Alter
        </button>
        <?php if( !empty( $admin ) ) : ?>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target=".multi-collapse" aria-expanded="false"
                    aria-controls="acceptance-files acceptance-flagging">Files and Flag
            </button>
        <?php endif; ?>
    </div>
    <div id="acceptance-flagging" class="row collapse multi-collapse py-3">
        <div class="col-8">
            <div class="row">
                <div id="acceptance-production" class="col-sm-6">
                    <h2>Flag Production issues</h2>
                    <?php
                    include( DIR_THEME_TEMPLATE_PARTS.'/acceptance/alter/options-flag-production.php' ); ?>
                </div>
                <?php if( $admin || MC_User_Functions::isMod() ) : ?>
                    <div id="acceptance-tags" class="col-sm-6">
                        <h2>Report incorrect alter tagging</h2>
                        <?php
                        include( DIR_THEME_TEMPLATE_PARTS.'/acceptance/alter/options-flag-tagging.php' ); ?>
                    </div>
                <?php endif; ?>
                <?php if( $admin || MC_User_Functions::isMod() ) : ?>
                    <div id="acceptance-message" class="col-sm-12 py-2">
                        <?php
                        include( DIR_THEME_TEMPLATE_PARTS.'/acceptance/message.php' ); ?>
                    </div>
                <?php endif; ?>
                <div id="acceptance-buttons" class="col-sm-12 py-2  text-end">
                    <button id="reject-<?= $idAlter ?>" data-alter-id="<?= $idAlter ?>" class="action-flag cas-button--red mx-3">
                        Flag
                    </button>
                    <?php if( $admin ) : ?>
                        <button id="reject-<?= $idAlter ?>" data-alter-id="<?= $idAlter ?>" class="action-pull cas-button--red">
                            Pull
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!--
        <div class="col-4">
            <div class="camera">
                <div class="camera-feed" style="width:100%;"></div>
                <div class="camera-result">
                    <div class="loading" style="display: none;"></div>
                </div>
            </div>
        </div>
        -->
        <button id="reject-<?= $idAlter ?>" data-alter-id="<?= $idAlter ?>" class="action-flag cas-button--red" style="display:none;">
            Reject
        </button>
        
        <?php Mythic_Core\Ajax\Acceptance\MC_Flag_Alter::render_nonce(); ?>

    </div>
    <div id="acceptance-files" class="collapse multi-collapse py-3">
        <h2>Review files for alter</h2>
        <div class="row">
            <div class="col-3 p-3"><h4>Card Image</h4>
                <img src="<?= $cardImage ?>"><br>
            </div>
            <div class="col-3 p-3" style="background:#e5e5e5;">
                <h4>Grey check</h4>
                <img src="<?= $designImage ?>"><br>
            </div>
            <div class="col-3 p-3" style="background:#226d14;"><h4>Green check</h4>
                <img src="<?= $designImage ?>"><br>
            </div>
            <?php
            if( !empty( $photoImage ) ) : ?>
                <div class="col-3 p-3"><h4>Approval Photo</h4>
                    <img src="<?= $photoImage ?>"><br>
                </div>
            <?php
            endif; ?>
        </div>

    </div>
</div>