<?php

namespace Mythic_Core\Abstracts;

/**
 * Class MC_Shortcode
 *
 * @package Mythic_Core\Abstracts
 */
abstract class MC_Shortcode {
    
    /**
     * MC_Shortcode constructor.
     */
    public function __construct() {
        add_shortcode( $this->getShortcode(), [ $this, 'parseShortcode' ] );
        add_shortcode( strtoupper( $this->getShortcode() ), [ $this, 'parseShortcode' ] );
    }
    
    /**
     * Returns the shortcode slug
     *
     * @return string
     */
    abstract public function getShortcode() : string;
    
    /**
     * Parses the shortcode
     *
     * @param array  $args
     * @param string $content
     *
     * @return string
     */
    public function parseShortcode( $args = [], $content = '' ) : string {
        return $this->generate( $this->parse_args( $args ), $content );
    }
    
    /**
     * Generates the shortcode output
     *
     * @param array  $args
     * @param string $content
     *
     * @return string
     */
    abstract public function generate( $args = [], $content = '' ) : string;
    
    /**
     * Parses the arguments
     *
     * @param array $args
     *
     * @return array
     */
    protected function parse_args( $args = [] ) : array {
        if( !is_array( $args ) ) $args = [];
        $defaults = $this->default_args();
        if( empty( $defaults ) ) return $args;
        
        return shortcode_atts( $defaults, $args, $this->getShortcode() );
    }
    
    /**
     * Lists the default arguments
     *
     * @return array
     */
    protected function default_args() : array {
        return [];
    }
    
}
