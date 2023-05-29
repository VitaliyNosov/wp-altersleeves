<?php

use Alter_Sleeves\System\AS_Browse;
use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Mtg_Card_Functions;
use Mythic_Core\Objects\MC_Mtg_Card;
use Mythic_Core\Objects\MC_Mtg_Printing;
use Mythic_Core\Objects\MC_Mtg_Set;

$params             = !empty( $params ) ? $params : AS_Browse::params();
$artist_id          = $params['artist_id'] ?? 0;
$browse_type        = $params['browse_type'];
$card_id            = $params['card_id'] ?? 0;
$page               = $params['target_page'] ?? 1;
$set_id             = $params['set_id'] ?? 0;
$framecode_id       = $params['framecode_id'] ?? 0;
$printing_id        = $params['printing_id'] ?? 0;
$params['set']      = !empty( $params['set_id'] ) ? new MC_Mtg_Set( $params['set_id'] ) : null;
$params['set_name'] = !empty( $params['set'] ) ? $params['set']->name : $params['search_term'];

$show_filter = false;

if( !empty( $browse_type ) ) {
    switch( $browse_type ) {
        case $browse_type == 'bestselling' :
            $results = MC_Alter_Functions::bestsellingResults();
            $title   = 'Check out the bestselling alters';
            break;
        case $browse_type == 'cards' && $card_id && $params['card_name'] == $params['search_term'] :
            $results     = MC_Mtg_Card_Functions::printingsBySearchableName( $params['search_term'] );
            $title       = 'You are browsing printings for "'.$params['search_term'].'"';
            $show_filter = true;
            break;
        case $browse_type == 'cards' && $card_id && $params['card_name'] != $params['search_term'] :
            $card        = new MC_Mtg_Card( $card_id );
            $show_filter = true;
            if( empty( $card ) ) {
                $results = [];
                break;
            }
            $results = !empty( $set_id ) ? MC_Mtg_Card_Functions::printingsBySet( $card_id, $set_id ) : MC_Mtg_Card_Functions::printings( $card_id );
            $title   = 'You are browsing printings for "'.$card->name.'"';
            break;
        case $browse_type == 'cards' && !$card_id && $params['search_term'] :
            $results = MC_Mtg_Card_Functions::search( $params['search_term'], [ 'number' => 0 ] );
            $title   = 'You are browsing for "'.$params['search_term'].'"';
            break;
        case $browse_type == 'sets' && $params['set_id'] && empty( $params['search_term'] ) :
            $results     = empty( $card_id ) ? MC_Mtg_Set_Functions::printings( $params['set_id'] ) : MC_Mtg_Card_Functions::printingsBySet( $card_id,
                                                                                                                                             $set_id );
            $set         = new MC_Mtg_Set( $set_id );
            $title       = 'You are browsing printings for "'.$set->name.'"';
            $show_filter = true;
            break;
        case $browse_type == 'by' && !empty( $artist_id ) :
            $artist      = new WP_User( $artist_id );
            $artist_name = $artist->display_name;
            
            $args                 = [
                'taxonomy'   => 'design_group',
                'meta_query' => [
                    [
                        'key'     => 'mc_variations',
                        'compare' => 'exists',
                    ],
                ],
            ];
            $args['meta_query'][] = [
                'key'     => 'mc_artist',
                'compare' => '=',
                'value'   => $artist_id,
            ];
            $title                = 'Alters by '.$artist_name;
            $results              = get_terms( $args );
            break;
        default :
            $printing = new MC_Mtg_Printing( $printing_id );
            $title    = 'You are browsing ';
            
            $args = [
                'taxonomy'   => 'design_group',
                'meta_query' => [],
            ];
            if( !empty( $framecode_id ) ) {
                $args['meta_query'][] = [
                    'key'   => 'mc_generic',
                    'value' => 1,
                ];
                $args['meta_query'][] = [
                    'key'     => 'mc_framecode_'.$framecode_id,
                    'compare' => 'exists',
                ];
                $title                .= 'universal ';
            } else {
                $args['meta_query'][] = [
                    'key'     => 'mc_printing_'.$printing_id,
                    'compare' => 'exists',
                ];
            }
            if( !empty( $artist_id ) ) {
                $args['meta_query'][] = [
                    'key'     => 'mc_artist',
                    'compare' => '=',
                    'value'   => $artist_id,
                ];
            }
            
            $results   = get_terms( $args );
            $card_name = $printing->name;
            $title     .= 'designs for "<a href="/browse?browse_type=cards&card_id='.$card_id.'" title="Return to results for '.$card_name.'">'.$card_name.' ('.$printing->set_name.' - '.$printing->collector_number.')</a>"';
            break;
    }
} else {
    $title   = 'Browse our most <a href="/browse" title="Check out the recent alters">recent alters</a>';
    $results = MC_Alter_Functions::recentResults();
}
$results_count = count( $results );
$offset        = ( $page - 1 ) * 12;
$results       = array_slice( $results, $offset, 12 );
$last_page     = $offset + count( $results ) == $results_count;
$first_page    = $page == 1;

$max_number_pages = ceil( $results_count / 12 );

if( MC_User_Functions::isAdmin() && !empty( $printing_id ) ) : ?>
    <div class="bg-success py-2 text-center">
        <a class="text-white" href="/cutter-preview?printing_id=<?= $printing_id ?>">View this printing in the mask cutter</a>
    </div>
