<div class="possible_tags">
	<h5>Possible tags:</h5>
	<div>{{promotion_code}} {{first_name}} {{last_name}} {{user_login}} {{user_email}} {{user_nicename}}</div>
</div>

<?php

use Mythic_Core\Display\MC_Forms;
use Mythic_Core\Objects\MC_Promo_Mailing;

$form_classes = [ 'mc-send-promo-mailing-form' ];

$fields        = MC_Promo_Mailing::getSendEmailsFields();
$hidden_fields = [
	[
		'mc_nonce' => 'mc_promo_mailing_data',
	],
];

MC_Forms::mcFormRender( $form_classes, $fields, 'Send', $hidden_fields );
?>