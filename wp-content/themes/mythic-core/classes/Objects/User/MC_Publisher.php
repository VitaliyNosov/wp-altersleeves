<?php

namespace Mythic_Core\Users;

use Mythic_Core\Functions\MC_User_Functions;
use Mythic_Core\System\MC_Statuses;
use Mythic_Core\System\MC_WP;

/**
 * Class MC_Publisher
 */
class MC_Publisher extends MC_User_Functions {
    
    /**
     * @param null $user
     *
     * @return bool
     */
    public static function is( $user = null ) : bool {
        $user_id = self::id( $user );
        
        return user_can( $user_id, 'publisher' );
    }
    
    /**
     * @param int  $publisher_id
     * @param bool $fields
     *
     * @return array
     */
    public static function pieces( $publisher_id = 0, $fields = true ) : array {
        if( empty( $publisher_id ) ) return [];
        $args = [
            'post_type'      => MC_WP::postTypes(),
            'post_status'    => MC_Statuses::keys(),
            'posts_per_page' => -1,
            'fields'         => $fields,
            'meta_query'     => [
                [
                    'key'   => 'mc_publisher',
                    'value' => $publisher_id,
                ],
            ],
        ];
        
        return get_posts( $args );
    }
    
}