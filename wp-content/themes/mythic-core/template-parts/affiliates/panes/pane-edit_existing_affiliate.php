<?php use Mythic_Core\Display\MC_Render;

MC_Render::templatePart( 'search', 'mc-search-autocomplete-affiliates', $args ); ?>
<div class="mc-af-edit-existing-loader">
    <?php MC_Render::templatePart( 'loading', 'loader-animation' ); ?>
</div>
<div class="mc-af-edit-existing-container <?php echo !empty( $args['existing_user_id'] ) ? 'mc-af-edit-existing-container-active' : '' ?>">
    <?php MC_Render::templatePart( 'affiliates/parts/as-affiliates-control', 'edit-existing',
                                   [ 'existing_user_data' => $args['existing_user_data'] ] ); ?>
</div>