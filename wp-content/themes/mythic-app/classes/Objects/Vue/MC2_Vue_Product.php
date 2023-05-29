<?php

namespace Mythic\Objects\Vue;

use Mythic\Abstracts\MC2_Vue;

class MC2_Vue_Product extends MC2_Vue {
	public static $fields = [
		'id',
		'title',
		'url',
		'image',
		'price'
	];

	public static function prepareObjectData($data){
		if(empty($data)) return false;

		if(!is_object($data)) $data = wc_get_product($data);

		if(empty($data)) return false;

		return [
			'id' => $data->get_id(),
			'title' => $data->get_title(),
			'url' => $data->get_permalink(),
			'image' => $data->get_image(),
			'price' => $data->get_price(),
		];
	}

}
