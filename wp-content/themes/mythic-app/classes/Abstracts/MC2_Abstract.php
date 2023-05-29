<?php

namespace Mythic\Abstracts;

class MC2_Abstract {
    
    /**
     * @param ...$params
     *
     * @return static
     */
    public static function new( ...$params) {
        return new static($params);
    }

}