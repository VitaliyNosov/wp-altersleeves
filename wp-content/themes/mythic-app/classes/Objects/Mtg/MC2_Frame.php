<?php

namespace Mythic\Objects\Mtg;

use Mythic\Abstracts\MC2_Object;

class MC2_Frame extends MC2_Object {

    protected static $table_name = 'mtg_frames';

    public $framecode;
    public $attributes;

    /**
     * @return mixed
     */
    public function get_framecode() {
        return $this->framecode;
    }

    /**
     * @param mixed $framecode
     */
    public function set_framecode( $framecode ) {
        $this->framecode = $framecode;
    }

    /**
     * @return mixed
     */
    public function get_attributes() {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     */
    public function set_attributes( $attributes ) {
        $this->attributes = $attributes;
    }

}