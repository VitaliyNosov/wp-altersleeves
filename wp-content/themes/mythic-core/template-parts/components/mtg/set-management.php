<?php

use Mythic_Core\Objects\MC_Mtg_Set;

$args = [
    'taxonomy'   => [ 'mtg_set' ],
    'hide_empty' => true,
    'fields'     => 'all',
    'orderby'    => 'meta_value_num',
    'order'      => 'DESC',
    'meta_query' => [
        [
            'key'  => 'mc_set_release_date',
            'type' => 'NUMERIC',
        ],
    ],
];
$sets = get_terms( $args );
ob_start(); ?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Available</th>
        <th>Set Name</th>
        <th>Set Code</th>
        <th>Release Date</th>
        <th>Printings</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach( $sets as $set ) :
        $id = $set->term_id;
        $name = $set->name;
        $name = utf8_decode( $name );
        if( strpos( $name, 'Tokens' ) || strpos( $name, 'Promos' ) ) continue;
        $code = $set->slug;
        if( strlen( $code ) > 5 ) {
            wp_delete_term( $id, 'mtg_set' );
            continue;
        }
        $available = $set->parent == MC_Mtg_Set::availableId();
        $date      = date( 'd/m/Y', get_term_meta( $id, 'mc_set_release_date', true ) );
        $printings = $set->count;
        ?>
        <tr>
            <td>
                <div class="form-check">
                    <input class="form-check-input set-availability" type="checkbox" id="set-<?= $id ?>" value="<?= $id ?>" <?php checked( $available,
                                                                                                                                           1 ) ?>>
                    <?php Mythic_Core\Ajax\Configuration\MC_Set_Status::render_nonce(); ?>
                </div>
            </td>
            <td>
                <?= $name ?>
            </td>
            <td>
                <?= $code ?>
            </td>
            <td>
                <?= $date ?>
            </td>
            <td>
                <?= $printings ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>