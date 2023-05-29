<?php

use Mythic_Core\Objects\MC_User;

if( empty( $idUser ) && empty( $idAlterist ) ) return;
if( !empty( $idAlterist ) ) {
    $idUser = $idAlterist;
}

if( $idUser == 3552 ) {
    $ids = [ 167955, 167961, 193060 ];
    
    ?>
    <div class="py-3">
        <h3>Published by Mental Misplay</a></h3>
        <div class="row justify-content-start align-items-start">
            <?php foreach( $ids as $id ) : ?>
                <div id="<?= $id ?>" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                    <?php MC_Alter_Functions::render( [ 'alter_id' => $id, 'patreon' => true ] ); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
} else if( $idUser == 6975 ) {
    ?>
    <div class="py-3">
        <h3>Published by EDHRec Podcast</a></h3>
        <div class="row justify-content-start align-items-start">

            <div id="189810" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 189810 ] ); ?>
            </div>

            <div id="189805" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 189805 ] ); ?>
            </div>

            <div id="189798" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 189798 ] ); ?>
            </div>
            
            <?php /*
            <div id="192493" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render([ 'alter_id' => 192493 ]); ?>
            </div>

            <div id="192491" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render([ 'alter_id' => 192491 ]); ?>
            </div>
             */ ?>

        </div>
    </div>
    <?php
} else if( $idUser == 6604 ) {
    ?>
    <div class="py-3">
        <h3>Published by Taalia Vess</a></h3>
        <div class="row justify-content-start align-items-start">

            <div id="188826" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 184268 ] ); ?>
            </div>

            <div id="186943" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 186943 ] ); ?>
            </div>

            <div id="189584" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 189584 ] ); ?>
            </div>

            <div id="189584" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 195041 ] ); ?>
            </div>

        </div>
    </div>
    <?php
} else if( $idUser == 3137 ) {
    ?>
    <div class="py-3">
        <h3>Published by Mana Curves</a></h3>
        <div class="row justify-content-start align-items-start">
            <div id="177167" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <div class="text-center">
                    <img class="card-corners card-display"
                         src="https://www.altersleeves.com/files/alterists/schmandrewart/designs/177167/browsing/53371.jpg">
                </div>
            </div>
        </div>
    </div>
    <?php
} else if( $idUser == 6603 ) { ?>
    <div class="py-3">
        <h3>Published by I Hate Your Deck</a></h3>
        <div class="row justify-content-start align-items-start">

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 202896, 'patreon' => true ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 200263, 'patreon' => true ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 199374, 'patreon' => true ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 194937, 'patreon' => true ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 192555, 'patreon' => true ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 189651, 'patreon' => true ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 189653, 'patreon' => true ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 187732, 'patreon' => true ] ); ?>
            </div>

        </div>
    </div>
    <?php
} else if( $idUser == 7434 ) { ?>
    <div class="py-3">
        <h3>Published by Drinks Of Alara</a></h3>
        <div class="row justify-content-start align-items-start">
            <div id="188828" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 188828 ] ); ?>
            </div>

            <div id="188828" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 188832 ] ); ?>
            </div>

            <div id="188826" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 188826 ] ); ?>
            </div>

            <div id="188565" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 188565 ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 188830 ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 192344 ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 197127 ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 200690 ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 200690 ] ); ?>
            </div>

            <div class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 200690 ] ); ?>
            </div>

        </div>

    </div>
    <?php
} else if( $idUser == 7941 ) { ?>
    <div class="py-3">
        <h3>Published by Triple Mango Threat</a></h3>
        <div class="row justify-content-start align-items-start">
            <div id="191699" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 191699 ] ); ?>
            </div>
            <div id="199376" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 199376 ] ); ?>
            </div>
            <div id="201120" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 201120 ] ); ?>
            </div>
        </div>
    </div>
    <?php
} else if( $idUser == 2947 ) { ?>
    <div class="py-3">

        <h3>Published by Pleasant Kenobi</a></h3>

        <div class="row justify-content-start align-items-start">
            <?php foreach( [ 202069,202071,206724, 210614, 210618 ] as $alter ) : ?>
                <div id="<?= $alter ?>" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                    <?php MC_Alter_Functions::render( [ 'alter_id' => $alter, 'patreon' => true ] ); ?>
                </div>
            <?php endforeach; ?>
        </div>
        
    </div>
    <?php
} else if( $idUser == 6608 ) { ?>
    <div class="py-3">
        <h3>Published by Garbage Andy</a></h3>
        <div class="row justify-content-start align-items-start">
            <div id="191699" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 194960 ] ); ?>
            </div>
        </div>
    </div>
    <?php
} else if( $idUser == 11633 ) { ?>
    <div class="py-3">
        <h3>Published by Degen Gaming</a></h3>
        <div class="row justify-content-start align-items-start">
            <?php foreach( [ 205229, 205233, 205235, 205237, 205239 ] as $alter ) : ?>
                <div id="<?= $alter ?>" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                    <?php MC_Alter_Functions::render( [ 'alter_id' => $alter, 'patreon' => false ] ); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
} else if( $idUser == 6541 ) { ?>
    <div class="py-3">
    <h3>Published by Magic Mics</a></h3>
    <div class="row justify-content-start align-items-start">
        <div id="179250" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
            <?php MC_Alter_Functions::render( [ 'alter_id' => 179250 ] ); ?>
        </div>
        <div id="179253" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
            <?php MC_Alter_Functions::render( [ 'alter_id' => 179253 ] ); ?>
        </div>
        <div id="202211" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
            <?php MC_Alter_Functions::render( [ 'alter_id' => 202211 ] ); ?>
        </div>
    </div>
    <?php /*
            <div id="179257" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 179257 ] ); ?>
            </div>
 ?>

        </div>
    </div>
    <?php
} else if( $idUser == 2658 ) {
    /*
    ?>
    <div class="py-3">
        <h3>Published by Sheepwave</a></h3>
        <div class="row justify-content-start align-items-start">
            <div id="191699" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => 204444, 'patreon' => true, 'disabled_text' => 'Artist Exclusive' ] ); ?>
            </div>
        </div>
    </div>
    <?php
    */
} else if( $idUser == 12073 ) {
    $ids = [
        207165,
        207167,
        207170,
        207178,
        207180,
        207184,
        207186,
        207188,
        207190,
        207193
    ];
    ?>
    <h3>Published by Archidekt</a></h3>
    <div class="row justify-content-start align-items-start">
        <?php foreach( $ids as $id ) : ?>
            <div id="<?= $id ?>" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                <?php MC_Alter_Functions::render( [ 'alter_id' => $id ] ); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?
}

