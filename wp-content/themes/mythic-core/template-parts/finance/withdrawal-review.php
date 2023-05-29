<?php

use Mythic_Core\Ajax\Reporting\MC_Royalties_Csv;
use Mythic_Core\Ajax\Reporting\MC_Withdrawals_Csv;
use Mythic_Core\Functions\MC_Withdrawal_Functions;

$withdrawals = MC_Withdrawal_Functions::getAllOrderByDate();

$output = '';
foreach( $withdrawals as $withdrawal ) {
    $user_id         = $withdrawal->creator_id;
    $user            = new WP_User( $user_id );
    $user_name       = $user->display_name;
    $checked         = !empty( $withdrawal->approved ) ? 'checked' : '';
    $email           = get_user_meta( $user_id, 'mc_transferwise_email', true );
    $email           = !empty( $email ) ? $email : $user->user_email;
    $withdrawal_name = get_user_meta( $user_id, 'mc_transferwise_name', true );
    $withdrawal_name = !empty( $withdrawal_name ) ? $withdrawal_name : $user->first_name.' '.$user->last_name;
    $metas           = [
        'date'            => date( 'Y-m-d H:i:s', strtotime( $withdrawal->date ) ),
        'email'           => $email,
        'withdrawal_name' => $withdrawal_name,
        'amount'          => $withdrawal->paid_init,
        'currency'        => $withdrawal->currency,
        'approved'        =>
            '<input class="form-check-input withdrawal-approved m-0" type="checkbox" id="withdrawal-approval-'.$withdrawal->id.'" value="'.$withdrawal->id.'" 
            '.$checked.'>',
    ];
    $cells           = '';
    foreach( $metas as $key => $meta ) {
        if( $key == 'withdrawal_name' ) $meta = '<a href="https://www.altersleeves.com/dashboard/withdrawal-request?artist_id='.$user_id.'">'.$meta.'</a>';
        $cells .= '<td>'.$meta.'</td>';
    }
    $output .= '<tr>'.$cells.'</tr>';
}
?>

<div class="row justify-content-center my-3">
    <div class="col-auto">
        <button class="cas-button cas-button--red" id="withdrawals-csv">Download Withdrawals</button>
        <?php MC_Withdrawals_Csv::render_nonce(); ?>
    </div>

    <div class="col-auto">
        <button class="cas-button cas-button--green" id="royalties-csv">Download Royalties</button>
        <?php MC_Royalties_Csv::render_nonce(); ?>
    </div>
</div>

<table class="table table-striped text-small mt-0">
    <thead>
    <tr>
        <th>Date</th>
        <th>Email</th>
        <th>Withdrawal Name</th>
        <th>Amount</th>
        <th>Currency</th>
        <th>Approved</th>
    </tr>
    </thead>
    <tbody>
    <?= $output ?>
    </tbody>
</table>
