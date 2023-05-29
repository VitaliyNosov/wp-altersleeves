<?php

namespace Mythic\Objects\Mtg;

use Mythic\Abstracts\MC2_Object;

class MC2_Set extends MC2_Object {

    protected static $table_name = 'mtg_sets';
    protected static $primary_key = 'set';

    public $set;
    public $name;
    public $searchable_name;
    public $released_at;
    public $icon;

    public function allocate_values( $data ) {
        parent::allocate_values( $data );
        $this->set_icon( '<i class="ss ss-'.$this->get_set().'"></i>' );
    }

    /**
     * @return mixed
     */
    public function get_set() {
        return $this->set;
    }

    /**
     * @param mixed $set
     */
    public function set_set( $set ) {
        $this->set = $set;
    }

    /**
     * @return mixed
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function set_name( $name ) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function get_searchable_name() {
        return $this->searchable_name;
    }

    /**
     * @param mixed $searchable_name
     */
    public function set_searchable_name( $searchable_name ) {
        $this->searchable_name = $searchable_name;
    }

    /**
     * @return mixed
     */
    public function get_released_at() {
        return $this->released_at;
    }

    /**
     * @param mixed $released_at
     */
    public function set_released_at( $released_at ) {
        $this->released_at = $released_at;
    }

    /**
     * @return mixed
     */
    public function get_icon() {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function set_icon( $icon ) {
        $this->icon = $icon;
    }

}