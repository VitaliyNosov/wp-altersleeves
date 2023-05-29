<?php

namespace Mythic_Template\Abstracts;


/**
 * Class MT_Data_Getter
 *
 * @package Mythic_Template\Abstracts
 */
abstract class MT_Data_Getter
{
    // @TODO: update $api_base
    public static $api_base = 'https://altersleeves.loc/wp-json/mt_api/v1/';
    // @TODO: decide how we will use API - I don't think we need to do a lot of requests for every getter, so we need to use something like page getter
    public static $api_data_list = [];
    public static $options_data_list = [];
    // @TODO: decide how we will render menus - via standard WP functions or not
    public static $menu_list = [];
    // @TODO: most of data we will take from api, options and nav, but maybe we will store locally something also
    public static $local_data_list = [];

    public $getter_data = [];

    /**
     * Start up
     */
    public function __construct()
    {
        $this->getter_data = [];
        if (!empty(static::$api_data_list)) {
            foreach (static::$api_data_list as $api_data_key => $api_data_single) {
                $this->getter_data[$api_data_key] = static::getApiData($api_data_single);
            }
        }

        if (!empty(static::$options_data_list)) {
            foreach (static::$options_data_list as $options_data_key => $options_data_single) {
                $this->getter_data[$options_data_key] = get_option($options_data_single);
            }
        }
    }

    /**
     * @return array
     */
    public function getData(){
        return $this->getter_data;
    }

    /**
     * @param $url_part
     * @param array $args
     * @return bool
     */
    public static function getApiData($url_part, $args = []){
        $url = static::$api_base . $url_part;

        $request_data = wp_remote_get($url, $args);

        if(empty($request_data['response']['code']) || $request_data['response']['code'] != 200) return false;

        return $request_data['body'];
    }

}
