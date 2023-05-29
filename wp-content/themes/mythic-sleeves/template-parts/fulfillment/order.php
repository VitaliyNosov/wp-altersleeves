<?php

if( !MC_WOO_ACTIVE ) return;

$order_id = $order->get_id();
$dateTime = str_replace( 'T', ' ', $order->get_date_created() );

$firstName = $order->get_shipping_first_name();
if( empty( $firstName ) ) $firstName = $order->get_billing_first_name();
$lastName = $order->get_shipping_last_name() ?? $order->get_billing_last_name();
if( empty( $lastName ) ) $lastName = $order->get_billing_last_name();
$name = $firstName.' '.$lastName;

$numberOfSleeves = 0;
$products        = [];
foreach( $order->get_items() as $itemId => $itemData ) {
    $idProduct = $itemData->get_product_id();
    
    if( !MC_Product_Functions::isAlter( $idProduct ) ) continue;
    $quantity        = $itemData->get_quantity();
    $numberOfSleeves = $numberOfSleeves + $quantity;
    
    $products[] = [
        'product_id' => $idProduct,
        'quantity'   => $quantity,
    ];
}

$labelPrinted = !empty( MC_WP::meta( 'mc_label_printed', $order_id ) );
$country      = $order->get_shipping_country();


$gift_card = false;
$post      = get_post( $order_id );
$post_date = $post->post_date_gmt;
$post_date = strtotime($post_date);

