<?php

namespace Mythic\Functions\Fulfillment;

/**
 * Class MC2_Invoice_Functions
 *
 * @package Mythic\Functions
 */
class MC2_Invoice_Functions {

    /**
     * @param $paper_format
     * @param $template_type
     *
     * @return array
     */
    public static function paperFormat( $paper_format, $template_type ) : array {
        $width        = 4.1;                                 //inches!
        $height       = 5.8;                                 //inches!
        $paper_format = [ 0, 0, $width * 72, $height * 72 ]; //convert inches to points

        return $paper_format;
    }

}