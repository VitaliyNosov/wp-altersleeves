<?php

$commanders = [
    $team['commander_1'] ?? [],
    $team['commander_2'] ?? [],
    $team['commander_3'] ?? [],
];

$count = 1;
foreach( $commanders as $commander ) :
    
    $variation_count = 1;
    ?>
    <div class="display-frame p-3 ">
        <h3>Stretch #<?= $count ?><?php if( !empty( $commander ) ) : ?>  - <?= implode( ', ', $commander ) ?><?php endif; ?></h3>
        <?php
        
        if( !empty( $commander ) ) :
            ?>
            <p><strong>Variation count <?= $variation_count ?></strong></p>
            <div class="row justify-content-start">
                <?php
                
                foreach( $commander as $variation ) :
                    $frame_image = !empty( $variation ) ? MC_Alter_Functions::image( $variation ) : MC_Mythic_Frames_Functions::placeholder_frame();
                    $printing_id = MC_Alter_Functions::printing( $variation ); ?>
                    <div class="mb-2"><a href="<?= get_the_permalink( $variation ) ?>"><?= $variation ?></a></div>
                    <?php
                    
                    $card_id = MC_Mtg_Printing_Functions::getCardId( $printing_id );
                    
                    $printing_args = [
                        'post_type'      => 'printing',
                        'post_status'    => [ 'publish' ],
                        'posts_per_page' => -1,
                        'tax_query'      => [
                            'RELATION' => 'AND',
                            [
                                'taxonomy' => 'mtg_card',
                                'field'    => 'term_id',
                                'terms'    => $card_id,
                            ],
                            [
                                'taxonomy' => 'mtg_set',
                                'field'    => 'term_id',
                                'terms'    => MC_Mtg_Set::unavailableId(),
                                'operator' => 'NOT IN',
                            ],
                        ],
                        'fields'         => 'ids',
                    ];
                    $printings     = get_posts( $printing_args );
                    
                    foreach( $printings as $printing_id ) {
                        $printing       = new MC_Mtg_Printing( $printing_id );
                        $printing_image = !empty( $printing ) ? $printing->imgJpgNormal : MC_Mythic_Frames_Functions::placeholder_card();
                        ?>
                        <div class="col-6 col-sm-3 mb-3 text-center">
                            <div class="frame-wrapper position-relative">
                                <img id="printing-<?= $printing_id ?>" src="<?= $printing_image ?>" class="card-display" alt="Printing Image">
                                <br>
                                <img src="<?= $frame_image ?>" alt="Image for frame alter <?= $variation ?>" class="position-absolute"
                                     style="top:0; left:0;">
                                <br>
                                <?= $printing->set_name ?>
                            </div>
                        </div>
                    <?php }
                    $variation_count++;
                endforeach; ?>
            </div>
        <?php else :
            $frame_image = !empty( $alter_id ) ? MC_Alter_Functions::image( $alter_id ) : MC_Mythic_Frames_Functions::placeholder_frame();
            $printing = !empty( $printing ) ? new MC_Mtg_Printing( $printing ) : null;
            $printing_image = !empty( $printing ) ? $printing->imgJpgNormal : MC_Mythic_Frames_Functions::placeholder_card(); ?>
            <div class="col-6 col-sm-3 p-3 text-center">
                <div class="frame-wrapper position-relative">
                    <img id="printing-0" src="<?= $printing_image ?>" class="card-display" alt="Printing Image">
                    <br>
                    <img src="<?= $frame_image ?>" alt="Image for frame" class="position-absolute" style="top:0; left:0;">
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
    $count++;
endforeach;
