<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;
use Mythic_Template\Data_Getters\Front_Page_Data_Getter;

class FrontPage extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'front-page',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        $data_getter = new Front_Page_Data_Getter();

        return $data_getter->getData();
    }
}
