<?php

use Mythic_Core\Display\MC_Tabs_With_Panes;

$tabs_info = [
	'send_emails' => [
		'title' => 'Send emails',
		'content' => ['slug' => 'creator/promo-mailing/parts/promo-mailing-control', 'name' => 'send'],
		'active' => 1,
	],
	'add_new_list' => [
		'title' => 'Add new listing',
		'content' => ['slug' => 'creator/promo-mailing/parts/promo-mailing-control', 'name' => 'add'],
		'active' => 0,
	]
];
?>

<div class="promo_mailing_wrapper">
<?php

	MC_Tabs_With_Panes::mcTabsRender($tabs_info, ['args' => []]);

?>

</div>