<?php

if( !isset( $idDesign ) ) return;
if( !isset( $type ) ) $type = 'design';

$printing_id = isset( $_GET['printing_id'] ) ? $_GET['printing_id'] : 0;
if( $printing_id == 0 ) {
    if( isset( $_GET['card_id'] ) && empty( $idCard ) ) $idCard = $_GET['card_id'];
    $printingArgs = [
        'post_type'      => 'printing',
        'posts_per_page' => 1,
        'tax_query'      => [
            'RELATION' => 'AND',
            [
                'taxonomy' => 'mtg_card',
                'field'    => 'term_id',
                'terms'    => $idCard,
            ],
        ],
        'fields'         => 'ids',
    ];
    if( isset( $idSet ) ) {
        $printingArgs['tax_query'][] =
            [
                'taxonomy' => 'mtg_set',
                'field'    => 'term_id',
                'terms'    => $idSet,
            ];
    }
    $printings = get_posts( $printingArgs );
    if( !empty( $printings ) ) $printing_id = $printings[0];
}
if( empty( $printing_id ) ) return;
$printing  = new MC_Mtg_Printing( $printing_id );
$name_card = $printing->name;
$framecode = $printing->framecode_id;

$nameAlterist   = MC_Alter_Functions::getAlteristDisplayName( $idDesign );
$creatorProfile = MC_Alter_Functions::getAlteristProfileUrl( $idDesign );
$alters         = as_design_alters( $idDesign );
$idAlter        = as_design_alter( $idDesign );
foreach( $alters as $alter ) {
    $framecodes = wp_get_object_terms( $alter, 'frame_code' );
    if( empty( $framecodes ) ) break;
    foreach( $framecodes as $objectFramecode ) {
        if( !is_object( $objectFramecode ) ) continue;
        $nameFramecode = $objectFramecode->name;
        if( $nameFramecode == $framecode ) $idAlter = $alter;
    }
}

$image = MC_Alter_Functions::image( $idAlter, 'lo' );

$printingImage = $printing->imgJpgNormal;

$info = '';
$info .= '<span><a href="'.$creatorProfile.'">'.$nameAlterist.'</a></span>';
$cart = true;

$url = get_the_permalink( $idAlter ).'?printing_id='.$printing_id.'&card_name='.urlencode( $name_card );

?>
<div id="<?= $idDesign ?>" class="browsing-item browsing-item-frame col-6 col-sm-4 col-md-3 col-xl-2 my-3">
    <div class="text-center images">
        <a href="<?= $url ?>">
            <img class="card-corners card-display printing" src="<?= $printingImage ?>">
            <img class="card-corners card-display alter" src="<?= $image ?>">
        </a>
    </div>
    <?php if( $cart ) :
        $cart = '<i class="fas fa-shopping-cart"></i>';
        ?>
        <div class="row pt-2">
            <?php echo MC_Product_Functions::getPrices($idDesign); ?>
            <div data-printing-id="<?= $printing_id ?>" data-alter-id="<?= $idDesign ?>"
                 class="col-auto float-end cas-add-to-cart browsing-item__cart-link" data-alter-id="<?= $idDesign ?>"><?= $cart ?></div>
            <?php Mythic_Core\Ajax\Store\Cart\MC_Add_Alter::render_nonce(); ?>
        </div>
    <?php endif; ?>
    <div class="browsing-item__info">
        <?= $info ?>
        <?php
        if( MC_User_Functions::isAdmin() ) : ?><br>Alter: <?php echo !empty( $idAlter ) ? $idAlter : as_design_alter( $idDesign ) ?>
            <?= isset( $idDesign ) ? '<br>Design: '.$idDesign : '' ?><?php endif; ?>
    </div>
</div>

<style>
    .browsing-item-frame .images {
        position: relative;
    }

    .browsing-item-frame .alter {
        position: absolute;
        left: 0;
        top: 0;
        z-index: 1;
    }
</style>