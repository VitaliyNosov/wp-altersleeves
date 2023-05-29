<?php

namespace Mythic_Core\Ajax\PromoMailing;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Objects\MC_Promo_Mailing;

/**
 * Class MC_Promo_Mailing_Send
 *
 * @package Mythic_Core\Ajax\PromoMailing
 */
class MC_Promo_Mailing_Send extends MC_Ajax {

	/**
	 * @return array
	 */
	public function required_values() : array {
		return [ 'data' ];
	}

	/**
	 * Handles POST request
	 */
	public function execute() {
		$this->success( MC_Promo_Mailing::send( $_POST['data'] ) );
	}

	/**
	 * @return string
	 */
	protected static function get_action_name() : string {
		return 'mc_promo_mailing_send';
	}

	/**
	 * @return string
	 */
	protected static function get_nonce_name() : string {
		return 'mc_promo_mailing_data';
	}

}
