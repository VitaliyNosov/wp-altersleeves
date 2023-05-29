<?php

namespace Mythic_Core\Ajax\Creator\Management;

use Mythic_Core\Abstracts\MC_Ajax;
use Mythic_Core\Functions\MC_User_Functions;

/**
 * Class MC_Charity
 *
 * @package Mythic_Core\Ajax\Creator\Management
 */
class MC_Charity extends MC_Ajax 
{
	/**
	 * @return array
	 */
	public function required_values() : array 
	{
		return [ 'data' ];
	}

	/**
	 * Handles POST request
	 */
	public function execute() 
	{
		// var_dump($_POST['data']); exit;
		$this->success( MC_User_Functions::update_user_charity( $_POST['data'] ) );
	}

	/**
	 * @return string
	 */
	protected static function get_action_name() : string 
	{
		return 'mc_charity';
	}

	/**
	 * @return string
	 */
	protected static function get_nonce_name() : string 
	{
		return 'mc_charity_data';
	}
}