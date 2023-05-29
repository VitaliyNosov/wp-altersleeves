<?php

namespace Mythic_Core\Functions;

use MC_WP;
use Mythic_Core\Objects\MC_User;
use Mythic_Core\Utils\MC_Pagination;
use Mythic_Core\Utils\MC_Vars;

class MC_Artist_Functions {
    
    /**
     * @param false $with_design
     *
     * @return array
     */
    public static function getAllAlterists( $with_design = false ) {
        $args           = self::getStandardQueryArgs();
        $args['number'] = 0;
        if( $with_design ) $args['has_published_posts'] = 'design';
        
        return get_users( $args );
    }
    
    /**
     * @return array
     */
    public static function getAllAlteristsWithDesign() : array {
        return self::getAllAlterists( true );
    }
    
    /**
     * @return string
     */
    public static function displayAlteristsFromUsers() {
        $creatorsAll = $creators = get_option( 'mc_alterists_index', [] );
        
        $page     = !empty( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        $offset   = ( $page - 1 ) * 24;
        $creators = array_splice( $creators, $offset, 24 );
        
        $output = '';
        if( $creatorsAll > 24 ) {
            ob_start();
            MC_Pagination::asDisplayPagination( count( $creatorsAll ), 24 );
            $output .= ob_get_clean();
        }
        
        $output .= '<div class="alterists row">';
        
        foreach( $creators as $idCreator ) {
            $output .= self::displayAlteristPreview( $idCreator );
        }
        $output .= '</div>';
        if( $creatorsAll > 24 ) {
            ob_start();
            MC_Pagination::paginationBrowsing( count( $creatorsAll ), 24 );
            $output .= ob_get_clean();
        }
        
        return $output;
    }
    
    /**
     * @param $creator
     * @param $imageRequired
     *
     * @return string
     */
    public static function displayAlteristPreview( $creator, $imageRequired = false ) {
        if( user_can( $creator, 'manage_options' ) ) return '';
        $output  = '';
        $name    = get_the_author_meta( 'display_name', $creator );
        $default = get_stylesheet_directory_uri().'/img/user/profile.png';
        $image   = MC_User::avatar( $creator );
        if( $imageRequired && ( empty( $image ) || $image == get_stylesheet_directory_uri().'/img/user/profile.png' ) ) return '';
        $output .= '<div class="col-6 col-sm-4 col-md-3 alterist__item">';
        $output .= '<a href="'.self::urlProfile( $creator ).'">';
        $output .= '<div class="alterist__image" style="background-image:url('.$image.');background-position:center;"></div>';
        $output .= '<div class="alterist__name">'.$name.'</div>';
        $output .= '</a>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * @param int $idCreator
     *
     * @return string
     */
    public static function urlProfile( $idCreator = 0 ) {
        if( empty( $idCreator ) ) return '';
        
        return '/alterist/'.get_the_author_meta( 'user_nicename', $idCreator );
    }
    
    /**
     * @return string
     */
    public static function getAlteristSocialIcons() {
        $idCreator = self::getAlteristId();
        $socials   = [
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_twitch',
            'social_artstation',
            'social_youtube',
        ];
        $output    = '';
        foreach( $socials as $social ) {
            if( get_the_author_meta( $social, $idCreator ) == '' ) continue;
            $url = get_the_author_meta( $social, $idCreator );
            switch( $social ) {
                case 'social_facebook' :
                    $icon = '<i class="fab fa-facebook-f p-2" style="font-size:21px;"></i>';
                    break;
                case 'social_instagram' :
                    $icon = '<i class="fab fa-instagram p-2" style="font-size:21px;"></i>';
                    if( strpos( $url, 'https://www.instagram.com' ) === false ) $url = 'https://www.instagram.com/'.$url;
                    break;
                case 'social_twitter' :
                    $icon = '<i class="fab fa-twitter p-2" style="font-size:21px;"></i>';
                    if( strpos( $url, 'https://www.twitter.com' ) === false ) $url = 'https://www.twitter.com/'.$url;
                    break;
                case 'social_twitch' :
                    $icon = '<i class="fab fa-twitch p-2" style="font-size:21px;"></i>';
                    if( strpos( $url, 'https://www.twitch.tv' ) === false ) $url = 'https://www.twitch.tv/'.$url;
                    break;
                case 'social_artstation' :
                    $icon = '<i class="fab fa-artstation p-2" style="font-size:21px;"></i>';
                    if( strpos( $url, 'https://www.artstation.com' ) === false ) $url = 'https://www.artstation.com/'.$url;
                    break;
                case 'social_youtube' :
                    $icon = '<i class="fab fa-youtube p-2" style="font-size:21px;"></i>';
                    break;
                default :
                    return '';
            }
            $output .= '<a href="'.$url.'" target="_blank">'.$icon.'</a>';
        }
        $output = '<div class="social text-center py-3" >'.$output.'</div>';
        
        return $output;
    }
    
    /**
     * @return string
     */
    public static function getAlteristId() {
        if( is_author() ) {
            $author   = get_queried_object();
            $authorId = $author->ID;
        } else {
            $authorId = get_the_author_meta( 'ID' );
        }
        
        return $authorId;
    }
    
    /**
     * @return array
     */
    public static function users( $limit = 0, $include = [] ) {
        $args = [
            'role'    => 'alterist',
            'orderby' => 'user_nicename',
            'order'   => 'ASC',
            'fields'  => 'ids',
        ];
        if( !empty( $limit ) ) {
            $args['number'] = $limit;
        }
        if( !empty( $include ) ) $args['include'] = $include;
        
        return get_users( $args );
    }
    
    /**
     * @return int[]
     */
    public static function getMtgArtists() {
        return [ 67, 47, 1270, 2820 ];
    }
    
    /**
     * @param int $idCreator
     *
     * @return string
     */
    public static function name( $idCreator = 0 ) {
        if( empty( $idCreator ) || !get_userdata( $idCreator ) ) return '';
        
        return get_the_author_meta( 'display_name', $idCreator );
    }
    
    /**
     * @param int $idAlterist
     *
     * @return array|int[]|WP_Post[]
     */
    public static function designs( int $idAlterist = 0 ) {
        if( empty( $idAlterist ) && !is_user_logged_in() ) return [];
        $idAlterist  = !empty( $idAlterist ) ? $idAlterist : wp_get_current_user()->ID;
        $argsDesigns = [
            'post_type'      => 'design',
            'posts_per_page' => -1,
            'author'         => $idAlterist,
            'post__not_in'   => MC_Product_Functions::snapboltIds(),
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'author__not_in' => self::suspended(),
        ];
        
        return get_posts( $argsDesigns );
    }
    
    public static function suspended() {
        return get_users( [
                              'meta_key'   => 'mc_suspended',
                              'meta_value' => 1,
                              'number'     => -1,
                              'order'      => 'ASC',
                              'fields'     => 'ids',
                          ] );
    }
    
    /**
     * // @Todo maybe add exclude
     *
     * @return array
     */
    public static function getStandardQueryArgs() {
        return [
            'role'    => 'alterist',
            'orderby' => 'display_name',
            'order'   => 'ASC',
            'fields'  => 'all',
        ];
    }
    
    /**
     * @param string $search_term
     * @param int    $limit
     * @param int    $offset
     * @param bool   $with_link
     *
     * @return array
     */
    public static function searchAlterist( $search_term = '', $limit = 150, $offset = 0, $with_link = false ) {
        if( empty( $search_term ) ) return [];
        $search_term = trim( strtolower( MC_Vars::alphanumericOnly( $search_term ) ) );
        
        $args                        = [
            'role'    => 'alterist',
            'orderby' => 'display_name',
            'order'   => 'ASC',
            'fields'  => 'all',
        ];
        $args['number']              = $limit;
        $args['offset']              = $offset;
        $args['search']              = "*".$search_term."*";
        $args['search_columns']      = [ 'display_name' ];
        $args['has_published_posts'] = [ 'design' ];
        
        $alterists = get_users( $args );
        
        if( empty( $alterists ) ) return [];
        
        foreach( $alterists as $alterist_key => $alterist ) {
            $results[ $alterist_key ] = [
                'id'   => $alterist->ID,
                'name' => $alterist->display_name,
            ];
            if( $with_link ) {
                $results[ $alterist_key ]['link'] = self::urlProfile( $alterist->ID );
            }
        }
        ksort( $results );
        
        return array_values( $results );
    }
    
    
    public static function indexAlterists() {
        $args   = [
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'tax_query'      => [
                'RELATION' => 'AND',
                [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'simple',
                ],
                [
                    'taxonomy' => 'product_group',
                    'field'    => 'slug',
                    'terms'    => 'alter',
                ],
            ],
            'fields'         => 'ids',
        ];
        $alters = get_posts($args);
        
        $alterists_with_pics    = [];
        $alterists_without_pics = [];
        $prexisting_alterists   = get_option('mc_alterists_index', []);
        foreach($alters as $alter) {
            $alterist_id = MC_WP::authorId($alter);
            if ( in_array($alterist_id, $alterists_with_pics) || in_array($alterist_id, $alterists_without_pics) ) {
                continue;
            }
            $alterist_avatar = MC_User::avatar($alterist_id);
            if ( empty($alterist_avatar) ) {
                $alterists_without_pics[] = $alterist_id;
            } else {
                $alterists_with_pics[] = $alterist_id;
            }
            $alterists_with_pics    = array_unique($alterists_with_pics);
            //$alterists_without_pics = array_unique($alterists_without_pics);
            $alterists              = array_merge($alterists_with_pics, $alterists_without_pics);
            $alterists              = array_merge($alterists, $prexisting_alterists);
            $alterists              = array_unique($alterists);
        }
    
        update_option('mc_alterists_index', $alterists);
    }
    
}