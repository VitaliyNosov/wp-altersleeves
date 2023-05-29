<?php

namespace Mythic_Template\API;

use Mythic_Template\Abstracts\MT_API;

class MT_API_Global extends MT_API {
	public $list_of_api_routes = [
		'get_header_data' => [
			'global/header',
			'GET',
			'getHeaderData'
		]
	];

	public function getHeaderData(){
		return [];
	}

}
