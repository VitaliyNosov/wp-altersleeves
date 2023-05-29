<?php

namespace Mythic_Core\Ajax\Creator\Management;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_User_Functions;

/**
 * Class MC_Promo_Mailing_Send
 *
 * @package Mythic_Core\Ajax\PromoMailing
 */
class MC_Sale_Agreement extends MC_Ajax {

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
		$sale_category = get_term_by( 'name','sale','product_cat' );

		if(!$sale_category) {
			$this->error( 'Sale category not found' );
		} else {
			$this->success( MC_User_Functions::update_user_sale( 'sale_agreement', $_POST['data']['sale_agreement'], $sale_category ) );
		}
	}

	/**
	 * @return string
	 */
	protected static function get_action_name() : string {
		return 'mc_sale_agreement';
	}

	/**
	 * @return string
	 */
	protected static function get_nonce_name() : string {
		return 'mc_sale_agreement_data';
	}

}