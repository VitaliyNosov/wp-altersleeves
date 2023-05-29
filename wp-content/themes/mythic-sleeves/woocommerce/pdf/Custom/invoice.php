<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Functions\MC_Product_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;
use Mythic_Core\System\MC_WP;

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$priceArgs         = [ 'currency' => $order->get_currency() ];
$billing_postcode  = $order->get_billing_postcode();
$shipping_postcode = $order->get_shipping_postcode();

$is_gift = false;
if( !empty( $billing_postcode ) && !empty( $shipping_postcode ) ) {
    if( trim( strtolower( ( $billing_postcode ) ) != trim( strtolower( $shipping_postcode ) ) ) ) $is_gift = true;
}

?>
<?php
do_action( 'wpo_wcpdf_before_document', $this->type, $this->order ); ?>
<style>
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
        padding: 0 0 0 2px;
    }

    table.products thead {
        background: #b4b4b4;
        font-size: 8px;
    }

    table.products tbody {
        font-size: 7px;
        border-bottom: 1px solid #000;
    }

    table.products tbody > tr:nth-child(even) {
        background: #cecece;
    }

    table.products tbody tr > td:nth-child(1) {
        width: 65%;
    }

    table.products tbody tr > td:nth-child(2) {
        width: 18%;
        text-align: center;
    }

    table.products tbody tr > td:nth-child(3) {
        width: 17%;
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
        font-size: 8px;
    }

    .totals {
        width: 100%;
        font-size: 10px;
    }

</style>
<?php
/**
 * ?>
 * <div style="position: fixed; bottom: 0; left: 0;font-size: 7px;">
 * <div style="font-size:9px;text-align:center;margin:0;">Page {{PAGE_NUM}} of {{PAGE_COUNT}}</div>
 * </div>
 */

?>
<table class="head container">
    <tr>
        <td class="header" valign="top" style="width:40px;">
            <img src="https://www.altersleeves.com/wp-content/uploads/2019/09/invoice-black.png" style="width:40px;"><br></p>
        </td>
        <td><p class="store-name">Mythic Gaming<br>
                <span style="font-weight: normal;font-size:7px;line-height:6px;">1925 Monroe Street, Madison, WI, 53711, USA</span></p></td>
        <td class="shop-info">
            <div class="store-address"><?php
                $this->shipping_address(); ?></div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <p><strong>Receipt - Order <?= $this->get_order_number(); ?></strong></p>
        </td>
        <?php
        $creatorItems  = 0;
        $quantityTotal = 0;
        $items         = $this->get_order_items();
        if( sizeof( $items ) > 0 ) : foreach( $items as $item_id => $item ) :
            $idProduct = $item['product_id'];
            
            if( $idProduct != 0 ) {
                $_product = wc_get_product( $idProduct );
                if( $_product->get_type() == 'composite' ) continue;
            }
            
            $quantityTotal = $quantityTotal + $item['quantity'];
        endforeach;
        endif;
        ?>
        <td class="date">
            <p><?= $this->order_date(); ?></p>
        </td>
    </tr>
</table>

<?php
$preorder    = false;
$hasPreorder = false;
if( strpos( $this->get_payment_method(), 'oo' ) !== false ) : ?>

    <table class="products">
        <thead>
        <tr>
            <td>
                <strong>Product</strong>
            </td>
            <td align="right" style="text-align:right;padding-right:5px;">
                <strong><?= $quantityTotal ?></strong>
            </td>
        </tr>
        </thead>
        <tbody>
        <?php
        $designsInSet = 0;
        $count        = 0;
        $items        = $this->get_order_items();
        if( sizeof( $items ) > 0 ) : foreach( $items as $item_id => $item ) : ?><?php
            $idProduct = $item['product_id'];
            $_product  = wc_get_product( $idProduct );
            if( empty( $_product ) ) continue;
            $nameAlterist = MC_Alter_Functions::getAlteristDisplayName( $idProduct );
            
            if( MC_Product_Functions::isGiftCard( $idProduct ) ) {
                $name       = get_the_title( $idProduct );
                $productRow = '<p>'.$name.'</p>';
            } else {
                $count++;
                $col         = 1;
                $printing_id = MC_Alter_Functions::printing( $idProduct );
                $printing    = new MC_Mtg_Printing( $printing_id );
                $name_card   = $printing->name;
                $setName     = $printing->set_name;
                $productName = MC_Alter_Functions::nameOrder( $idProduct, $item_id );
                if( $count <= $designsInSet ) {
                    $productRow = '<p><em>Set Item '.$count.': </em>'.$productName.'<br>'.$_product->get_sku().'</p>';
                } else {
                    $productRow = '<p>'.$productName;
                    $productRow .= '</p>';
                }
                $stock = $item['quantity'];
            }
            ?>
            <tr class="<?= apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $this->type, $this->order, $item_id ); ?>">
                <td class="product" colspan="<?= $col ?>">
                    <?= $productRow ?>
                </td>
                <?php
                if( $col == 1 ) : ?>
                    <td class="quantity" align="right" style="text-align:right;padding-right:5px;"><?= $item['quantity']; ?></td>
                <?php
                endif; ?>
            </tr>
        <?php
        endforeach; endif; ?>
        </tbody>
    </table>
