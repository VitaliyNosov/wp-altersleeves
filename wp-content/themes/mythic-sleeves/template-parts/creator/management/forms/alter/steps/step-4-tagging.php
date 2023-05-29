<?php

use Mythic_Core\Functions\MC_Alter_Functions;

?>
<div class="tab-pane fade p-3" id="linking_submit" role="tabpanel" aria-labelledby="linking_tab">

    <h2>Tag your alter</h2>
    <?php
    
    if( empty( $idAlter ) ) $idDesign = MC_Alter_Functions::_get();
    if( empty( $idDesign ) ) $idDesign = MC_Alter_Functions::design_get();
    $tags = wp_get_object_terms( $idDesign, 'product_tag' );
    
    $idsTags = [];
    if( !empty( $tags ) ) {
        foreach( $tags as $objectTag ) {
            if( !is_object( $objectTag ) ) continue;
            $idsTags[] = $objectTag->term_id;
        }
    }
    
    ?>
    <div id="field-design-tags" class="field-wrapper mb-3 has-info" data-info-id="info_tags">
        <?php
        $argsChildTags    =
            [ 'taxonomy' => 'product_tag', 'hide_empty' => false, 'name__like' => 'Theme: ' ];
        $objectsChildTags = get_terms( $argsChildTags );
        
        ?>
        <ul class="clearfix management-tags">
            <?php
            foreach( $objectsChildTags as $objectChildTag ) :
                $nameChildTag = $objectChildTag->name;
                $nameChildTag = str_replace( 'Theme:', '', $nameChildTag );
                $idChildTag = $objectChildTag->term_id;
                $selected = in_array( $idChildTag, $idsTags );
                ?>
                <li class="tag">
                    <a href="javascript:void(0);" data-tag-id="<?= $idChildTag ?>" <?php if( $selected ) : ?>class="selected"<?php endif; ?>>
                        <?= $nameChildTag ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
        $value = !empty( $idsTags ) ? json_encode( $idsTags ) : '';
        ?>
        <input type="hidden" id="input-design-tags" value="<?= $value ?>">
    </div>
</div>