if( !empty($post_date) ) {
    if( ($post_date > 1638162000 && $post_date < 1638248400) && $numberOfSleeves >= 5 ) $gift_card = true;
}
?>
<div class="order2process" id="order_<?= $order_id ?>">
    <?php if( !empty($gift_card) ) : ?>
        <div class="py-2 bg-light">
            <h3 class="text-danger">Add Gift Card for Cyber Monday</h3>
        </div>
    <?php endif; ?>
    <div class="row align-items-start">
        <div class="order2process__ID col-sm-1">
            <strong><a target="_blank" href="<?= '/wp-admin/post.php?post='.$order_id.'&action=edit' ?>">
                    <?= $order_id ?></a> </strong>
        </div>

        <div class="order2process__name col-sm-2"><?= $name ?>
            <?php
            if( !empty( $labelPrinted ) ) : ?>
                <br><span class="text-success">Label printed</span>
            <?php
            else : ?>
                <br><span class="text-danger">Manual Label needed</span>
            <?php
            endif; ?>

        </div>
        <div class="order2process__count col-sm-1"><?= $numberOfSleeves ?></div>
        <div class="order2process__date col-sm-8">
            <strong><?= strtoupper( $order->get_status() ); ?></strong> <br>
            <?= $dateTime ?>
        </div>
        <div class="col-8 offset-4 order2process__references">
            <div class="row align-items-start">
                <div class="order2process__print col-sm-3">
                    <button class="pink--button order2process__print--files" data-order-id="<?= $order_id; ?>">
                        <strong>SEND FILES</strong></button>
                </div>
                <div class="order2process__invoice col-sm-3">
                    
                    <?php
                    $gift    = MC_WP::meta( '_invoice_drop_ship', $order_id );
                    $urlGift = !empty( $gift ) ? '&gift=1' : '&gift=0';
                    
                    if( !empty( $gift ) ) : ?>
                        <a class="order2process__print--invoice" href="#<?= $order_id ?>" data-gift="0">
                            <button class="orange--button"><strong>PRINT INVOICE</strong></button>
                        </a>
                    <?php
                    else : ?>
                        <a class="order2process__print--invoice" href="#<?= $order_id ?>" data-gift="1">
                            <button class="orange--button"><strong>PRINT INVOICE</strong></button>
                        </a>
                    <?php
                    endif; ?>
                    
                    <?php
                    $urlNonce = wp_nonce_url( admin_url( "admin-ajax.php?action=generate_wpo_wcpdf&document_type=invoice&order_ids=".$order_id.$urlGift ),
                                              'generate_wpo_wcpdf' ); ?>
                    <a href="<?= $urlNonce ?>" target="_blank">
                        <button class="orange--button"><strong>VIEW INVOICE</strong></button>
                    </a>
                </div>
                <div class="order2process__remove col-sm-3">
                    <a class="order2process__remove--init" href="#<?= $order_id ?>">
                        <button class="red--button"><strong>HIDE</strong></button>
                    </a>
                </div>
                <?php
                if( $order->get_status() == 'completed' )  : ?>
                    <div class="order2process__reship col-sm-3">
                        <a class="order2process__reship" href="#<?= $order_id ?>" data-order-id="<?= $order_id ?>">
                            <button class="green--button"><strong>RE-SHIP</strong></button>
                        </a>
                    </div>
                <?php
                else : ?>
                    <div class="order2process__complete col-sm-3">
                        <a class="order2process__complete-order" href="#<?= $order_id ?>">
                            <button class="green--button"><strong>COMPLETE</strong></button>
                        </a> <br>
                        <a class="order2process__reship" href="#<?= $order_id ?>" data-order-id="<?= $order_id ?>">
                            <button class="green--button"><strong>UNDO PRINT</strong></button>
                        </a>
                    </div>
                <?php
                endif; ?>
            </div>
            <hr>
            <?php
            foreach( $order->get_items() as $itemId => $itemData ) {
                $idProduct = $itemData->get_product_id();
                if( !MC_Product_Functions::isAlter( $idProduct ) ) continue;
                $idProducts[]    = $idProduct;
                $quantity        = $itemData->get_quantity(); // Get the item quantity
                $numberOfSleeves = $numberOfSleeves + $quantity;
            }
            
            $productList = '<p style="margin:0;"><strong>Design Ids: </strong>';
            $count       = count( $idProducts );
            $i           = 1;
            foreach( $products as $product ) {
                $id = $product['product_id'];
                if( !MC_Product_Functions::isAlter( $id ) ) continue;
                $quantity = $product['quantity'];
                $tag      = ', ';
                if( $count == $i ) $tag = '';
                $productList .= $id.$tag;
                $i++;
            }
            $productList .= '</p>';
            echo $productList;
            ?>
            <button class="blue--button" data-bs-toggle="collapse" data-bs-target="#preview-<?= $order_id ?>">Designs in Order
            </button>

            <div id="preview-<?= $order_id ?>" class="collapse show">
                <div class="row">
                    <?php
                    $productOutput = '';
                    foreach( $products as $product ) {
                        $id = $product['product_id'];
                        if( !MC_Product_Functions::isAlter( $id ) ) continue;
                        $quantity  = $product['quantity'];
                        $fileClass = 'green';
                        if( !file_exists( DIR_PRINTS_PDF.'/'.$id.'.pdf' ) ) {
                            $fileClass = 'red';
                        }
                        $png          = MC_WP::meta( 'mc_lo_res_alter_png', $id );
                        $printing     = MC_Alter_Functions::printingObject( $id );
                        $name_card    = $printing->name;
                        $nameAlterist = MC_Alter_Functions::getAlteristDisplayName( $id );
                        
                        $productOutput .= '<div class="col-sm-3 order2process__design"><div class="order2process__design-background';
                        if( $quantity > 1 ) $productOutput .= ' order2process__design-multiple';
                        $productOutput .= '">';
                        $productOutput .= '<img src="'.$png.'"></div>';
                        $productOutput .= '<input id="product-count-'.$id.'" type="number" value="1"><button data-order-id="'.$order_id.'" data-alter-id="'.$id.'" class="order2process__print--product pink--button"><strong>SEND FILES</strong></button>';
                        if( $order->get_status() == 'completed' ) {
                            $errorUrl      = '/order-errors?order_id='.$order_id.'&product_id='.$id;
                            $productOutput .= '<p><a target="_blank" href="'.$errorUrl.'" style="color:red;">LOG ERROR</a></p>';
                        }
                        $productOutput .= '<p><a target="_blank" href="'.get_the_permalink( $id ).'">'.$name_card.'<br>'.$nameAlterist.'</a></p>';
                        $productOutput .= '<p class="'.$fileClass.'">'.$id.' <strong>x'.$quantity.'</strong></p>';
                        $productOutput .= '</div>';
                    }
                    echo $productOutput;
                    ?>
                </div>
            </div>
            
            <?php Mythic_Core\Ajax\Fulfillment\MC_Complete_Order::render_nonce(); ?>

        </div>
    </div>
</div>