<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Mtg_Printing_Functions;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;
use Mythic_Core\Objects\MC_User;
use Mythic_Core\System\MC_WP;

$isAlter = false;
if( !isset( $target ) ) $target = 0;
if( !isset( $idProduct ) ) $idProduct = MC_Product_Functions::id();
if( isset( $idDesign ) ) {
    $idAlter = MC_Alter_Functions::design_alter( $idDesign );
} else {
    if( isset( $idAlter ) ) {
        $isAlter   = true;
        $idProduct = $idAlter;
        $idDesign  = MC_Alter_Functions::design( $idAlter );
    } else {
        $isAlter = MC_Product_Functions::isAlter( $idProduct );
        if( !$isAlter ) return;
        $idAlter  = $idProduct;
        $idDesign = MC_Alter_Functions::design( $idAlter );
    }
}

$isCollection = MC_Product_Functions::isCollection( $idProduct );
$typeDesign   = MC_Alter_Functions::design_type( $idDesign, 'key' );
$printing_id  = isset( $_GET['printing_id'] ) && $isAlter ? $_GET['printing_id'] : MC_Alter_Functions::printing( $idAlter );
if( empty( $printing_id ) ) $printing_id = MC_Alter_Functions::printing( $idAlter );
/*
if ( $idDesign ) {
    $alters = MC_Alter_Functions::design_alters($idDesign);
    if ( count($alters) > 1 ) {
        $framecode = MC_Mtg_Printing_Functions::codeFromId($printing_id);
        foreach($alters as $alter) {
            if ( has_term($framecode, 'frame_code', $alter) ) {
                $idAlter = $alter;
                break;
            }
        }
    }
}
*/

?>

<div class="cas-product-info-item-wrapper ">
    <div class="cas-product-info__title">
        Alter ID<?php echo MC_User_Functions::isAdmin() ? ' - '.get_post_status( $idAlter ) : ''; ?>
    </div>
    <div class="cas-product-info__data">
        <?php if( MC_User::authorCurrentObject() || MC_User_Functions::isAdmin() ) : ?>
            <span class="product-text-alter alter-id"><a href="/files/prints/png/<?= $idAlter ?>.png" target="_blank"><?= $idAlter ?></a></span>
        <?php else : ?>
            <a href="<?= get_permalink( $idAlter ) ?>"><span class="product-text-alter alter-id"><?= $idAlter ?></span></a>
        <?php endif; ?>
    </div>
</div>

<?php
$printing  = new MC_Mtg_Printing( $printing_id );
$idCard    = $printing->card_id;
$idSet     = $printing->set_id;
$name_card = $printing->name;
$name_set  = $printing->set_name;
$printings = MC_Alter_Functions::printingsWithAlters( $idAlter );
?>
<div class="cas-product-info-item-wrapper">
    <div class="cas-product-info__title">
        Card Name
    </div>
    <div class="cas-product-info__data">

        <a class="mr-2" href="/browse?browse_type=designs&card_id=22336=<?= $idCard ?>&printing_id=<?= $printing_id ?>"><?= $name_card ?></a>
    </div>
</div>

<?php if( !MC_Mtg_Printing_Functions::is_basic_land( $printing_id ) ) : ?>
    <div class="cas-product-info-item-wrapper cas-product-info-printings-wrapper">
        <div class="cas-product-info__title">
            <?php if( empty( $printings ) ) : ?><?php echo $dataKey = 'Printing'; ?><?php else : ?><?php echo $dataKey = 'Printings'; ?><?php endif; ?>
        </div>
        <div class="cas-product-info__data" data-options="printings">
            <?php if( empty( $printings ) ) : ?>
                <a class="mr-2" href="/browse?browse_type=cards&card_id=<?= $idCard ?>"><?= $name_set ?></a><?php else : ?>
                <span id="selected-printings" class="cas-product-info--selected  unselectable">
<?= $name_set ?>
            </span><i id="chevron-printings" class="fas fa-chevron-down"></i>
            <?php endif; ?>
        </div>
        <?php
        if( !empty( $printings ) ) : ?>
            <ul id="options-printings" class="options-printings cas-product-info-options cas-product-info-printings">
                <?php foreach( $printings as $printing ) :
                    $optionValue = $printing['value'];
                    $optionText = $printing['text'];
                    $idAlter = $printing['alter_id'];
                    ?>
                    <li class="cas-product-info-option"><a class="cas-product-info-printing" href="javascript:void(0);"
                                                           data-alter-id="<?= $idAlter ?>" data-value="<?= $optionValue ?>"
                                                           data-option-type="printings" data-target="<?= $target ?>"><?= $optionText ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php
if( ( !MC_User::authorCurrentObject() || MC_User_Functions::isAdmin() ) && !$isCollection ) :
    $idCreator = MC_WP::authorId( $idProduct );
    $creatorUserName = get_the_author_meta( 'user_nicename', $idCreator );
    $creatorDisplayName = MC_Alter_Functions::getAlteristDisplayName( $idProduct );
    ?>
    <div class="cas-product-info-item-wrapper ">
        <div class="cas-product-info__title">
            Alterist
        </div>
        <div class="cas-product-info__data">
            <a href="/alterist/<?= $creatorUserName ?>"><?= $creatorDisplayName ?></a>
        </div>
    </div>
<?php endif; ?>
