<?php

use Mythic_Core\Ajax\Finance\MC_Export_Statement;
use Mythic_Core\Functions\MC_Transaction_Functions;
use Mythic_Core\Functions\MC_Woo_Order_Functions;

$affiliate_id = wp_get_current_user()->ID;
$default_id = 16;
if( MC_User_Functions::isAdmin() ) {
    $affiliate_id = $_GET['affiliate_id'] ?? $default_id;
    $affiliate_id = $_GET['artist_id'] ?? $affiliate_id;
}
if( empty( $affiliate_id ) ) return;
$items  = MC_Transaction_Functions::getForAffiliateLedger( $affiliate_id, true );
$output = '';
foreach( $items as $item ) {
    $action_id      = $item->action_id;
    $date           = date( 'Y-m-d H:i:s', strtotime( $item->date ) );
    $type           = $item->type;
    $value          = $item->value;
    $store          = $item->site_id == 2 ? 'Mythic Gaming' : 'Alter Sleeves';
    $message        = '';
    $cleared        = '<span class="text-success">Cleared</span>';
    $date_as_string = strtotime( $date );
    if( $type == 'royalty' && $item->site_id == 1 ) {
        $cleared = $date_as_string < ( time() - strtotime( '30 days', 0 ) );
        $cleared = empty( $cleared ) ? '<span class="text-danger">Pending</span>' : '<span class="text-success">Cleared</span>';
    }
    
    switch( $type ) {
        case 'referral_fee' :
            $order_id = $item->order_id;
            $message  = 'Associated with order ';
            if( MC_User_Functions::isAdmin() ) $message .= '<a href="/wp-admin/post.php?post='.$order_id.'&action=edit">';
            $message .= $order_id;
            $type    = 'referral';
            if( MC_User_Functions::isAdmin() ) $message .= '</a>';
            $message .= ' worth $'.MC_Woo_Order_Functions::orderTotal( $order_id, true, false );
            break;
        case 'withdrawal' :
            if( empty( $value ) || $value == 0 ) continue 2;
            $currency = $item->currency;
            $type     = 'withdrawal';
            $message  = 'Withdrawal of $'.$value.' in "'.$currency.'"';
            $value    = -$value;
            break;
        case 'contracted_fee' :
            $type    = 'partner fee';
            $message = 'Recurring fee of $'.$value;
            break;
        case 'royalty' :
            $type       = empty( $value ) ? 'promotional' : 'royalty';
            $product_id = $item->product_id;
            if( empty( $product_id ) ) {
                $message = $item->message;
            } else {
                $message = 'Royalty for sale of product <a href="'.get_blog_permalink( $item->site_id, $product_id ).'">'.$item->product_id.'</a>';
            }
            if( empty( $value ) ) {
                $message = str_replace( 'Royalty', 'Promotional', $message );
                $message .= '<br><small>Your Alter Sleeve has been used in marketing promote the platform and your work</small>';
            }
            break;
    }
    
    ob_start(); ?>
    <tr>

        <td><?= $date ?></td>
        <td><span class="text-<?= empty($value) ? 'danger' : 'success' ?>"><?= $value ?></span></td>
        <td><?= $type ?></td>
        <td class="d-none d-sm-table-cell"><?= $message ?></td>
        <td><?= $store ?></td>
        <td><?= $cleared ?></td>
    </tr>
    <?php
    $output .= ob_get_clean();
}

if( empty( $output ) ) {
    echo 'You have had no activity yet on Alter Sleeves. Remember to share and spread the word!';
}
?>
<h2>Financial Statement</h2>
<p>Here you can see an itemised statement of financial activity associated with your account. The ledger only shows the last 60 days. For a full
    report, please click the export button:</p>
<a class="btn btn-success" href="javascript:void(0)" data-user-id="<?= $affiliate_id ?>" id="export-statement">Export Records</a>
<?php
if( !empty( $balance_formatted ) ) : ?>
    <h5 class="my-2"><strong>Current Balance:</strong> $<?= $balance_formatted ?></h5>
<?php endif; ?>

<table class="mt-0 table ledger-table">
    <thead>
    <tr>
        <th scope="col">Date</th>
        <th scope="col">Value ($)</th>
        <th scope="col">Type</th>
        <th scope="col" class="d-none d-sm-table-cell">Message</th>
        <th scope="col" class="d-none d-sm-table-cell">Store</th>
        <th scope="col" class="d-none d-sm-table-cell">Status</th>
    </tr>
    </thead>
    <tbody>
    <?= $output ?>
    </tbody>
</table>
<a class="button" href="javascript:void(0)" data-user-id="<?= $affiliate_id ?>" id="export-statement">Click here to download the full report</a>
<?php MC_Export_Statement::render_nonce() ?>
<style>
    .ledger-table {
        font-size: 12px;
    }

    .ledger-table * {
        padding: 0.25rem !important;
    }
</style>