<?php

use Mythic_Core\Display\MC_Render;

$label   = !empty( $label ) ? $label : 'Search for affiliate';
$classes = !empty( $classes ) ? $classes : '';
?>

<div class="mc-af-edit-existing-search">
    <label for="mc-af-edit-existing-search-input"><?php echo $label ?></label>
    <div class="mc-af-edit-existing-search-input-container">
        <?php MC_Render::templatePart( 'loading', 'loader-animation' ); ?>
        <input type="text" placeholder="Enter a name" id="mc-af-edit-existing-search-input"
               autocomplete="off"
               name="mc-af-edit-existing-search-input" class="mc-af-edit-existing-search-input <?php echo $classes ?>"
               value="<?php echo !empty( $existing_user_data['displayName'] ) ? $existing_user_data['displayName'] : '' ?>">
        <span class="as-clear-search">&#10006;</span>
        <ul class="mc-af-edit-existing-search-results"></ul>
        <?php Mythic_Core\Ajax\Search\MC_Ajax_Search_Autocomplete_Users::render_nonce(); ?>
    </div>
    <div class="mc-af-search-autocomplete-no-results">
        <p>No results found</p>
    </div>
</div>