<?php

namespace Mythic\Functions\API\Partials;

use Mythic\Abstracts\MC2_API_Abstract;

class MC2_Content extends MC2_API_Abstract {
    
    const CACHE_NAME = 'api_content';
    
    public function __construct( $cache = true ) {
        parent::__construct();
        $this->set_cache( $cache );
    }
    
    public function get_data() {
        // TODO: Implement get_data() method.
    }
    
    public function get_home_data() {
        $data = [
            // 'top_banner' => [ 'html', 'background', 'padding' ]
            'slider_products_big_single_and_half' =>
                [
                    'title'    => 'NEW MYTHIC STAFF',
                    'products' =>
                        [
                            0 =>
                                [
                                    'id'    => 1,
                                    'title' => 'Non-CREATURE FRAMES PACK',
                                    'price' =>
                                        [
                                            'usd' => '$9',
                                            'eur' => 8,
                                        ],
                                    'image' => '',
                                ],
                            1 =>
                                [
                                    'id'    => 2,
                                    'title' => 'Non-CREATURE FRAMES PACK2',
                                    'price' =>
                                        [
                                            'usd' => '$9',
                                            'eur' => 8,
                                        ],
                                    'image' => '',
                                ],
                            2 =>
                                [
                                    'id'    => 3,
                                    'title' => 'Non-CREATURE FRAMES PACK3',
                                    'price' =>
                                        [
                                            'usd' => '$9',
                                            'eur' => 8,
                                        ],
                                    'image' => '',
                                ],
                        ],
                ],
            'homepage_sorted_products'            =>
                [
                    'bannerUnlocked' =>
                        [
                            'html'       => '<p>New set</p><h3>Unlocked</h3>',
                            'background' => '',
                        ],
                    'bestsellers'    =>
                        [
                            'title'         => 'BESTSELLERS',
                            'see_more_link' =>
                                [
                                    'title' => 'SEE MORE',
                                    'url'   => '/',
                                ],
                            'products'      =>
                                [
                                    0 =>
                                        [
                                            'id'    => 1,
                                            'title' => 'Non-CREATURE FRAMES PACK',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    1 =>
                                        [
                                            'id'    => 2,
                                            'title' => 'Non-CREATURE FRAMES PACK2',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    2 =>
                                        [
                                            'id'    => 3,
                                            'title' => 'Non-CREATURE FRAMES PACK3',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    3 =>
                                        [
                                            'id'    => 4,
                                            'title' => 'Non-CREATURE FRAMES PACK4',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                ],
                        ],
                    'sales'          =>
                        [
                            'title'         => 'SALES',
                            'see_more_link' =>
                                [
                                    'title' => 'SEE MORE',
                                    'url'   => '/',
                                ],
                            'products'      =>
                                [
                                    0 =>
                                        [
                                            'id'    => 1,
                                            'title' => 'Non-CREATURE FRAMES PACK',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    1 =>
                                        [
                                            'id'    => 2,
                                            'title' => 'Non-CREATURE FRAMES PACK2',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    2 =>
                                        [
                                            'id'    => 3,
                                            'title' => 'Non-CREATURE FRAMES PACK3',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    3 =>
                                        [
                                            'id'    => 4,
                                            'title' => 'Non-CREATURE FRAMES PACK4',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                ],
                        ],
                    'categoryOne'    =>
                        [
                            'title'         => 'Category',
                            'see_more_link' =>
                                [
                                    'title' => 'SEE MORE',
                                    'url'   => '/',
                                ],
                            'products'      =>
                                [
                                    0 =>
                                        [
                                            'id'    => 1,
                                            'title' => 'Non-CREATURE FRAMES PACK',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    1 =>
                                        [
                                            'id'    => 2,
                                            'title' => 'Non-CREATURE FRAMES PACK2',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    2 =>
                                        [
                                            'id'    => 3,
                                            'title' => 'Non-CREATURE FRAMES PACK3',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    3 =>
                                        [
                                            'id'    => 4,
                                            'title' => 'Non-CREATURE FRAMES PACK4',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                ],
                        ],
                    'categoryTwo'    =>
                        [
                            'title'         => 'Category',
                            'see_more_link' =>
                                [
                                    'title' => 'SEE MORE',
                                    'url'   => '/',
                                ],
                            'products'      =>
                                [
                                    0 =>
                                        [
                                            'id'    => 1,
                                            'title' => 'Non-CREATURE FRAMES PACK',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    1 =>
                                        [
                                            'id'    => 2,
                                            'title' => 'Non-CREATURE FRAMES PACK2',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    2 =>
                                        [
                                            'id'    => 3,
                                            'title' => 'Non-CREATURE FRAMES PACK3',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                    3 =>
                                        [
                                            'id'    => 4,
                                            'title' => 'Non-CREATURE FRAMES PACK4',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'image' => '',
                                        ],
                                ],
                        ],
                ],
            'wc_featured_sliders'                 =>
                [
                    0 =>
                        [
                            'logo'          => '',
                            'title'         => 'ALTER SLEEVES',
                            'link'          =>
                                [
                                    'title' => 'ALTER SLEEVES',
                                    'url'   => '#',
                                ],
                            'description'   => '<p>Aliquam quis ante porta, faucibus metus eu, auctor massa. Aenean id convallis neque. Donec sit amet auctor urna. Sed id mi quis arcu posuere rutrum sit amet ac dolor.                                 Curabitur dignissim dictum feugiat. Phasellus placerat lectus lacus, ac tempus mi sagittis sit amet. Duis a porttitor lectus, a vestibulum metus.</p><p>Donec egestas eget nisl at consectetur. Sed efficitur fringilla lorem, eu ornare nisi lobortis non. Proin luctus, quam elementum facilisis convallis, diam odio pellentesque felis, vel dapibus magna massa elementum magna. Donec lacinia nibh at feugiat pulvinar. Nullam a est tincidunt, vehicula ipsum at, vestibulum tortor. In diam elit, porta eget ex a, pretium pretium nibh. Suspendisse aliquet consectetur leo a hendrerit.</p>',
                            'see_more_link' =>
                                [
                                    'title' => 'SEE MORE ALTER SLEEVES PRODUCTS',
                                    'url'   => '#',
                                ],
                            'products'      =>
                                [
                                    0 =>
                                        [
                                            'id'    => 1,
                                            'title' => 'Non-CREATURE FRAMES PACK',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'url'   => '#',
                                            'image' => '',
                                        ],
                                    1 =>
                                        [
                                            'id'    => 2,
                                            'title' => 'Non-CREATURE FRAMES PACK2',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'url'   => '#',
                                            'image' => '',
                                        ],
                                    2 =>
                                        [
                                            'id'    => 3,
                                            'title' => 'Non-CREATURE FRAMES PACK3',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'url'   => '#',
                                            'image' => '',
                                        ],
                                    3 =>
                                        [
                                            'id'    => 4,
                                            'title' => 'Non-CREATURE FRAMES PACK4',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'url'   => '#',
                                            'image' => '',
                                        ],
                                    4 =>
                                        [
                                            'id'    => 5,
                                            'title' => 'Non-CREATURE FRAMES PACK5',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'url'   => '#',
                                            'image' => '',
                                        ],
                                ],
                        ],
                    1 =>
                        [
                            'logo'          => '',
                            'title'         => 'MYTHIC FRAMES',
                            'link'          =>
                                [
                                    'title' => 'MYTHIC FRAMES',
                                    'url'   => '#',
                                ],
                            'description'   => '<p>Aliquam quis ante porta, faucibus metus eu, auctor massa. Aenean id convallis neque. Donec sit amet auctor urna. Sed id mi quis arcu posuere rutrum sit amet ac dolor.                                 Curabitur dignissim dictum feugiat. Phasellus placerat lectus lacus, ac tempus mi sagittis sit amet. Duis a porttitor lectus, a vestibulum metus.</p><p>Donec egestas eget nisl at consectetur. Sed efficitur fringilla lorem, eu ornare nisi lobortis non. Proin luctus, quam elementum facilisis convallis, diam odio pellentesque felis, vel dapibus magna massa elementum magna. Donec lacinia nibh at feugiat pulvinar. Nullam a est tincidunt, vehicula ipsum at, vestibulum tortor. In diam elit, porta eget ex a, pretium pretium nibh. Suspendisse aliquet consectetur leo a hendrerit.</p>',
                            'see_more_link' =>
                                [
                                    'title' => 'SEE MORE MYTHIC FRAMES PRODUCTS',
                                    'url'   => '#',
                                ],
                            'products'      =>
                                [
                                    0 =>
                                        [
                                            'id'    => 1,
                                            'title' => 'Non-CREATURE FRAMES PACK',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'url'   => '#',
                                            'image' => '',
                                        ],
                                    1 =>
                                        [
                                            'id'    => 2,
                                            'title' => 'Non-CREATURE FRAMES PACK2',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'url'   => '#',
                                            'image' => '',
                                        ],
                                    2 =>
                                        [
                                            'id'    => 3,
                                            'title' => 'Non-CREATURE FRAMES PACK3',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'url'   => '#',
                                            'image' => '',
                                        ],
                                    3 =>
                                        [
                                            'id'    => 4,
                                            'title' => 'Non-CREATURE FRAMES PACK4',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'url'   => '#',
                                            'image' => '',
                                        ],
                                    4 =>
                                        [
                                            'id'    => 5,
                                            'title' => 'Non-CREATURE FRAMES PACK5',
                                            'price' =>
                                                [
                                                    'usd' => '$9',
                                                    'eur' => 8,
                                                ],
                                            'url'   => '#',
                                            'image' => '',
                                        ],
                                ],
                        ],
                ],
            'slider_articles'                     =>
                [
                    'title'         => 'NEWEST ARTICLES',
                    'see_more_link' =>
                        [
                            'title' => 'More articles',
                            'url'   => '#',
                        ],
                    'posts'         =>
                        [
                            0 =>
                                [
                                    'title'          => 'Article title',
                                    'image'          => '',
                                    'read_more_link' =>
                                        [
                                            'title' => 'READ ARTICLE',
                                            'url'   => '#',
                                        ],
                                ],
                            1 =>
                                [
                                    'title'          => 'Article title2',
                                    'image'          => '',
                                    'read_more_link' =>
                                        [
                                            'title' => 'READ ARTICLE',
                                            'url'   => '#',
                                        ],
                                ],
                            2 =>
                                [
                                    'title'          => 'Article title3',
                                    'image'          => '',
                                    'read_more_link' =>
                                        [
                                            'title' => 'READ ARTICLE',
                                            'url'   => '#',
                                        ],
                                ],
                        ],
                ],
            'slider_creators'                     =>
                [
                    'title'         => 'RECENTLY JOINED CrEators',
                    'see_more_link' =>
                        [
                            'title' => 'FIND MORE CREATORS',
                            'url'   => '#',
                        ],
                    'posts'         =>
                        [
                            0 =>
                                [
                                    'title'          => 'Creators name/NICK',
                                    'image'          => '',
                                    'profile_link'   =>
                                        [
                                            'title' => 'store.Artist.com',
                                            'url'   => '#',
                                        ],
                                    'read_more_link' =>
                                        [
                                            'title' => 'View profile',
                                            'url'   => '#',
                                        ],
                                ],
                            1 =>
                                [
                                    'title'          => 'Creators name/NICK',
                                    'image'          => '',
                                    'profile_link'   =>
                                        [
                                            'title' => 'store.Artist.com',
                                            'url'   => '#',
                                        ],
                                    'read_more_link' =>
                                        [
                                            'title' => 'View profile',
                                            'url'   => '#',
                                        ],
                                ],
                            2 =>
                                [
                                    'title'          => 'Creators name/NICK',
                                    'image'          => '',
                                    'profile_link'   =>
                                        [
                                            'title' => 'store.Artist.com',
                                            'url'   => '#',
                                        ],
                                    'read_more_link' =>
                                        [
                                            'title' => 'View profile',
                                            'url'   => '#',
                                        ],
                                ],
                            3 =>
                                [
                                    'title'          => 'Creators name/NICK',
                                    'image'          => '',
                                    'profile_link'   =>
                                        [
                                            'title' => 'store.Artist.com',
                                            'url'   => '#',
                                        ],
                                    'read_more_link' =>
                                        [
                                            'title' => 'View profile',
                                            'url'   => '#',
                                        ],
                                ],
                        ],
                ],
            'slider_products'                     =>
                [
                    'title'         => 'Inspired by your browsing history',
                    'see_more_link' =>
                        [
                            'title' => 'SEE MORE',
                            'url'   => '#',
                        ],
                    'products'      =>
                        [
                            0 =>
                                [
                                    'id'    => 1,
                                    'title' => 'Non-CREATURE FRAMES PACK',
                                    'price' =>
                                        [
                                            'usd' => '$9',
                                            'eur' => 8,
                                        ],
                                    'url'   => '#',
                                    'image' => '',
                                ],
                            1 =>
                                [
                                    'id'    => 2,
                                    'title' => 'Non-CREATURE FRAMES PACK2',
                                    'price' =>
                                        [
                                            'usd' => '$9',
                                            'eur' => 8,
                                        ],
                                    'url'   => '#',
                                    'image' => '',
                                ],
                            2 =>
                                [
                                    'id'    => 3,
                                    'title' => 'Non-CREATURE FRAMES PACK3',
                                    'price' =>
                                        [
                                            'usd' => '$9',
                                            'eur' => 8,
                                        ],
                                    'url'   => '#',
                                    'image' => '',
                                ],
                            3 =>
                                [
                                    'id'    => 4,
                                    'title' => 'Non-CREATURE FRAMES PACK4',
                                    'price' =>
                                        [
                                            'usd' => '$9',
                                            'eur' => 8,
                                        ],
                                    'url'   => '#',
                                    'image' => '',
                                ],
                            4 =>
                                [
                                    'id'    => 5,
                                    'title' => 'Non-CREATURE FRAMES PACK5',
                                    'price' =>
                                        [
                                            'usd' => '$9',
                                            'eur' => 8,
                                        ],
                                    'url'   => '#',
                                    'image' => '',
                                ],
                        ],
                ],
            'promotion_join_us'                   =>
                [
                    'html' => '<h3>WOULD LIKE TO GET PERSONALIZED RECOMMENDATIONS?</h3>',
                ],
        ];
        return apply_filters( 'ma_api_home', $data );
    }
    
}