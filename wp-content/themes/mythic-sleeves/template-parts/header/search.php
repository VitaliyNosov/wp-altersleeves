<?php

use Mythic_Core\Settings\MC_Site_Settings;
use Mythic_Core\Utils\MC_Vars;

$search_text   = MC_Site_Settings::value( 'header_search_text' );
$search_groups = apply_filters( 'mc_header_search_groups', [] );

?>
<div id="header-search-wrapper" class="wrapper search-wrapper p-0 position-relative col-12 col-md order-5
 order-md-2">

    <!-- Search Form -->
    <form role="search" method="get" id="header-search" action="<?= esc_url( home_url( '/' ) ); ?>" accept-charset="UTF-8" class="search">
        <div id="header-search-field" class="search-field">
            <label class="form-label" for="header-search-input" class="sr-only"><?= $search_text ?></label>
            <input type="text" name="s" id="header-search-input" value="" placeholder="Search for cards, alterists, content creators..." autocomplete="off"
                   autocapitalize="none" spellcheck="false" maxlength="1024" class="search-input py-2 px-3">
            <span class="search-clear clear">&#10006;</span>
            <?php MC_Render::loader( 'search-loader' ) ?>
            <?php \Mythic_Core\Ajax\Search\MC_Search_Autocomplete::render_nonce(); ?>
        </div>
    </form>
    
    <?php if( !empty( $search_groups ) ) : ?>
        <div id="header-search-results" class="header-results results position-absolute">
            <?php
            foreach( $search_groups as $search_group ) :
                $search_group = MC_Vars::parseableString( $search_group );
                ?>
                <ul class="results-group" data-search-group="<?= $search_group ?>"></ul>
            <?php endforeach; ?>
        </div>
        <div id="header-no-results" class="header-no-results no-results">
            <?= apply_filters( 'mc_header_search_results_empty', 'No results matched your search' ) ?>
        </div>
    <?php endif; ?>

</div>
