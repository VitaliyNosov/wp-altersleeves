<?php

use Mythic_Core\Functions\MC_Mtg_Card_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;

$card_id     = $card_id ?? $_GET['card_id'] ?? 0;
$printing_id = $printing_id ?? 0;
if( empty( $card_id ) && empty( $printing_id ) ) return;
$type = !empty( $card_id ) ? 'card' : 'printing';

if( empty( $printing_id ) ) {
    $card = MC_Mtg_Card_Functions::printing( $card_id );
} else {
    $card = new MC_Mtg_Printing( $printing_id );
}

if( !is_object( $card ) ) return;
$id               = $card->id;
$name             = $card->name;
$image            = $card->image ?? '';
if( empty($image) ) return;
$set_name         = $card->set_name;
$set_code         = strtoupper( $card->set_code ?? '' );
$collector_number = $card->collector_number ?? '';
$framecode_id     = $card->framecode_id;

$specific_results        = get_terms( [
                                          'taxonomy'     => 'design_group',
                                          'meta_key'     => 'mc_printing_'.$printing_id,
                                          'meta_compare' => 'EXISTS',
                                      ] );
$cleanResults = function($carry, $item) {
    $alters = get_term_meta( $item->term_id, 'mc_variations', true );
    $alter_id = $alters[0];
    if( get_post_status( $alter_id ) === 'removed' ) {
        return $carry;
    }
    array_push($carry, $item);
    return $carry;
};
$specific_results = array_reduce($specific_results, $cleanResults, []);
$specific_results_count  = count( $specific_results );
$universal_results       = get_terms( [
                                          'taxonomy'   => 'design_group',
                                          'meta_query' => [
                                              [
                                                  'key'     => 'mc_framecode_'.$framecode_id,
                                                  'compare' => 'EXISTS',
                                              ],
                                              [
                                                  'key'     => 'mc_generic',
                                                  'compare' => 'EXISTS',
                                              ],
                                          ],
                                      ] );
$universal_results = array_reduce($universal_results, $cleanResults, []);
$universal_results_count = count( $universal_results );
$creations               = $specific_results + $universal_results;
$specific_url            = $universal_url = 'javascript:void(0);';
if( $specific_results_count == 1 && !MC_User_Functions::isAdmin() ) {
    $specific_result = $specific_results[0];
    if( is_object( $specific_result ) ) {
        $specific_result_id = $specific_result->term_id;
        $specific_alter_id  = get_term_meta( $specific_result_id, 'mc_printing_'.$printing_id, true );
        if( !empty( $specific_alter_id ) ) {
            $specific_url = get_the_permalink( $specific_alter_id );
        }
    }
}
if ( $printing_id ) {
    $specific_url .= "?printing_id=".$printing_id;
}
if( $universal_results_count == 1 && !MC_User_Functions::isAdmin() ) {
    $universal_result = $universal_results[0];
    if( is_object( $universal_result ) ) {
        $universal_result_id = $universal_result->term_id;
        $universal_alter_id  = get_term_meta( $universal_result_id, 'mc_framecode_'.$printing_id, true );
        if( !empty( $universal_alter_id ) ) {
            $universal_url = get_the_permalink( $universal_alter_id );
        }
    }
}
?>
<div id="card-<?= $id ?>" class="printing <?= !empty( $creations ) ? 'has-creations' : 'no-creations' ?>">
        <?php
        /*
        ?>
                <div class="card-text py-2 text-center">
                    <small><?= $type == 'card' ? $name : $set_code ?> (<?= $collector_number ?>)</small>
                </div> */ ?>
    <a href="<?= $specific_url ?>" class="result-link specific" title="You are selecting <?= $name ?>"
       data-browse-type="<?= $type == 'printing' ? 'design' : 'cards' ?>" data-printing-id="<?= $printing_id ?>">
            <div class="card-images">
                <div class="card-image-front">
                    <img src="<?= $image ?>" alt="Card image for <?= $name ?>">
                </div>
            </div>
            <div class="card-text py-2 text-center">
                <a href="<?= $specific_url ?>" class="result-link specific font-weight-bold" title="You are selecting <?= $name ?>"
                   data-browse-type="<?= $type == 'printing' ? 'design' : 'cards' ?>"
                   data-printing-id="<?= $printing_id ?>"><?= $specific_results_count ?>
                    specifics</a><br>
                <a href="<?= $universal_url ?>" class="result-link generic" title="You are selecting <?= $name ?>"
                   data-browse-type="<?= $type == 'printing' ? 'design' : 'cards' ?>" data-framecode-id="<?= $framecode_id ?>"
                   data-printing-id="<?= $printing_id ?>"><?= $universal_results_count ?> universals</a>
            </div>

        </a>
    <?php Mythic_Core\Ajax\Browsing\MC_Browse_Results::render_nonce(); ?>
    </div>
