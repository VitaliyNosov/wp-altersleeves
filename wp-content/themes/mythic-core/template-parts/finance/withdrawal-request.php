<?php

use Mythic_Core\Functions\MC_Creator_Functions;
use Mythic_Core\Utils\MC_Wise;

if( !is_user_logged_in() ) return;

$user_id = MC_Creator_Functions::get_current_creator_id();
$user    = get_user_by( 'ID', $user_id );
$balance = MC_Creator_Functions::getAffiliateBalance($user_id);
$totalEarned = MC_Creator_Functions::getEarnedByUser( $user_id );
$totalWithdrawn = MC_Withdrawal_Functions::getTotalWithdrawn( $user_id );
$withdrawals    = MC_Withdrawal_Functions::getByCreatorId( $user_id );

$currencies = MC_Wise::availableCurrencies();

$name = get_user_meta( $user_id, 'mc_transferwise_name', true );
if( empty( $name ) ) $name = $user->first_name.' '.$user->last_name;
$email = get_user_meta( $user_id, 'mc_transferwise_email', true );
if( empty( $email ) ) $email = $user->user_email;
$preferredCurrency = get_user_meta( $user_id, 'mc_transferwise_currency', true );
if( empty( $preferredCurrency ) ) $preferredCurrency = 'USD';

?>
    <h3>Request a Withdrawal</h3>

    <p>Well done on selling Mythic Gaming products. Please find below your current balance and withdrawal history: current balance is calculated from your
        available <a href="/royalty-report">royalties</a>.</p>
    <ul style="list-style: none;padding:0;margin:1rem 0;">
        <li><strong>Lifetime earnings:</strong> $<?= $totalEarned ?></li>
        <li><strong>Total Withdrawn:</strong> $<?= $totalWithdrawn ?></li>
        <li><strong>Available Balance:</strong> $<?= $balance ?></li>
    </ul>

<?php if( !empty($balance = \Mythic_Core\Functions\MC_Creator_Functions::getAffiliateBalance()) ) :?>
    <p>You can use your available balance to pay for any Mythic Gaming or Alter Sleeves orders. <br><a href="/coupon/accountcredit/">Click to pay for cart purchase using account balance</a> </p>
<?php endif;  ?>

    <p><strong>Please note:</strong> all withdrawals are done via
        <a href="https://wise.com/" target="_blank">Wise</a>; you must have an account to withdraw. If you have a circumstance that does not allow you to do this, then please email support@mythicgaming.com</p>

<?php if( $balance > 0 ) : ?>
    <div id="form-withdrawal">

        <div class="mb-3">
            <label for="input-transferwise-name">Name</label>
            <input type="text" class="form-control" id="input-transferwise-name" aria-describedby="nameHelp" placeholder="Enter Name"
                   value="<?= $name ?>" required>
            <small id="nameHelp" class="form-text text-muted">Your name on Wise</small>
        </div>

        <div class="mb-3">
            <label for="input-transferwise-email">Email address</label>
            <input type="email" class="form-control" id="input-transferwise-email" aria-describedby="emailHelp" placeholder="Enter email"
                   value="<?= $email ?>" required>
            <small id="emailHelp" class="form-text text-muted">This must match your Wise account</small>
        </div>

        <div class="mb-3">
            <label for="input-withdraw-amount">Amount to withdraw</label>
            <input type="number" class="form-control" id="input-withdraw-amount" aria-describedby="amountHelp" placeholder="Enter amount to withdraw"
                   value="0.00" step=".01" required>
            <small id="amountHelp" class="form-text text-muted">You will not be able to withdraw more than your available amount (in $)</small>
        </div>

        <div class="mb-3">
            <label for="exampleFormControlSelect1">Preferred Currency</label>
            <select class="form-control" id="input-transferwise-currency">
                <?php foreach( $currencies as $code => $currency ) :
                    ?>
                    <option value="<?= $code ?>" <?= $code == $preferredCurrency ? 'selected' : '' ?>><?= $currency ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button id="action-request-withdrawal" class="btn blue--button">Request Withdrawal</button>
        <div id="withdrawal-error" class="text-danger font-weight-bold" style="display:none;">There has been an error: ensure you have sufficient
            funds and have filled in all fields
        </div>
    </div>
<?php else : ?>
    <p>Unfortunately you do not have a balance to currently withdraw</p>
<?php endif;

if( empty($withdrawals) ) return;
?>


<h3>Withdrawal History</h3>
<table class="table w-auto mx-0 my-3">
    <thead>
    <tr>
        <th scope="col">
            Date
        </th>
        <th scope="col">
            Amount ($)
        </td>
        <th scope="col">
            Method
        </td>
    </tr>
    </thead>
    <tbody>
    <?php foreach( $withdrawals as $withdrawal ) : ?>
        <tr>
            <td>
                <?php $date = $withdrawal->date;
                $createDate = new DateTime( $date );
                $strip      = $createDate->format( 'Y-m-d' );
                echo $strip;
                
                ?>
            </td>
            <td>
                <?= $withdrawal->paid_init; ?>
            </td>
            <td>
                <?= ucfirst( $withdrawal->source ); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
