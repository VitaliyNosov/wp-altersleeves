<?php

$alter_id    = $team['land'] ?? 0;
$frame_image = !empty( $alter_id ) ? MC_Alter_Functions::image( $alter_id ) : '';

$lands = [
    $land_plains ?? MC_Mythic_Frames_Functions::plains(),
    $land_island ?? MC_Mythic_Frames_Functions::island(),
    $land_swamp ?? MC_Mythic_Frames_Functions::swamp(),
    $land_mountain ?? MC_Mythic_Frames_Functions::mountain(),
    $land_forest ?? MC_Mythic_Frames_Functions::forest(),
];

$status = get_post_status( $alter_id );
?>

    <div class="display-frame p-3 ">
        <h3>Land <?php if( !empty( $alter_id ) ) : ?> -
                <a href="<?= get_the_permalink( $alter_id ) ?>"><?= $alter_id ?></a><?php endif; ?></h3>
        <?php if( MC_Vars::stringContains( $status, 'approved' ) ) : ?>
            <span class="text-success">Approved</span>
        <?php else : ?>
            <span class="text-danger">Not approved/Not submitted</span>
        <?php endif; ?>
        -
        <?php if( MC_Vars::stringContains( $status, 'internal' ) ) : ?>
            <span class="text-success">Internal</span>
        <?php else : ?>
            <span class="text-danger">NOT INTERNAL</span>
        <?php endif; ?>
        <div class="row justify-content-start">
            <?php
            foreach( $lands as $land ) :
                $printing_id = $land;
                $frame_image = !empty( $alter_id ) ? MC_Alter_Functions::image( $alter_id ) : MC_Mythic_Frames_Functions::placeholder_frame();
                $printing = new MC_Mtg_Printing( $printing_id );
                $printing_image = !empty( $printing ) ? $printing->imgJpgNormal : MC_Mythic_Frames_Functions::placeholder_card();
                ?>
                <div class="col-6 col-sm p-3 text-center">
                    <div class="frame-wrapper position-relative">
                        <img id="printing-<?= $printing_id ?>" src="<?= $printing_image ?>" class="card-display" alt="Printing Image">
                        <br>
                        <img src="<?= $frame_image ?>" alt="Image for frame alter <?= $alter_id ?>" class="position-absolute" style="top:0; left:0;">

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php
unset( $alter_id );
unset( $frame_image );