<?php
else : ?>
    <table class="products" style="width:100%;">
        <thead>
        <tr>
            <td>
                <strong>Product</strong>
            </td>
            <?php
            if( !$is_gift ) : ?>
                <td style="text-align:center;">
                    <strong>Price</strong>
                </td>
            <?php
            endif; ?>
            <td align="right" style="text-align:right;padding-right:5px;">
                <strong>Items: <?= $quantityTotal ?></strong>
            </td>
        </tr>
        </thead>
        <tbody>
        <?php
        $designsInSet = 0;
        $count        = 0;
        $order        = wc_get_order( $this->get_order_number() );
        $idCustomer   = $order->get_user_id();
        
        $items = $this->get_order_items();
        if( sizeof( $items ) > 0 ) : foreach( $items as $item_id => $item ) : ?><?php
            $idProduct = $item['product_id'];
            $idCreator = MC_WP::authorId( $idProduct );
            if( $idCreator == $idCustomer ) {
                $creatorItems = $creatorItems + $item['quantity'];
            }
            
            if( $idProduct != 0 ) {
                $_product = wc_get_product( $idProduct );
                if( empty( $_product ) ) continue;
                $nameAlterist = MC_Alter_Functions::getAlteristDisplayName( $idProduct );
                
                $count++;
                $col         = 1;
                $printing_id = MC_Alter_Functions::printing( $idProduct );
                $printing    = new MC_Mtg_Printing( $printing_id );
                $name_card   = $printing->name;
                $setName     = $printing->set_name;
                $productName = MC_Alter_Functions::nameOrder( $idProduct, $item_id );
                if( $count <= $designsInSet ) {
                    $productRow = '<p><em>Set Item '.$count.': </em>'.$productName.'</p>';
                } else {
                    if( MC_Product_Functions::isGiftCard( $idProduct ) ) {
                        $name       = get_the_title( $idProduct );
                        $productRow = '<p>'.$name.'</p>';
                    } else {
                        //$productRow = '<p>'.$productName.'<br> by <strong>'.$nameAlterist.'</strong></p>';
                        
                        $productRow = '<p>'.$productName;
                        $productRow .= '</p>';
                    }
                }
                $stock = $item['quantity'];
                
                ?>
                <tr style="font-size:7px" class="<?= apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $this->type, $this->order, $item_id ); ?>">
                    <td class="product">
                        <?= $productRow ?>
                    </td>
                    <?php
                    if( !$is_gift ) : ?>
                        <td class="product">
                            <?= $item['order_price'] ?>
                        </td>
                    <?php
                    endif; ?>
                    <td class="quantity" align="right" style="text-align:right;padding-right:5px;"><?= $item['quantity']; ?></td>
                </tr>
                <?php
            } else {
                ?>
                <tr class="<?= apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $this->type, $this->order, $item_id ); ?>">
                    <td class="product">
                        <?= $item['name'] ?>
                    </td>
                    <?php
                    if( !$is_gift ) : ?>
                        <td class="product">
                            <?= $item['order_price'] ?>
                        </td>
                    <?php
                    endif; ?>
                    <td class="quantity" align="right" style="text-align:right;padding-right:5px;"><?= $item['quantity']; ?></td>
                </tr>
                <?php
            }
        
        endforeach; endif; ?>
        </tbody>
    </table>
    <?php
    
    if( !$is_gift ) : ?>
        <table style="margin-left:0;margin-top:-15px;">
            <tr>
                <td class="no-borders" colspan="2">
                    <table class="totals">
                        <tfoot>
                        <?php
                        
                        foreach( $this->get_woocommerce_totals() as $key => $total ) :
                            if( trim( strtolower( $total['value'] ) ) == 'flat rate' ) continue;
                            $coupons = $order->get_coupon_codes();
                            ?>
                            <tr class="<?php
                            echo $key; ?>">
                                <td class="no-borders"></td>
                                <th class="description"><?php
                                    echo $total['label']; ?></th>
                                <td class="price"><span class="totals-price"><?php
                                        echo $total['value']; ?></span></td>
                            </tr>
                            
                            <?php if( $key == 'cart_subtotal' && !empty( $coupons ) ) : ?>
                            <tr class="coupons">
                                <td class="no-borders"></td>
                                <th class="description">Coupons</th>
                                <td class="price"><span class="totals-price"><?=
                                        implode( ', ', $coupons ); ?></span></td>
                            </tr>
                        <?php endif; ?>
                        <?php
                        endforeach; ?>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    
    <?php
    endif;
endif;

?>
