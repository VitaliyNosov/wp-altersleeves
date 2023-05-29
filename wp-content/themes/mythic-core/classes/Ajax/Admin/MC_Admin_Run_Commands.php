<?php

namespace Mythic_Core\Ajax\Admin;

use Mythic_Core\Abstracts\MC_Ajax;
use ReflectionMethod;

/**
 * Class MC_Admin_Run_Commands
 *
 * @package Mythic_Core\Ajax\Admin
 */
class MC_Admin_Run_Commands extends MC_Ajax {
    
    /**
     * @return array
     */
    public function required_values() : array {
        return [ 'function_name' ];
    }
    
    /**
     * @return bool
     */
    protected function is_public() : bool {
        return false;
    }
    
    /**
     * Handles POST request
     */
    public function execute() {
        $result = [ 'status' => 0, 'message' => '', 'data' => '' ];
        
        $function_name      = $_POST['function_name'];
        $function_namespace = !empty( $_POST['function_namespace'] ) ? str_replace( '\\\\', '\\', $_POST['function_namespace'] ) : '';
        $params             = !empty( $_POST['function_parameters'] ) ? explode( ',', $_POST['function_parameters'] ) : [];
        if( !empty( $params ) ) {
            foreach( $params as $param_key => $param ) {
                $params[ $param_key ] = trim( $param );
            }
        }
        
        if( !empty( $function_namespace ) ) {
            if( !class_exists( $function_namespace ) ) {
                $result['message'] = 'Class not found';
                
                $this->success( $result );
            }
            
            if( !method_exists( $function_namespace, $function_name ) ) {
                $result['message'] = 'Method not found';
                
                $this->success( $result );
            }
            
            $ReflectionMethod = new ReflectionMethod( $function_namespace, $function_name );
            if( !$ReflectionMethod->isStatic() ) {
                $result['message'] = 'Method not static';
                
                $this->success( $result );
            }
            
            $data = call_user_func_array( [ $function_namespace, $function_name ], $params );
        } else {
            if( !function_exists( $function_name ) ) {
                $result['message'] = 'Function not found';
                
                $this->success( $result );
            }
            
            $data = call_user_func_array( $function_name, $params );
        }
        
        $result = [ 'status' => 1, 'message' => '', 'data' => $data ];
        
        $this->success( $result );
    }
    
    /**
     * @return string
     */
    protected static function get_action_name() : string {
        return 'mcAdminRunCommands';
    }
    
}
