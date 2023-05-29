<?php

namespace Mythic_Core\Functions;

use Mythic_Core\Display\MC_Template_Parts;
use Mythic_Core\Users\MC_Wp_User;
use Mythic_Core\Utils\MC_Vars;

/**
 * Class MC_Search_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Search_Functions {
    
    public static $as_display_per_page = 100;
    
    public static function asGenerateSearchCriteriaFromGet() {
        $params = [];
        if( !isset( $_GET['type'] ) ) return $params;
        
        $params['type'] = $_GET['type'];
        
        $page = get_query_var( 'paged' );
        if( !empty( $page ) ) {
            $params['page'] = $page;
        }
        switch( $params['type'] ) {
            case 'cards':
            case 'sets':
                if( isset( $_GET['search'] ) ) {
                    $params['search'] = $_GET['search'];
                }
                break;
            
            case 'set':
                if( isset( $_GET['set_id'] ) ) {
                    $params['set_id'] = $_GET['set_id'];
                }
                if( isset( $_GET['search'] ) ) {
                    $params['search'] = $_GET['search'];
                }
                break;
            
            case 'card':
                if( isset( $_GET['card_id'] ) ) {
                    $params['card_id'] = $_GET['card_id'];
                }
                if( isset( $_GET['order'] ) ) {
                    $params['order'] = $_GET['order'];
                }
                if( isset( $_GET['orderby'] ) ) {
                    $params['orderby'] = $_GET['orderby'];
                }
                break;
            
            case 'designs':
                if( isset( $_GET['card_id'] ) ) {
                    $params['form_data']['card_id'] = $_GET['card_id'];
                }
                if( isset( $_GET['set_id'] ) ) {
                    $params['form_data']['set_id'] = $_GET['set_id'];
                }
                if( isset( $_GET['tag_id'] ) ) {
                    $params['form_data']['tag_id'] = $_GET['tag_id'];
                }
                if( isset( $_GET['artist_id'] ) ) {
                    $params['form_data']['artist_id'] = $_GET['alterist_id'];
                }
                break;
        }
        
        return $params;
    }
    
    /**
     * @param bool $echo
     *
     * @return false|string|void
     */
    public static function asDisplaySearchPageContent( $args = [], $echo = true ) {
        static::asPrepareSearchArgs( $args );
        
        $data = MC_Template_Parts::get( 'browsing', 'layout', $args );
        if( $echo ) {
            echo $data;
            
            return;
        }
        
        return $data;
    }
    
    /**
     * Render affiliates control content
     *
     * @param array $args
     * @param bool  $echo
     *
     * @return false|string|void
     */
    public static function asDisplaySearchControl( $args = [], $echo = true ) {
        static::asPrepareSearchArgs( $args );
        
        switch( $args['type'] ) {
            case 'allAffiliates':
                $data = MC_Template_Parts::get( 'affiliates/parts/as-affiliates-control', 'all', $args );
                break;
            case 'allPromotions':
                $data = MC_Template_Parts::get( 'affiliates/parts/as-affiliates-coupons-control', 'all', $args );
                break;
            case 'promotionCodes':
                if( !empty( $_POST['promotionId'] ) ) {
                    $args['promotion_id'] = $_POST['promotionId'];
                }
                $data = MC_Template_Parts::get( 'affiliates/parts/as-affiliates-coupons', 'codes', $args );
                break;
            case 'artistAllShares':
                if( !empty( $_POST['userId'] ) ) {
                    static::prepareShareSearchArgs( $args );
                    $data = MC_Template_Parts::get( 'rights-sharing-control/parts/artist', 'all_shares', $args );
                }
                break;
            case 'publisherNotAccepted':
                if( !empty( $_POST['userId'] ) ) {
                    static::prepareShareSearchArgs( $args );
                    $data = MC_Template_Parts::get( 'rights-sharing-control/parts/publisher', 'not_accepted_shares', $args );
                }
                break;
            case 'publisherAllShares':
                if( !empty( $_POST['userId'] ) ) {
                    static::prepareShareSearchArgs( $args );
                    $data = MC_Template_Parts::get( 'rights-sharing-control/parts/publisher', 'all_shares', $args );
                }
                break;
            case 'financeControl':
                static::prepareFinanceSearchArgs( $args );
                $data = MC_Template_Parts::get( 'finance/parts/finance', 'all_transactions', $args );
                break;
            default:
                $data = '';
        }
        
        if( $echo ) {
            echo $data;
            
            return;
        }
        
        return $data;
    }
    
    public static function prepareShareSearchArgs( &$args ) {
        $args['user_id']           = $_POST['userId'];
        $args['skip_filters_html'] = !empty( $_POST['skipFiltersHtml'] ) ? $_POST['skipFiltersHtml'] : 0;
        $args['product_id']        = !empty( $_POST['filterByProductId'] ) ? $_POST['filterByProductId'] : 0;
        $args['publisher']         = !empty( $_POST['filterByPublisher'] ) ? $_POST['filterByPublisher'] : 0;
        $args['artist']            = !empty( $_POST['filterByArtist'] ) ? $_POST['filterByArtist'] : 0;
        $args['status']            = !empty( $_POST['filterByStatus'] ) ? $_POST['filterByStatus'] : 0;
    }
    
    public static function prepareFinanceSearchArgs( &$args ) {
        $args['skip_filters_html'] = !empty( $_POST['skipFiltersHtml'] ) ? $_POST['skipFiltersHtml'] : 0;
        $args['user_id']           = !empty( $_POST['userId'] ) ? $_POST['userId'] : [];
        $args['product_id']        = !empty( $_POST['filterByProductId'] ) ? $_POST['filterByProductId'] : [];
        $args['order_id']          = !empty( $_POST['filterByOrderId'] ) ? $_POST['filterByOrderId'] : [];
        $args['transaction_type']  = !empty( $_POST['filterByType'] ) ? $_POST['filterByType'] : [];
    }
    
    /**
     * Prepares search args
     *
     * @param $args
     */
    public static function asPrepareSearchArgs( &$args ) {
        if( empty( $args ) ) $args = self::asGenerateSearchCriteriaFromGet();
        
        $args = array_merge( $args, [
            'per_page' => static::$as_display_per_page,
        ] );
    }
    
    public static $table_name = 'mc_search_indexing';
    public static $list_of_types_for_indexing = [
        'terms' => [
            'cards' => [
                'tax'            => 'mtg_card',
                'searchable_key' => 'as_searchable_name',
                'order_key'      => 'mc_edhrec_rank',
            ],
            'sets'  => [ 'tax' => 'mtg_set' ],
        ],
        'users' => [
            'content_creators' => [ 'role' => 'content_creator', 'searchable_key' => 'display_name' ],
            'artists'          => [ 'role' => 'alterist', 'searchable_key' => 'display_name' ],
        ]
    ];
    
    /**
     * @param     $search_term
     * @param     $type
     * @param int $limit
     * @param int $offset
     * @param int $is_ordered
     *
     * @return array|string
     */
    public static function searchWithIndexing( $search_term, $type, $limit = 0, $offset = 0, $is_ordered = 0 ) {
        $search_term = MC_Vars::alphanumericOnlyForSearch( $search_term );
        if( empty( $search_term ) ) return '';
        
        global $wpdb;
        $table_name = static::$table_name;
        
        $query = "SELECT object_id FROM $table_name WHERE searchable_name LIKE '%$search_term%' AND type = '$type'";
        
        $order_field = !empty( $is_ordered ) ? 'order_rank' : 'id';
        
        $query .= " ORDER BY $order_field ASC";
        
        if( !empty( $limit ) ) {
            $query .= " LIMIT $offset, $limit";
        }
        
        return $wpdb->get_col( $query );
    }
    
    /**
     * @param     $search_term
     * @param     $type
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public static function userIndexedSearch( $search_term, $type, $limit = 10, $offset = 0 ) {
        return self::prepareUserSearchResult(
            MC_Search_Functions::searchWithIndexing( $search_term, $type, $limit, $offset ), $type
        );
    }
    
    /**
     * @param $users
     * @param $type
     *
     * @return array
     */
    public static function prepareUserSearchResult( $users, $type ) {
        if( empty( $users ) ) return [];
        foreach( $users as $user_key => $user_id ) {
            if( empty( $user = new MC_Wp_User( $user_id ) ) ) {
                unset( $users[ $user_key ] );
                continue;
            }
            $type                 = $type == 'content_creator' ? 'content-creator' : $type;
            $results[ $user_key ] = [
                'id'    => $user_id,
                'name'  => $user->getDisplayName(),
                'email' => $user->getEmail(),
                'link'  => '/'.$type.'/'.$user->getNiceName(),
            ];
        }
        ksort( $results );
        
        return array_values( $results );
    }
    
    /**
     * Runs search global indexing with saving in JSON file
     */
    public static function runGlobalIndexingJson() {
        $tax_data   = self::runTaxIndexingJson();
        $users_data = self::runUsersIndexingJson();
        
        $global_data = array_merge_recursive( $tax_data, $users_data );
        
        $folder_path = static::prepareFolderForIndexedData();
        static::clearFolderForIndexedData( $folder_path );
        static::createAllEmptyFiles( $folder_path );
        foreach( $global_data as $global_data_key => $global_data_single ) {
            self::writeIndexedDataToJson( $folder_path.$global_data_key, $global_data_single );
        }
    }
    
    /**
     * Runs search terms indexing for JSON file
     */
    public static function runTaxIndexingJson() {
        $data = [];
        foreach( static::$list_of_types_for_indexing['terms'] as $type_key => $type ) {
            $args = [
                'number'     => 0,
                'hide_empty' => false,
                'taxonomy'   => $type['tax'],
                'fields'     => 'ids',
            ];
            
            if( empty( $terms = get_terms( $args ) ) ) return $data;
            
            foreach( $terms as $term ) {
                if( !empty( $type['searchable_key'] ) ) {
                    $term_searchable_name = get_term_meta( $term, $type['searchable_key'], 1 );
                }
                $term_object = get_term( $term );
                $term_id     = $term_object->term_id;
                
                $args  = [
                    'post_type'      => [ 'product', 'printing' ],
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'tax_query'      => [
                        [
                            'taxonomy' => $type['tax'],
                            'field'    => 'term_id',
                            'terms'    => $term_id,
                        ],
                    ],
                ];
                $posts = get_posts( $args );
                if( empty( $posts ) ) continue;
                $term_readable_name   = !empty( $term_object->name ) ? $term_object->name : '';
                $term_searchable_name = !empty( $term_searchable_name ) ? $term_searchable_name : $term_readable_name;
                if( empty( $term_searchable_name ) ) continue;
                $term_edhrec_rank              = !empty( $type['order_key'] ) ? get_term_meta( $term, $type['order_key'], 1 ) : 0;
                $term_searchable_name          = MC_Vars::alphanumericOnlyForSearch( $term_searchable_name );
                $term_searchable_name          = preg_replace( "/\s+/", " ", $term_searchable_name );
                $term_searchable_name_exploded = explode( ' ', $term_searchable_name );
                $already_added_letter          = [];
                foreach( $term_searchable_name_exploded as $term_searchable_name_single ) {
                    $first_letter = substr( $term_searchable_name_single, 0, 1 );
                    if( empty( $first_letter ) ) continue;
                    $first_letter = is_numeric( $first_letter ) ? 'a'.$first_letter : $first_letter;
                    if( in_array( $first_letter, $already_added_letter ) ) continue;
                    $already_added_letter[]               = $first_letter;
                    $data[ $first_letter ][ $type_key ][] = [
                        'i' => $term,
                        'n' => $term_searchable_name,
                        'r' => $term_readable_name,
                        'o' => intval( $term_edhrec_rank ),
                    ];
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Runs search users indexing
     */
    public static function runUsersIndexingJson() {
        $data = [];
        foreach( static::$list_of_types_for_indexing['users'] as $role_key => $role ) {
            if( $role['role'] == 'alterist' ) {
                $users = MC_Artist_Functions::getAllAlteristsWithDesign();
            } else {
                $users = get_users( [ 'number' => 0, 'role__in' => [ $role['role'] ] ] );
            }
            
            if( empty( $users ) ) return $data;
            
            foreach( $users as $user ) {
                $user_searchable_name          = MC_Vars::alphanumericOnlyForSearch( $user->display_name );
                $user_searchable_name          = preg_replace( "/\s+/", " ", $user_searchable_name );
                $user_searchable_name_exploded = explode( ' ', $user_searchable_name );
                $already_added_letter          = [];
                foreach( $user_searchable_name_exploded as $user_searchable_name_single ) {
                    $first_letter = substr( $user_searchable_name_single, 0, 1 );
                    $first_letter = is_numeric( $first_letter ) ? 'a'.$first_letter : $first_letter;
                    if( in_array( $first_letter, $already_added_letter ) ) continue;
                    $already_added_letter[]               = $first_letter;
                    $data[ $first_letter ][ $role_key ][] = [
                        'i' => $user->ID,
                        'n' => $user_searchable_name,
                        'r' => $user->display_name,
                        'l' => $user->user_nicename,
                    ];
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Runs search posts indexing
     */
    public static function runPostsIndexingJson() {
        $data = [];
        foreach( static::$list_of_types_for_indexing['posts'] as $type_key => $type ) {
            $args = [
                'number'     => 0,
                'hide_empty' => false,
                'taxonomy'   => $type['tax'],
                'fields'     => 'ids',
            ];
            
            if( empty( $posts = get_posts( $args ) ) ) return $data;
            
            foreach( $posts as $post ) {
                if( !empty( $type['searchable_key'] ) ) {
                    $post_searchable_name = get_post_meta( $post, $type['searchable_key'], 1 );
                }
                $post_searchable_name = !empty( $post_searchable_name ) ? $post_searchable_name : $post->post_title;
                if( empty( $post_searchable_name ) ) continue;
                $post_edhrec_rank              = !empty( $type['order_key'] ) ? get_post_meta( $post, $type['order_key'], 1 ) : 0;
                $post_searchable_name          = MC_Vars::alphanumericOnlyForSearch( $post_searchable_name );
                $post_searchable_name          = preg_replace( "/\s+/", " ", $post_searchable_name );
                $post_searchable_name_exploded = explode( ' ', $post_searchable_name );
                $already_added_letter          = [];
                foreach( $post_searchable_name_exploded as $post_searchable_name_single ) {
                    if( empty( $post_searchable_name_single ) ) continue;
                    $first_letter = substr( $post_searchable_name_single, 0, 1 );
                    $first_letter = is_numeric( $first_letter ) ? 'a'.$first_letter : $first_letter;
                    if( in_array( $first_letter, $already_added_letter ) ) continue;
                    $already_added_letter[]               = $first_letter;
                    $data[ $first_letter ][ $type_key ][] = [
                        'i' => $post,
                        'n' => $post_searchable_name,
                        'o' => intval( $post_edhrec_rank ),
                        'r' => $post->post_title,
                    ];
                }
            }
        }
        
        return $data;
    }
    
    /**
     * @param       $file_name
     * @param array $data
     *
     * @return bool
     */
    public static function writeIndexedDataToJson( $file_name, $data = [] ) {
        return file_put_contents( $file_name.'.json', json_encode( $data ) );
    }
    
    /**
     * @return string
     */
    public static function prepareFolderForIndexedData() {
        $folder_path = ABSPATH.'files/search_indexing/';
        
        if( !file_exists( $folder_path ) ) {
            mkdir( $folder_path, 0755, true );
        }
        
        return $folder_path;
    }
    
    /**
     * @param $folder_path
     */
    public static function clearFolderForIndexedData( $folder_path ) {
        $files = glob( $folder_path.'*' );
        foreach( $files as $file ) {
            if( is_file( $file ) ) {
                unlink( $file );
            }
        }
    }
    
    /**
     * @param $folder_path
     */
    public static function createAllEmptyFiles( $folder_path ) {
        $letters = range( 'a', 'z' );
        foreach( $letters as $letter ) {
            static::writeIndexedDataToJson( $folder_path.$letter );
        }
        
        $numbers = range( 0, 9 );
        foreach( $numbers as $number ) {
            static::writeIndexedDataToJson( $folder_path.'a'.$number );
        }
    }
    
}