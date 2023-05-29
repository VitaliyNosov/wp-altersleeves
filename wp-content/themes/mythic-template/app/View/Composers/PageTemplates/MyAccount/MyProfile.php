<?php

namespace App\View\Composers\Partials;

use Roots\Acorn\View\Composer;
use Mythic_Template\Data_Getters\Page_Templates\My_Account\My_Profile_Data_Getter;

class MyProfile extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.footer',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        $data_getter = new My_Profile_Data_Getter();

        return $data_getter->getData();
    }
}
