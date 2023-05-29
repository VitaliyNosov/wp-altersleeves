<?php

namespace App\View\Composers\Partials;

use Roots\Acorn\View\Composer;
use Mythic_Template\Data_Getters\Page_Templates\Search_Results_Data_Getter;

class SearchResults extends Composer
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
        $data_getter = new Search_Results_Data_Getter();

        return $data_getter->getData();
    }
}
