<?php

return;

use Mythic_Core\Functions\MC_Alter_Functions;

if( !isset( $idDesign ) ) return;
$tags = MC_Alter_Functions::design_getTags( $idDesign );
if( empty( $tags ) ) return;
foreach( $tags as $key => $tag ) if( $tag->parent == 25593 ) unset( $tags[ $key ] );
if( empty( $tags ) ) return;
?>
<div class="cas-product-alter-tags-wrapper d-none d-md-block">
    <h4>Related Tags</h4>
    <ul class="cas-product-alter-tags">
        <?php foreach( $tags as $tag ) :
            $tagId = $tag->term_id;
            $tagName = $tag->name;
            ?>
            <li id="tag-<?= $tagId ?>" class="d-inline-block"><a class="cas-product-alter-tag"
                                                                 href="/browse?type=tag&id=<?= $tag->term_id ?>"><?= $tagName ?></a>
                <?php if( MC_User_Functions::isAdmin() ) : ?>
                    <i data-design-id="<?= $idDesign ?>" data-tag-id="<?= $tag->term_id ?>"
                       class="text-danger action-tag-remove ml-1 mr-3 fa fa-times"></i>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
