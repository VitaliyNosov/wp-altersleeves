<?php

namespace Mythic\Objects\Mtg;

use Mythic\Abstracts\MC2_Object;

class MC2_Frame_Attribute extends MC2_Object {

    protected static $table_name = 'mtg_frame_attrs';

    public $type;
    public $name;

    /**
     * @return mixed
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function set_type( $type ) : void {
        $this->type = $type;
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
    public function set_name( $name ) : void {
        $this->name = $name;
    }

}