<?php

namespace Mythic_Core\Ajax\PromoMailing;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Objects\MC_Promo_Mailing;

/**
 * Class MC_Promo_Mailing_Add
 *
 * @package Mythic_Core\Ajax\PromoMailing
 */
class MC_Promo_Mailing_Add extends MC_Ajax {

	/**
	 * @return array
	 */
	public function required_values(): array {
		return [ 'name' ];
	}

	/**
	 * Handles POST request
	 */
	public function execute() {
		$data = [
			'name'     => $_REQUEST['name'],
			'csv_file' => $_FILES['csv_file']
		];

		$this->success( MC_Promo_Mailing::add( $data ) );
	}

	/**
	 * @return string
	 */
	protected static function get_action_name(): string {
		return 'mc_promo_mailing_add';
	}

	/**
	 * @return string
	 */
	protected static function get_nonce_name(): string {
		return 'mc_promo_mailing_data';
	}

}
