<?php

namespace Mythic_Core\Utils;

/**
 * Class MC_Pagination
 *
 * @package Mythic_Core\Utils
 */
class MC_Pagination {
    
    /**
     * @param array $args
     *
     * @return string
     */
    public static function get( $args = [] ) : string {
        $quantity        = $args['quantity'] ?? 0;
        $itemsPerPage    = $args['per_page'] ?? 24;
        $ajax_pagination = !empty( $args['ajax'] ) ? true : false;
        $ajax_params     = $args['ajax_params'] ?? [];
        
        if( empty( $quantity ) || $itemsPerPage > $quantity ) return '';
        
        /** Additional parameters */
        if( !is_ajax() ) {
            $queries = !empty( $_GET ) ? $_GET : [];
        } else {
            $queries = $ajax_params;
        }
        $linkextra = [];
        if( !empty( $queries ) ) {
            foreach( $queries as $query => $value ) {
                if( !is_array( $value ) ) {
                    $linkextra[] = $query.'='.urlencode( $value );
                }
            }
        }
        $linkextra = implode( "&amp;", $linkextra );
        if( $linkextra ) {
            $linkextra .= "&amp;";
        }
        
        // build array containing links to all pages
        $page = get_query_var( 'paged' );
        if( $page == 0 ) $page = 1;
        
        $tmp = [];
        for( $p = 1, $i = 0; $i < $quantity; $p++, $i += $itemsPerPage ) {
            if( $page == $p ) {
                // current page shown as bold, no link
                $tmp[] = "<b class='mx-1'>{$p}</b>";
            } else {
                $tmp[] = "<a class='mx-1' href=\"?{$linkextra}paged={$p}\">{$p}</a>";
            }
        }
        
        // thin out the links (optional)
        for( $i = count( $tmp ) - 3; $i > 1; $i-- ) {
            if( abs( $page - $i - 1 ) > 2 ) {
                unset( $tmp[ $i ] );
            }
        }
        
        $output = '';
        if( count( $tmp ) > 1 ) {
            $ajax_pagination_class = !empty( $ajax_pagination ) ? ' as-ajax-pagination' : '';
            $output                .= "<div class='d-block pagination".$ajax_pagination_class.">";
            
            $lastlink = 0;
            foreach( $tmp as $i => $link ) {
                if( $i > $lastlink + 1 ) {
                    echo " ... "; // where one or more links have been omitted
                } else {
                    if( $i ) {
                        echo " ";
                    }
                }
                $output   .= $link;
                $lastlink = $i;
            }
            
            $output .= "</div>";
        }
        
        return $output;
    }
    
    public static function asDisplayPagination( $quantity = 0, $itemsPerPage = 24, $alignment = 'center',
                                                $ajax_pagination = false ) {
        if( empty( $quantity ) || $itemsPerPage > $quantity ) return '';
        
        /** Additional parameters */
        if( !is_ajax() ) {
            $queries = !empty( $_GET ) ? $_GET : [];
        } else {
            $queries = static::asDisplayPaginationAjaxParams();
        }
        $linkextra = [];
        if( !empty( $queries ) ) {
            foreach( $queries as $query => $value ) {
                if( !is_array( $value ) ) {
                    $linkextra[] = $query.'='.urlencode( $value );
                }
            }
        }
        $linkextra = implode( "&amp;", $linkextra );
        if( $linkextra ) {
            $linkextra .= "&amp;";
        }
        
        // build array containing links to all pages
        $page = get_query_var( 'paged' );
        if( $page == 0 ) $page = 1;
        
        $tmp = [];
        for( $p = 1, $i = 0; $i < $quantity; $p++, $i += $itemsPerPage ) {
            if( $page == $p ) {
                // current page shown as bold, no link
                $tmp[] = "<b>{$p}</b>";
            } else {
                $tmp[] = "<a href=\"?{$linkextra}paged={$p}\">{$p}</a>";
            }
        }
        
        // thin out the links (optional)
        for( $i = count( $tmp ) - 3; $i > 1; $i-- ) {
            if( abs( $page - $i - 1 ) > 2 ) {
                unset( $tmp[ $i ] );
            }
        }
        
        // display page navigation iff data covers more than one page
        if( count( $tmp ) > 1 ) {
            $ajax_pagination_class = $ajax_pagination ? ' as-ajax-pagination' : '';
            echo "<p class='d-block pagination py-2 m-0 text-".$alignment." pagination".$ajax_pagination_class."'>";
            
            $lastlink = 0;
            foreach( $tmp as $i => $link ) {
                if( $i > $lastlink + 1 ) {
                    echo " ... "; // where one or more links have been omitted
                } else if( $i ) {
                    echo " ";
                }
                echo $link;
                $lastlink = $i;
            }
            
            echo "</p>";
        }
        
        return true;
    }
    
