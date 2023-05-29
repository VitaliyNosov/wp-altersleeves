<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Users\MC_Affiliates;

if( !MC_Affiliates::is() && !MC_User_Functions::isAdmin() ) return;

$idUser = wp_get_current_user()->ID;

$favDesigns = get_user_meta( $idUser, 'mc_fav_designs', true );
if( !is_array( $favDesigns ) ) $favDesigns = [];
?>
<div id="field-alter-favoriter" class="field-wrapper">
    <input type="hidden" id="input-user-id" value="<?= $idUser ?>">
    <h2>Select your favorite alters</h2>
    <p>Enter your favorite alters below, either using their URL or ID. Maximum of 12. Duplicates will be removed.</p>
    <div class="form-row align-items-center">
        <div class="col">
            <label class="sr-only" for="input-alter-favoriter">Select your favorite alters</label>
            <input id="input-alter-favoriter" class="form-control" autocomplete="off" type="text" placeholder="Enter Alter Id or URL" name="fav-term">
            <?php Mythic_Core\Ajax\Creator\Management\MC_Alter_Favoriter::render_nonce(); ?>

        </div>
        <div class="col-auto">
            <div id="loading-card-search" class="loading-inline" style="display: none;"></div>
        </div>
    </div>
    <button id="save-fav-alters" class="btn cas-button--blue blue--button"><strong>Save Changes</strong></button>
    <div class="loading" style="display: none;"></div>
    <span class="notice-saved text-success px-3" style="display: none;">Saved Changes</span>
    <?php Mythic_Core\Ajax\Creator\Management\MC_Save_Favorites::render_nonce(); ?>

    <div id="alter-favorites" class="row justify-content-start">
        <?php
        foreach( $favDesigns as $key => $idDesign ) :
            if( empty( get_the_title( $idDesign ) ) || get_post_status( $idDesign ) != 'publish' ) {
                unset( $favDesigns[ $key ] );
                continue;
            }
            $alters             = MC_Alter_Functions::design_alters( $idDesign );
            $response['alters'] = $alters;
            if( empty( $alters ) || !is_array( $alters ) ) continue;
            $idAlter = $alters[0];
            if( !is_numeric( $idAlter ) ) continue;
            $image = MC_Alter_Functions::getCombinedImage( $idAlter );
            ?>
            <div id="alter-fav-id-<?= $idDesign ?>" class="col-md-2 col-sm-3 text-center mt-3">
                <p class="my-1 text-danger design-remove-fav" data-design-id="<?= $idDesign ?>">Remove
                    <i class="mx-2 fas fa-times"></i></p>
                <img class="card-display" src="<?= $image ?>">
            </div>
        <?php endforeach; ?>
    </div>
    <input id="input-alter-favorited" type="hidden" value="<?= json_encode( $favDesigns, JSON_NUMERIC_CHECK ) ?>">
</div>