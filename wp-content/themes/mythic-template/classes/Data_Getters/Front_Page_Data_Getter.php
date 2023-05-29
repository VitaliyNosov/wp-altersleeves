<?php

namespace Mythic_Template\Data_Getters;

use Mythic_Template\Abstracts\MT_Data_Getter;

class Front_Page_Data_Getter extends MT_Data_Getter
{
    public static $api_data_list = [
        'new-mythic-staff',
        'bestsellers',
        'sales',
        'category',
        'alters',
        'mythic-frames',
        'articles',
        'creators'
    ];
    public static $options_data_list = ['banner-data'];
    public static $local_data_list = ['products-by-browsing-history'];
}