    public static function asDisplayPaginationAjaxParams() {
        $params = [];
        
        if( !empty( $_POST['type'] ) ) {
            $params['type'] = $_POST['type'];
        }
        if( !empty( $_POST['page'] ) ) {
            $params['page'] = $_POST['page'];
        }
        if( !empty( $_POST['element_id'] ) ) {
            $params[ $_POST['type'].'_id' ] = $_POST['element_id'];
        }
        if( !empty( $_POST['order'] ) ) {
            $params['order'] = $_POST['order'];
        }
        if( !empty( $_POST['orderby'] ) ) {
            $params['orderby'] = $_POST['orderby'];
        }
        if( !empty( $_POST['search'] ) ) {
            $params['search'] = $_POST['search'];
        }
        
        return $params;
    }
    
    /**
     * @param int    $quantity
     * @param int    $itemsPerPage
     * @param string $alignment
     * @param bool   $ajax_pagination
     *
     * @return bool|string
     */
    public static function paginationBrowsing( $quantity = 0, $itemsPerPage = 24, $alignment = 'center', $ajax_pagination = false ) {
        return self::asDisplayPagination( $quantity, $itemsPerPage, $alignment, $ajax_pagination );
    }
    
    public static function searchAndFilter( $wpQuery, $itemsPerPage = 10 ) {
        $NUMPERPAGE = $itemsPerPage; // max. number of items to display per page
        $this_page  = preg_replace( '/\?.*/', '', $_SERVER['REQUEST_URI'] );
        $wpQuery->found_posts;
        $data        = range( 1, $wpQuery->found_posts ); // data array to be paginated
        $num_results = count( $data );
        
        # Original PHP code by Chirp Internet: www.chirp.com.au
        # Please acknowledge use of this code by including this header.
        
        if( !isset( $_GET['sf_paged'] ) || !$page = intval( $_GET['sf_paged'] ) ) {
            $page = 1;
        }
        // extra variables to append to navigation links (optional)
        $linkextra = [];
        if( isset( $_GET['_sf_s'] ) && $var1 = $_GET['_sf_s'] ) { // repeat as needed for each extra variable
            $linkextra[] = "_sf_s=".urlencode( $var1 );
        }
        if( isset( $_GET['_sft_set_type'] ) && $var1 = $_GET['_sft_set_type'] ) { // repeat as needed for each extra variable
            $linkextra[] = "_sft_set_type=".urlencode( $var1 );
        }
        if( isset( $_GET['design_status'] ) && $var1 = $_GET['design_status'] ) { // repeat as needed for each extra variable
            $linkextra[] = "design_status=".urlencode( $var1 );
        }
        
        if( isset( $_GET['artist_id'] ) && $var1 = $_GET['artist_id'] ) { // repeat as needed for each extra variable
            $linkextra[] = "artist_id=".urlencode( $var1 );
        }
        $linkextra = implode( "&amp;", $linkextra );
        if( $linkextra ) {
            $linkextra .= "&amp;";
        }
        
        // build array containing links to all pages
        $tmp = [];
        for( $p = 1, $i = 0; $i < $num_results; $p++, $i += $NUMPERPAGE ) {
            if( $page == $p ) {
                // current page shown as bold, no link
                $tmp[] = "<b>{$p}</b>";
            } else {
                $tmp[] = "<a href=\"{$this_page}?{$linkextra}sf_paged={$p}\">{$p}</a>";
            }
        }
        
        // thin out the links (optional)
        for( $i = count( $tmp ) - 3; $i > 1; $i-- ) {
            if( abs( $page - $i - 1 ) > 2 ) {
                unset( $tmp[ $i ] );
            }
        }
        
        // display page navigation iff data covers more than one page
        if( count( $tmp ) > 1 ) {
            echo "<p class='search__pagination'>";
            
            if( $page > 1 ) {
                // display 'Prev' link
                echo "<a href=\"{$this_page}?{$linkextra}sf_paged=".( $page - 1 )."\">&laquo; Prev</a> | ";
            } else {
                echo "Page ";
            }
            
            $lastlink = 0;
            foreach( $tmp as $i => $link ) {
                if( $i > $lastlink + 1 ) {
                    echo " ... "; // where one or more links have been omitted
                } else if( $i ) {
                    echo " | ";
                }
                echo $link;
                $lastlink = $i;
            }
            
            if( $page <= $lastlink ) {
                // display 'Next' link
                echo " | <a href=\"{$this_page}?{$linkextra}sf_paged=".( $page + 1 )."\">Next &raquo;</a>";
            }
            
            echo "</p>\n\n";
        }
    }
    
}
