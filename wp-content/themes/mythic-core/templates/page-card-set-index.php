<?php
/**
 * Template Name: Card Set Index
 */

use Mythic_Core\Objects\MC_Mtg_Set;
use Mythic_Core\System\MC_Redirects;


if( !is_user_logged_in() || !MC_User_Functions::isAdmin() ) MC_Redirects::home();

add_filter( 'mc_content_container_filter', function( $classes ) {
    $key = array_search( 'container', $classes );
    unset( $classes[ $key ] );

    return $classes;
}, 10, 1 );

get_header(); ?>
    <div id="page" class="page">
        <div class="wing bg-white-wing"></div>
        <div class="container content bg-white-content">
            <h1 class="text-center">Browse designs by sets</h1>
            <p class="text-center">Select a set below to browse submitted designs for those printings</p>
            <?php
            $available = get_term_by( 'name', 'Available', 'mtg_set' );
            if( $available != null ) {
                $args          = [
                    'taxonomy'   => 'mtg_set',
                    'fields'     => 'all',
                    'parent'     => MC_Mtg_Set::availableId(),
                    'orderby'    => 'meta_value_num',
                    'order'      => 'DESC',
                    'meta_query' => [
                        [
                            'key'  => 'mc_set_release_date',
                            'type' => 'NUMERIC',
                        ],
                    ],
                ];
                $availableSets = get_terms( $args );

                foreach( $availableSets as $key => $availableSet ) {
                    $cards = get_term_meta( $availableSet->term_id, 'mc_cards_designs', true );
                    if( empty( $cards ) ) unset( $availableSets[ $key ] );
                }

                $columns = 3;
                $columns = array_chunk( $availableSets, ceil( count( $availableSets ) / 3 ) ); ?>
                <div class="row align-items-start">
                    <?php
                    foreach( $columns as $column ) : ?>
                        <ul class="col-sm-6 col-md-4 list-unstyled mb-0">
                            <?php
                            foreach( $column as $set ) {
                                if( $set->name == 'Unavailable' ) continue;
                                $idSet    = $set->term_id;
                                $name_set = $set->name;
                                //$icon    = get_term_meta( $idSet, 'mc_set_icon', true );
                                echo '<li><a href="/browse?type=set&set_id='.$idSet.'">'.$name_set.'</a></li>';
                            } ?>
                        </ul>
                    <?php
                    endforeach; ?>
                </div>
                <?php
            } ?>
        </div>
        <div class="wing bg-white-wing"></div>
    </div>

<?php
get_footer();