<?php

if(
    empty( $giftcard_id ) ||
    empty( $order_id )
) {
    return;
}

$order = wc_get_order( $order_id );
if( empty( $order ) ) return;
$order_name = $order->get_shipping_first_name();

$giftcard_code = strtoupper( trim( get_the_title( $giftcard_id ) ) );
$sleeves       = get_post_meta( $giftcard_id, 'as_sleeves', true );
if( !empty( $sleeves ) ) $sleeves = $sleeves == 1 ? '1 Alter Sleeve' : $sleeves.' Alter Sleeves';

$path   = DIR_THEME_IMAGES.'/logo/dark.png';
$type   = pathinfo( $path, PATHINFO_EXTENSION );
$data   = file_get_contents( $path );
$base64 = 'data:image/'.$type.';base64,'.base64_encode( $data );
?>

<!DOCTYPE html>
<html>
<head>
    <title>PDF Create</title>
    <style type="text/css">
        * {
            font-family: Helvetica, Arial, sans-serif;
        }

        h1, h2, .blue {
            color: #2C9AC2;
        }

        .center {
            text-align: center;
        }


        th, td {
            border: solid 1px #777;
            padding: 2px;
            margin: 2px;
        }
    </style>
</head>
<body>
<div class="center">
    <h1>Congratulations</h1>
    <img src="<?= $base64 ?>"><br>
    <h2>You've received an
        Alter Sleeves Gift Card</h2>

    <h3>Your code:<br><span class="blue"><?= $giftcard_code ?></span></h3>

    <div class="center" style="width:600px;margin:0 auto;"><p>This can be redeemed at <strong>www.altersleeves.com</strong> at checkout.</p></div>
    
    <?php if( !empty( $sleeves ) ) : ?>
        <h4 class="center">Remaining Value: <?= $sleeves ?></h4>
    <?php endif; ?>

    <br><br><br><br>
    <p><small>Terms and conditions apply. Redeem 5 or more sleeves at one time to receive free untracked shipping</small>. Additional shipping charges
        may apply.</p>
</div>
</body>
</html>