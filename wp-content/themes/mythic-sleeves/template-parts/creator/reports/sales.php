<?php

use Mythic_Core\Functions\MC_Creator_Functions;

$content_creator_id = MC_Creator_Functions::get_current_creator_id();

$sales = MC_User_Functions::isAdmin() && !empty( $_GET['all'] ) ? MC_Creator_Functions::get_all_sales() : MC_Creator_Functions::get_sales( $content_creator_id );

?>

<h2>Mythic Gaming Sales</h2>

<p>Thank you for contributing to Mythic Gaming. Below you will find the ranked sales of your products.</p>

<?php
//Agreement form
$form_classes = [ 'sale_agreement border rounded' ];

$fields = [
    [
        'label'    => '<small>I am willing to participate in Sitewide sales where the retail price of my product will be lowered at the behest of the site owner for promotional purposes, and the resulting royalties of any such sale will be reflective of the discount provided to the customer, prior to the addition of additional coupons or discounts.</small>',
        'name'     => 'sale_agreement',
        'type'     => 'checkbox',
        'id_part'  => 'sale_agreement',
        'required' => 0,
    ],
];

$hidden_fields = [
    [
        'mc_nonce' => 'mc_sale_agreement_data',
    ],
];

$result = get_user_meta( get_current_user_id(), 'sale_agreement', true );
if( $result !== 0 && $result !== false ) {
    $result = 1;
    update_user_meta( get_current_user_id(), 'sale_agreement', $result );
}

MC_Forms::mcFormRender( $form_classes, $fields, 'Submit', $hidden_fields, [ 'sale_agreement' => $result ] );

?>

<table class="table table-striped mt-0 mb-3">
    <thead>
    <tr>
        <th>Sales Count</th>
        <th>Product ID</th>
        <th>Product Name</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach( $sales as $sale ) : ?>
        <tr>
            <td><?= $sale['count'] ?></td>
            <td><a href="<?= $sale['url'] ?>"><?= $sale['site'].'-'.$sale['product_id'] ?></a></td>
            <td><small><a href="<?= $sale['url'] ?>"><?= str_replace( 'Design Team:', '', $sale['title'] ) ?></a></small></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

