<?php

use Mythic_Core\Functions\MC_Creator_Functions;

$content_creator_id = MC_Creator_Functions::get_current_creator_id();

$sales = MC_Creator_Functions::get_sales( $content_creator_id );
?>

<h2>Mythic Gaming Sales</h2>

<p>Thank you for contributing to Mythic Gaming. Below you will find the ranked sales of your products.</p>

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
                <td><small><a href="<?= $sale['url'] ?>"><?= str_replace('Design Team:', '', $sale['title'] ) ?></a></small></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>