<?php

namespace Mythic_Core\Settings;

use Mythic_Core\Abstracts\MC_Settings_Page;
use Mythic_Core\System\MC_Settings;

/**
 * Class MC_Run_Commands
 *
 * @package Mythic_Core\Configuration
 */
class MC_Run_Commands extends MC_Settings_Page {
    
    public static $setting_group = 'mythic_run_commands';
    public static $setting_name = 'mythic_run_commands';
    public static $menu_slug = 'admin-mythic-core-run-commands';
    public static $title = 'Mythic Core Run Commands';
    public $setting_list = [];
    
    /**
     * @return array
     */
    public function settingsList() : array {
        return [
            'site' => [
                'title'  => 'Run Commands',
                'fields' => [
                    'function_namespace'  => __( 'Write full namespace with class name if it\'s a static class method or leave it empty if it\'s simple function',
                                                 MC_TEXT_DOMAIN ),
                    'function_name'       => __( 'Function name', MC_TEXT_DOMAIN ),
                    'function_parameters' => __( 'Function parameters (comma separated)', MC_TEXT_DOMAIN ),
                ],
            ],
        ];
    }
    
    /**
     * Add Namespace field
     */
    public function function_namespace_callback() {
        MC_Settings::input( [
                                'input_name'  => 'function_namespace',
                                'option_name' => self::$setting_name,
                                'placeholder' => 'Namespace',
                                'type'        => 'text',
                            ] );
    }
    
    /**
     * Add Function field
     */
    public function function_name_callback() {
        MC_Settings::input( [
                                'input_name'  => 'function_name',
                                'option_name' => self::$setting_name,
                                'placeholder' => 'Function name',
                                'type'        => 'text',
                            ] );
    }
    
    /**
     * Add Parameters field
     */
    public function function_parameters_callback() {
        MC_Settings::input( [
                                'input_name'  => 'function_parameters',
                                'option_name' => self::$setting_name,
                                'placeholder' => 'Parameters',
                                'type'        => 'text',
                            ] );
    }
    
}
