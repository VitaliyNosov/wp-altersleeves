<?php

namespace App\View\Composers\Partials;

use Roots\Acorn\View\Composer;
use Mythic_Template\Data_Getters\Partials\Header_Data_Getter;

class Header extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.header',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        $data_getter = new Header_Data_Getter();

        return $data_getter->getData();
    }
}