<?php endif; ?>
<section id="results-wrapper" class="wrapper">

    <aside id="filter" class="border-bottom">
        <div class="row">
            <?php if( !is_front_page() && $max_number_pages > 1 ) : ?>
                <div class="col-auto results-chevron left d-none d-sm-block">
                    <i class="fas fa-chevron-left" style="visibility: hidden"></i>
                </div>
            <?php endif ?>
            <div class="col">
                <h2 class="py-3"><?= $title ?></h2>
                <?php if( $show_filter ) : ?><?php if( $results_count > 0 ) : ?>
                    <p><?= $results_count ?> results returned</p>
                <?php endif; ?>
                    <div class="row">
                        <?php
                        MC_Render::component( 'browse/filter', 'card-name', $params );
                        MC_Render::component( 'browse/filter', 'card-sets', $params );
                        ?>
                    </div>
                <?php endif ?>
            </div>
            <?php if( !is_front_page() && $max_number_pages > 1 ) : ?>
                <div class="col-auto results-chevron left  d-none d-sm-block">
                    <i class="fas fa-chevron-left" style="visibility: hidden"></i>
                </div>
            <?php endif ?>
        </div>
    </aside>

    <div id="search-data" class="d-none">
        <?php foreach( $params as $key => $param ) :
            if( is_array( $param ) || is_object( $param ) ) continue;
            ?>
            <input type="hidden" id="input-<?= $key ?>" value="<?= $param ?>">
        <?php endforeach; ?>
    </div>
    <?php if( $results_count > 12 ) : ?>
        <div id="pagination-top" class="mb-3 text-end">
            <label for="input-results-page-select-top" class="sr-only">Select page</label>
            <select class="form-control-sm input-results-page-select d-inline-block w-auto my-2" id="input-results-page-select-top">
                <?php for( $x = 1; $x <= $max_number_pages; $x++ ) : ?>
                    <option value="<?= $x ?>"<?= $x == $page ? ' selected' : '' ?>><?= $x ?></option>
                <?php endfor; ?>
            </select>
        </div>
    <?php endif; ?>

    <div class="row align-items-center position-relative">
        
        <?php if( !is_front_page() && $max_number_pages > 1 ) : ?>
            <div data-page-number="<?= $first_page ? 0 : $page - 1 ?>" class="col-auto results-chevron left">
                <i class="fas fa-chevron-left" <?php if( empty( $results_count ) || $page < 2 ) : ?>style="visibility: hidden"<?php endif ?>></i>
            </div>
        <?php endif ?>

        <div class="col">
            <div id="results" class="results row align-items-start">
                <?php if( !empty( $results ) ) :
                    foreach( $results as $result ) :
                        
                        ob_start();
                        if( $browse_type == 'cards' ) {
                            if( $card_id ) {
                                $args['printing_id'] = $result->id;
                                MC_Mtg_Card_Functions::render( $args );
                            } else {
                                $args['card_id'] = $result->id;
                                MC_Mtg_Card_Functions::render( $args );
                            }
                        } else if( $browse_type == 'sets' ) {
                            $args['printing_id'] = $result->id;
                            MC_Mtg_Set_Functions::render( $args );
                        } else {
                            if( empty( $browse_type ) || $browse_type == 'bestselling' ) {
                                $alter_id = $result;
                            } else {
                                if( $browse_type == 'by' ) {
                                    $alters = get_term_meta( $result->term_id, 'mc_variations', true );
                                    if( empty( $alters ) ) continue;
                                    $alter_id = $alters[0];
                                    if( empty( $alter_id ) ) continue;
                                } else {
                                    $key      = !empty( $framecode_id ) ? 'mc_framecode_'.$framecode_id : 'mc_printing_'.$printing_id;
                                    $alter_id = get_term_meta( $result->term_id, $key, true );
                                    if( empty( $alter_id ) ) continue;
                                }
                            }
                            if( get_post_status( $alter_id ) != 'publish' ) continue;
                            
                            $args['alter_id']    = $alter_id;
                            $args['printing_id'] = $printing_id;
                            MC_Alter_Functions::render( $args );
                        }
                        $output = ob_get_clean();
                        if( empty( $output ) ) continue;
                        
                        ?>
                        <div class="col-lg-2 col-md-3 col-6 col-sm-4 my-2">
                            <?= $output ?>
                        </div>
                    <?php endforeach;
                else : ?>
                    <h3>No results matched your search</h3>
                <?php endif ?>
            </div>
        </div>

        <div data-page-number="<?= $last_page ? 0 : $page + 1 ?>" class="col-auto results-chevron right">
            <i class="fas fa-chevron-right" <?php if( empty( $results_count ) || $last_page ) : ?>style="visibility: hidden"<?php endif ?>></i>
        </div>

    </div>
    <?php if( $results_count > 12 ) : ?>
        <div id="pagination-bottom" class="mb-3 text-end">
            <label for="input-results-page-select-top" class="sr-only">Select page</label>
            <select class="form-control-sm input-results-page-select d-inline-block w-auto my-2" id="input-results-page-select-bottom">
                <?php for( $x = 1; $x <= $max_number_pages; $x++ ) : ?>
                    <option value="<?= $x ?>"<?= $x == $page ? ' selected' : '' ?>><?= $x ?></option>
                <?php endfor; ?>
            </select>
        </div>
    <?php endif; ?>
</section>

