<?php

use Mythic_Core\Objects\MC_User;

if( empty( $idUser ) ) return;
$description = get_the_author_meta( 'description', $idUser );
$charity     = MC_User::charity( $idUser );
if( empty( $description ) && empty( $charity['status'] ) ) return;
?>
<div class="description">
    <?php if( isset( $charity['status'] ) && !empty($charity['status']) ): ?>
        <h2>This Thanksgiving weekend, I'm supporting:</h2>
        <div class="my-3 p-3 bg-light rounded">
            <div class="row align-items-start">
                <?php if( isset( $charity['image'] ) && !empty( $charity['image'] ) ): ?>
                    <div class="charity_image_cont col-auto col-sm-4">
                        <img src="<?= $charity['image'] ?>" class="charity_image">
                    </div>
                <?php endif ?>
                <div class="charity_data_cont col mt-0">
                    <h3 class="mt-0"><a href="<?= $charity['url'] ?? '' ?>" target="_blank"><?= $charity['name'] ?? '' ?></a></h3>
                    <div><?= $charity['reason'] ?? '' ?></div>
                </div>
                <div class="charity_button_cont col-12">
                    <div class="float-left">
                        <?php if( !MC_Woo_Cart_Functions::empty() ) : ?>
                            <a href="/cart?charity_creator_id=<?= $idUser ?>" class="btn btn-info">Support & Save</a>
                        <?php else : ?>
                            <a href="#" class="btn btn-info charity_support" data-charity_user_id="<?= $idUser ?>">Support & Save</a>
                        <?php endif; ?>
                    </div>
                    <div class="charity_button_info">
                        By clicking to support <?= $charity['name'] ?? '' ?> you will also receive this content creator's 5% discount at checkout
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
    
    <?= !empty( $description ) ? wpautop( $description ) : ''; ?>
</div>