$designs = MC_User::meta( 'mc_fav_designs', $idUser );
if( empty( $designs ) ) return;
shuffle( $designs );
$designs = array_slice( $designs, 0, 12 );
?>
    <div class="py-3">
        <h3>Favorite Alters</a></h3>
        <div class="row justify-content-start align-items-start">
            <?php
            foreach( $designs as $idDesign ) {
                if( get_post_type( $idDesign ) !== 'product' && get_post_type( $idDesign ) !== 'design' ) continue;
                if( get_post_type( $idDesign ) == 'design' ) $idDesign = MC_Alter_Functions::design_alter( $idDesign );
                include( TP_ITEMS_ALTER_A );
            }
            ?>
        </div>
    </div>

<?php
/*
use Mythic_Core\Objects\MC_User;

$idAlterist = !empty( $idAlterist ) ? $idAlterist : 0;
if( empty( $idUser ) && empty( $idAlterist ) ) return;
if( !empty( $idAlterist ) ) {
    $idUser = $idAlterist;
}

$prod_shares = MC_Licensing_Functions::getLicensingByPublisher( 2, $idUser );

if( !empty( $prod_shares ) ) { ?>
    <div class="py-3">
        <?php if( !empty( $name ) ) { ?>
            <h3>Published by <?php echo $name ?></a></h3>
        <?php } ?>
        <div class="row justify-content-start align-items-start">
            
            <?php foreach( $prod_shares as $prod_share ) { ?>

                <div id="<?php echo $prod_share['product_id'] ?>" class="browsing-item col-6 col-sm-4 col-md-3 col-xl-2 my-3">
                    <?php MC_Alter_Functions::render( [ 'alter_id' => $prod_share['product_id'] ] ); ?>
                </div>
            <?php } ?>

        </div>
    </div>
<?php }

$designs = MC_User::meta( 'mc_fav_designs', $idUser );
if( empty( $designs ) ) return;
shuffle( $designs );
$designs = array_slice( $designs, 0, 12 );
?>
<div class="py-3">
    <h3>Favorite Alters</a></h3>
    <div class="row justify-content-start align-items-start">
        <?php
        
        foreach( $designs as $idDesign ) {
            if( get_post_type( $idDesign ) !== 'product' && get_post_type( $idDesign ) !== 'design' ) continue;
            if( get_post_type( $idDesign ) == 'design' ) $idDesign = MC_Alter_Functions::design_alter( $idDesign );
            include( TP_ITEMS_ALTER_A );
        }
        ?>
    </div>
</div>
*/