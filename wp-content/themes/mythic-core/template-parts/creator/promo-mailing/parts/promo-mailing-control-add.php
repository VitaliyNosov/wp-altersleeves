<?php

use Mythic_Core\Display\MC_Forms;
use Mythic_Core\Objects\MC_Promo_Mailing;

$form_classes = [ 'mc-add-promo-mailing-form' ];

$fields        = MC_Promo_Mailing::getAddEmailsFields();
$hidden_fields = [
	[
		'mc_nonce' => 'mc_promo_mailing_data',
	],
];

MC_Forms::mcFormRender( $form_classes, $fields, 'Add', $hidden_fields );
?>