<?php

use Alter_Sleeves\System\AS_Browse;
use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Mtg_Card_Functions;

$params       = !empty( $params ) ? $params : AS_Browse::params();
$browse_type  = $params['browse_type'];
$card_id      = $params['card_id'] ?? 0;
$page         = $params['target_page'] ?? 1;
$set_id       = $params['set_id'] ?? 0;
$framecode_id = $params['framecode_id'] ?? 0;
$printing_id  = $params['printing_id'] ?? 0;

$title   = 'Check out the <a href="/browse?browse_type=bestselling" title="See the bestselling alters">bestselling alters</a>';
$results = MC_Alter_Functions::bestsellingResults();

$results_count = count( $results );
$offset        = ( $page - 1 ) * 12;
$results       = array_slice( $results, $offset, 12 );
$last_page     = $offset + count( $results ) == $results_count;
$first_page    = $page == 1;

$max_number_pages = ceil( $results_count / 12 );
?>
<section id="results-wrapper" class="wrapper">

    <aside id="filter" class="border-bottom">
        <div class="row">
            <?php if( !is_front_page() && $max_number_pages > 1 ) : ?>
                <div class="col-auto results-chevron left d-none d-sm-block">
                    <i class="fas fa-chevron-left" style="visibility: hidden"></i>
                </div>
            <?php endif; ?>
            <div class="col">
                <h2 class="py-3"><?= $title ?></h2>
            </div>
            <?php if( !is_front_page() && $max_number_pages > 1 ) : ?>
                <div class="col-auto results-chevron left  d-none d-sm-block">
                    <i class="fas fa-chevron-left" style="visibility: hidden"></i>
                </div>
            <?php endif; ?>
        </div>
    </aside>
    
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
        <?php endif; ?>

        <div class="col">
            <div id="results" class="results row align-items-start">
                <?php if( !empty( $results ) ) :
                    foreach( $results as $result ) :
                        
                        ob_start();
                        if( $browse_type == 'cards' ) {
                            $args['printing_id'] = $result->id;
                            MC_Mtg_Card_Functions::render( $args );
                        } else {
                            if( empty( $browse_type ) ) {
                                $alter_id = $result;
                            } else {
                                $key      = !empty( $framecode_id ) ? 'mc_framecode_'.$framecode_id : 'mc_printing_'.$printing_id;
                                $alter_id = get_term_meta( $result->term_id, $key, true );
                                if( empty( $alter_id ) ) continue;
                            }
                            
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