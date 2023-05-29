<?php

namespace Mythic\Objects\System;

use Mythic\Abstracts\MC2_Class;
use Mythic\Helpers\MC2_Functions;

/**
 * The shortcode blueprint. Build all shortcodes from this class. You can add the actions(), ajax(), events() and filters() methods from MC2_Class for efficient script and style enqueuing
 */
class MC2_Shortcode extends MC2_Class {

    private $function;

    /**
     * MC2_Shortcode constructor.
     */
    public function __construct( ...$params ) {
        extract($params);
        if( empty($shortcode) || empty($function) ) return;
        parent::__construct();
        $this->set_function($function);
        add_shortcode( $shortcode, [ $this, 'generate' ] );
        add_shortcode( strtoupper( $shortcode ), [ $this, 'generate' ] );
    }

    /**
     * @param array  $args
     * @param string $content
     *
     * @return string
     * @throws \ReflectionException
     */
    public function generate( array $args = [], string $content = '' ) : string {
        return MC2_Functions::run( $this->get_function(), [ 'args' => $args, 'content' => $content ] );
    }

    /**
     * @return mixed
     */
    public function get_function() {
        return $this->function;
    }

    /**
     * @param mixed $function
     */
    public function set_function( $function ) : void {
        $this->function = $function;
    }

}
