<div class="mc-af-edit-existing-loader">
    <?php use Mythic_Core\Display\MC_Render;

    MC_Render::templatePart( 'loading', 'loader-animation' ); ?>
</div>
<div class="mc-af-edit-existing-container mc-af-edit-existing-container-coupons <?php echo !empty( $args['existing_promotion']['promotionId'] ) ? 'mc-af-edit-existing-container-active' : '' ?>">
    <?php MC_Render::templatePart( 'affiliates/parts/as-affiliates-coupons-control', 'edit-existing',
                                   [ 'existing_promotion' => $args['existing_promotion'], 'existing_user_id' => $args['existing_user_id'] ] ); ?>
    <div class="mc-promotions-codes-container">
        <?php MC_Render::templatePart( 'affiliates/parts/as-affiliates-coupons', 'codes',
                                       [ 'promotion_id' => $args['existing_promotion']['promotionId'] ] ); ?>
    </div>
</div>