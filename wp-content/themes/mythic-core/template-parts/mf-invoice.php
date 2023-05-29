<?php

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$logo_path = get_template_directory_uri().'/src/img/logos/mythic-frames/logo/red-black/500.png';
$type      = pathinfo( $logo_path, PATHINFO_EXTENSION );
$data      = file_get_contents( $logo_path );
$base64    = 'data:image/'.$type.';base64,'.base64_encode( $data );

?>
<style>

    @font-face {
        font-family: 'Open Sans';
        font-style: normal;
        font-weight: normal;
        src: url(http://themes.googleusercontent.com/static/fonts/opensans/v8/cJZKeOuBrn4kERxqtaUH3aCWcynf_cDxXwCLxiixG1c.ttf) format('truetype');
    }
    
    td, p {
        font-family: 'Open Sans', sans-serif;
    }
    
    @page {
        margin: 0.5cm 0.5cm 1cm 0.5cm;
    }

    table {
        width: 100%;
    }

    table.head {
        margin-bottom: 5px;
    }

    table.head img {
        width: 30px;
    }

    table.head td {
        vertical-align: middle;
    }

    table.products tr {
        padding: 0;
    }

    table.products td {
        padding: 5px;
    }

    table.products thead {
        background: #b4b4b4;
        font-size: 10px;
    }

    table.products tbody {
        font-size: 9px;
    }


    table.products tbody {
        border-bottom: 1px solid #000;
    }

    table.products tbody > tr:nth-child(even) {
        background: #cecece;
    }

    table.products tbody tr > td:nth-child(1) {
        width: 70%;
    }

    table.products tbody tr > td:nth-child(2) {
        width: 20%;
        text-align: center;
    }

    table.products tbody tr > td:nth-child(3) {
        width: 10%;
    }

    .store-name {
        font-size: 12px;
        line-height: 10px;
    }

    .store-address {
        font-size: 8px;
        line-height: 8px;
        text-align: right;
    }

    .date {
        text-align: right;
        font-size: 10px;
    }

</style>

<table class="head container" valign="bottom">
    <tr>
        <td class="header" valign="top" colspan="2">
            <img src="<?= $base64 ?>" style="width:100px;"><br>
            <span style="font-weight: normal;font-size:7px;line-height:10px;">1925 Monroe Street, Madison, WI, 53711</span></p>
        </td>
        
        <td class="shop-info">
            <div class="store-address"><?=
                $details['address'] ?? 'Address Required' ?></div>
        </td>
    </tr>
    <tr>
        <td>
            <strong style="font-size: 10px;"><?= $details['invoice_id'] ?? '0000-0000' ?></strong>
        </td>

        <td class="date">
            <?= $details['email'] ?? 'Email Required' ?>
        </td>

        <td class="date">
            October 2021
        </td>
    </tr>
</table>
<?php
$products = $details['products'] ?? [];
if( empty( $products ) ) return '<p>No products bought or allocated</p>';
?>
<table class="products">
    <thead>
    <tr>
        <td>
            <strong>Product</strong>
        </td>
        <td style="text-align: center;">
            <strong>#</strong>
        </td>
    </tr>
    </thead>
    <tbody>
    <?php
    
    $total = 0;
    foreach( $products as $product ) :
    $total = $product['quantity'] + $total;
    ?>
        <tr>
            <td><?= nl2br( \Mythic_Core\Functions\MC_Mythic_Frames_Functions::convert_item_to_readable_name($product)) ?></td>
            <td style="text-align: center;"><?= $product['quantity'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td>Items:
            </td>
            <td style="text-align: center;"><?= $total ?></td>
        </tr>
    </tfoot>
</table>
