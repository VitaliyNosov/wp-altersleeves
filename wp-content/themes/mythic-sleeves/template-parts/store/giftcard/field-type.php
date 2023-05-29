<?php

$args = [
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'tax_query'      => [
        'RELATION' => 'AND',
        [
            'taxonomy' => 'product_group',
            'field'    => 'slug',
            'terms'    => 'giftcard',
        ],
    ],
    'fields'         => 'ids',
    'meta_query'     => [
        'sleeves' => [],
        'digital' => [
            'key'   => 'mc_digital',
            'value' => 1,
        ],
    ],
    'orderby'        => [
        'sleeves' => 'ASC',
    ],
];
if( empty( $digital ) ) {
    $args['meta_query']['digital']['compare'] = 'NOT EXISTS';
}
$giftcards = get_posts( $args );
?>

<div id="field-gift-card-type" class="form-group cas-product-sidebar-info">
    <label for="input-gift-card-value">Gift Card Value</label>
    <select class="form-control" id="input-gift-card-value" name="giftcard_value">
        <?php
        $sleeves = !empty( $sleeves ) ? $sleeves : 0;
        foreach( $giftcards as $giftcard_id ) {
            if( $giftcard_id == 173636 ) continue;
            $giftcard_sleeves = MC_Giftcard_Functions::sleeves( $giftcard_id );
            $selected         = $sleeves == $giftcard_sleeves ? ' selected' : '';
            $label            = $sleeves == 1 ? ' Alter Sleeve' : ' Alter Sleeves';
            //$label .= !empty($digital) ? ' (Digital)' : ' (Physical)';
            echo "<option value='$giftcard_id'$selected>$giftcard_sleeves$label</option>";
        }
        ?>
    </select>
</div>