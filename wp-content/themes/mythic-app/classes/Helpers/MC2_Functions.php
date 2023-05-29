<?php

namespace Mythic\Helpers;

use ReflectionFunction;
use ReflectionMethod;

class MC2_Functions {

    /**
     * @param string $function_namespace
     * @param string $function_name
     *
     * @return int
     * @throws \ReflectionException
     */
    public static function param_count( string $function_namespace = '', string $function_name = '' ) : int {
        if( empty( $function_namespace ) ) return 0;
        if( empty( $function = $function_namespace ) ) {
            $fct = new ReflectionFunction( $function );
            if( !function_exists( $function ) ) return 0;
            return $fct->getNumberOfParameters() ?? 0;
        } else {
            $method = new ReflectionMethod( $function_namespace, $function_name );
            return $method->getNumberOfParameters() ?? 0;
        }
    }

    /**
     * @param string $function_namespace
     * @param string $function_name
     *
     * @return bool
     * @throws \ReflectionException
     */
    public static function static( string $function_namespace = '', string $function_name = '' ) : bool {
        if( empty( $function_namespace ) || empty( $function_name ) ) return false;
        $ReflectionMethod = new ReflectionMethod( $function_namespace, $function_name );
        return !$ReflectionMethod->isStatic();
    }

    /**
     * @param object $object
     * @param string $method
     * @param mixed  $params
     *
     * @return mixed
     */
    public static function invoke( object $object, string $method, $params = null ) {
        if( is_null( $params ) ) return $object->$method();
        return $object->$method( extract( $params ) );
    }

    /**
     * @param string|array $function
     * @param array        $params
     *
     * @return false|mixed|string|null
     * @throws \ReflectionException
     */
    public static function run( $function = '', array $params = [] ) {
        switch( $function ) {
            case is_string( $function ) :
                if( !function_exists( $function ) ) {
                    return 'Function not found';
                }
                return call_user_func_array( $function, $params );
            case is_array( $function ) :
                if( empty( $function ) || empty( $function[2] ) ) return null;
                $function_name      = $function['function'] ?? $function[1];
                $function_namespace = $function['namespace'] ?? $function[0];
                if( !class_exists( $function_namespace ) ) return 'Class not found';
                if( !method_exists( $function_namespace, $function_name ) ) return 'Method not found';
                $static = self::static( $function_namespace, $function_name );
                if( $static ) {
                    $param_count = self::param_count( $function_namespace, $function_name );
                    $params      = empty( $param_count ) ? null : $params;
                    return self::invoke( new $function_namespace(), $function_name, $params );
                } else {
                    return call_user_func_array( [ $function_namespace, $function_name ], $params );
                }
            default :
                return null;
        }
    }

}