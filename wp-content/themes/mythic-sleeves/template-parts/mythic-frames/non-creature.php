<?php

use Mythic_Core\Objects\MC_Mtg_Printing;

$alter_id     = $team['Non-Creature'] ?? 0;
$noncreatures = $noncreatures ?? MC_Mythic_Frames_Functions::noncreatures();

$eras   = [ 2015, 2003, 1997 ];
$status = get_post_status( $alter_id );

?>

    <div class="display-frame p-3 ">
        <h3>Non-Creature<?php if( !empty( $alter_id ) ) : ?> -
                <a href="<?= get_the_permalink( $alter_id ) ?>"><?= $alter_id ?></a><?php endif; ?></h3>
        
        <?php if( strpos( $status, 'approved' ) ) : ?>
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
            
            $printing_id    = MC_Alter_Functions::printing( $alter_id );
            $printing       = new MC_Mtg_Printing( $printing_id );
            $frame_image    = !empty( $alter_id ) ? MC_Alter_Functions::image( $alter_id ) : MC_Mythic_Frames_Functions::placeholder_frame();
            $printing_image = !empty( $printing ) ? $printing->imgJpgNormal : MC_Mythic_Frames_Functions::placeholder_card(); ?>
            <div class="col-6 col-sm-3 p-3 text-center">
                <div class="frame-wrapper position-relative">
                    <img id="printing-<?= $printing_id ?>" src="<?= $printing_image ?>" class="card-display" alt="Printing Image">
                    <br>
                    <img src="<?= $frame_image ?>" alt="Image for frame alter <?= $alter_id ?>" class="position-absolute" style="top:0; left:0;"><br>
                    Selected
                </div>
            </div>
            <?php
            foreach( $eras as $era ) :
                $printing_id = $noncreatures[ $era ];
                $printing = new MC_Mtg_Printing( $printing_id );
                $printing_image = !empty( $printing ) ? $printing->imgJpgNormal : MC_Mythic_Frames_Functions::placeholder_card();
                ?>
                <div class="col-6 col-sm-3 p-3 text-center">
                    <div class="frame-wrapper position-relative">
                        <img id="printing-<?= $printing_id ?>" src="<?= $printing_image ?>" class="card-display" alt="Printing Image">
                        <br>
                        <img src="<?= $frame_image ?>" alt="Image for frame alter <?= $alter_id ?>" class="position-absolute"
                             style="top:0; left:0;"><br>
                        <?= $era ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php
unset( $alter_id );
unset( $frame_image );