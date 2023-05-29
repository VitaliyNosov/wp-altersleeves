<?php

namespace Mythic_Template\Abstracts;

/**
 * Class MT_API
 *
 * @package Mythic_Template\Abstracts
 */
abstract class MT_API {
    public $api_namespace = 'mt-api/v1/';
    public $api_key = 'U9EPMRWJ65BYSAQY66B3ZRW6';
    public $list_of_api_routes = [];

    /**
     * MT_API constructor.
     */
    public function __construct()
    {
        if (empty($this->list_of_api_routes)) return;

        $this->registerRestApi();
    }

    /**
     * Add routes registration action
     */
    public function registerRestApi()
    {
        add_action('rest_api_init', array($this, 'mtRegisterRoutes'));
    }

    /**
     * Register API routes
     */
    public function mtRegisterRoutes()
    {
        foreach ($this->list_of_api_routes as $route) {
            if (empty($route[0]) || empty($route[1]) || empty($route[2])) continue;

            register_rest_route($this->api_namespace, $route[0], [
                [
                    'methods' => $route[1],
                    'callback' => [$this, $route[2]],
                    'permission_callback' => [$this, !empty($route[3]) ? $route[3] : 'mtPermissionCallback'],
                ]
            ]);
        }
    }

    /**
     * @param $request
     * @return bool
     */
    public function mtPermissionCallback($request)
    {
        // @TODO: decide and configure API key using
        $request_headers = $request->get_headers();

        return true;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getBodyParams($request)
    {
        return json_decode($request->get_body());
    }
}
