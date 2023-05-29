<?php

namespace Mythic\Abstracts;

/**
 * The class blueprint that allows you to add wordpress filters and actions as part of your development flow
 */
abstract class MC2_Class extends MC2_Abstract {
    
    public function __construct() {
        $this->actions();
        $this->ajax();
        $this->filters();
        $this->shortcodes();
    }

    public static function init() {
        new static();
    }

    /**
     * Add this method to class for actions
     */
    public function actions() {
    }

    /**
     * Add this method to class for ajax calls
     */
    public function ajax() {
    }

    /**
     * Add this method to class for filters
     */
    public function filters() {
    }

    /**
     * Add this method to class for ajax calls
     */
    public function shortcodes() {
    }

}