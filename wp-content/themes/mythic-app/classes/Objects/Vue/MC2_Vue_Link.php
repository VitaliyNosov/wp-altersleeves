<?php

namespace Mythic\Objects\Vue;

use Mythic\Abstracts\MC2_Vue;

class MC2_Vue_Link extends MC2_Vue {
	public static $fields = [
		'url',
		'title',
		'target'
	];


	/**
	 * @param $data
	 * @return array|bool
	 */
	public static function prepareObjectData($data){
		if(empty($data[0])) return false;

		$result = [
			'url' => $data[0]
		];
		$result['title'] = !empty($data[1]) ? $data[1] : $data[0];
		if(!empty($data[2])) $result['_target'] = 'blank';

		return $result;
	}
}
