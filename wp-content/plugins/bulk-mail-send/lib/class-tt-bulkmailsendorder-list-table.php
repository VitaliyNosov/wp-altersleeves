<?php
/**
 * Bulk Mail Send
 *
 * @package    Bulk Mail Send
 * @subpackage BulkMailSendOrder List Table
 * reference   Custom List Table Example
 *             https://wordpress.org/plugins/custom-list-table-example/
/*
	Copyright (c) 2020- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
	require_once( ABSPATH . 'wp-admin/includes/screen.php' );
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	require_once( ABSPATH . 'wp-admin/includes/template.php' );
}

/** ==================================================
 * List table
 */
class TT_BulkMailSendOrder_List_Table extends WP_List_Table {

	/** ==================================================
	 * Max items
	 *
	 * @var $max_items  max_items.
	 */
	public $max_items;

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		if ( ! class_exists( 'BulkMailSend' ) ) {
			require_once dirname( __FILE__ ) . '/class-bulkmailsend.php';
		}

		global $status, $page;
		/* Set parent defaults */
		parent::__construct(
			array(
				'singular'  => 'mails',
				'ajax'      => false,
			)
		);

	}

	/** ==================================================
	 * Read data
	 *
	 * @since 1.00
	 */
	private function read_data() {

		$search_text = get_user_option( 'bulkmailsendorder_search_text', get_current_user_id() );
		$order_product = get_user_option( 'bulkmailsendorder_product', get_current_user_id() );
		if ( ! $order_product ) {
			$order_product = null;
		}
		if ( isset( $_POST['bulk-mail-send-order-filter'] ) && ! empty( $_POST['bulk-mail-send-order-filter'] ) ) {
			if ( check_admin_referer( 'bmsu_order_filter', 'bulk_media_send_order_filter' ) ) {
				if ( ! empty( $_POST['search_text'] ) ) {
					$search_text = sanitize_text_field( wp_unslash( $_POST['search_text'] ) );
					update_user_option( get_current_user_id(), 'bulkmailsendorder_search_text', $search_text );
				} else {
					delete_user_option( get_current_user_id(), 'bulkmailsendorder_search_text' );
					$search_text = null;
				}
				if ( ! empty( $_POST['product'] ) ) {
					$order_product = sanitize_text_field( wp_unslash( $_POST['product'] ) );
					update_user_option( get_current_user_id(), 'bulkmailsendorder_product', $order_product );
				} else {
					delete_user_option( get_current_user_id(), 'bulkmailsendorder_product' );
					$order_product = null;
				}
			}
		}

		global $wpdb;

		$query = new WC_Order_Query(
			array(
				'limit' => -1,
				'orderby' => 'date',
				'order' => 'desc',
				'return' => 'ids',
			)
		);
		$orders = $query->get_orders();

		$order_array = array();
		$tmp_email = array();
		$downloadables = array();
		foreach ( $orders as $order_id ) {
			$the_order = wc_get_order( $order_id );
			if ( is_a( $the_order, 'WC_Order_Refund' ) ) {
				$the_order = wc_get_order( $the_order->get_parent_id() );
			}
			$billing = array();
			$billing['first_name'] = $the_order->get_billing_first_name();
			$billing['last_name'] = $the_order->get_billing_last_name();
			$billing['email'] = $the_order->get_billing_email();
			$date = $the_order->get_date_created();
			$products_name = array();
			foreach ( $the_order->get_items() as $item_id => $item ) {
				$product_id = $item->get_product_id();
				$products_name[] = get_the_title( $product_id );
			}
			if ( ! empty( $billing ) && is_null( $order_product ) || in_array( $order_product, $products_name ) ) {
				$order_array[ $order_id ]['order'] = $order_id;
				$order_array[ $order_id ]['name'] = $billing['first_name'] . ' ' . $billing['last_name'];
				$order_array[ $order_id ]['email'] = $billing['email'];
				$order_array[ $order_id ]['date'] = $date;
				$order_array[ $order_id ]['status'] = $the_order->get_status();
				$order_array[ $order_id ]['product'] = $products_name;
			}
		}

		$listtable_array = array();
		if ( ! empty( $order_array ) ) {

			$count = 0;
			foreach ( $order_array as $key => $value ) {

				$name  = '<span style="float: left; margin: 5px;">' . get_avatar( $value['email'], 32, null, false, array() ) . '</span>';
				$name .= '<div style="overflow: hidden;">' . $value['name'] . '</div>';
				$datetime = mysql2date( 'Y-m-d H:i:s', $value['date'], true );
				$products = null;
				foreach ( $value['product'] as $value2 ) {
					$products .= '<div>' . $value2 . '</div>';
				}

				$search = false;
				if ( $search_text ) {
					if ( false !== strpos( $value['order'], $search_text ) ) {
						$search = true;
					}
					if ( false !== strpos( $value['name'], $search_text ) ) {
						$search = true;
					}
					if ( false !== strpos( $value['email'], $search_text ) ) {
						$search = true;
					}
					if ( false !== strpos( $datetime, $search_text ) ) {
						$search = true;
					}
					if ( false !== strpos( $value['status'], $search_text ) ) {
						$search = true;
					}
					if ( in_array( $search_text, $value['product'] ) ) {
						$search = true;
					}
				} else {
					$search = true;
				}

				if ( $search ) {
					$listtable_array[] = array(
						'ID'      => $count,
						'order'   => $value['order'],
						'name'    => $name,
						'mail'    => $value['email'],
						'date'    => $datetime,
						'status'  => $value['status'],
						'product' => $products,
					);
					++$count;
				}
			}
		}

		return $listtable_array;

	}

	/** ==================================================
	 * Column default
	 *
	 * @param array  $item  item.
	 * @param string $column_name  column_name.
	 * @since 1.00
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'order':
				return $item[ $column_name ];
			case 'name':
				return $item[ $column_name ];
			case 'mail':
				return $item[ $column_name ];
			case 'date':
				return $item[ $column_name ];
			case 'status':
				return $item[ $column_name ];
			case 'product':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); /* Show the whole array for troubleshooting purposes */
		}
	}

	/** ==================================================
	 * Column title
	 *
	 * @param array $item  item.
	 * @since 1.00
	 */
	public function column_title( $item ) {
		/* Return the title contents */
		return sprintf(
			'%1$s <span style="color:silver"></span>',
			/*$1%s*/ $item['order']
		);
	}

	/** ==================================================
	 * Column checkbox
	 *
	 * @param array $item  item.
	 * @since 1.00
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[%2$s]" value="%3$s" form="selectmailsend_order_forms" />',
			/*%1$s*/ $this->_args['singular'],
			/*%2$s*/ $item['order'],
			/*%3$s*/ $item['mail']
		);
	}

	/** ==================================================
	 * Get Columns
	 *
	 * @since 1.00
	 */
	public function get_columns() {
		$columns = array(
			'cb'    => '<input type="checkbox" />', /* Render a checkbox instead of text */
			'order' => __( 'Order', 'woocommerce' ),
			'name'  => __( 'Name' ),
			'mail'  => __( 'Email' ),
			'date'  => __( 'Date' ),
			'status'  => __( 'Status', 'woocommerce' ),
			'product'  => __( 'Product', 'woocommerce' ),
		);
		return $columns;
	}

	/** ==================================================
	 * Get Sortable Columns
	 *
	 * @since 1.00
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'order' => array( 'order', false ),
			'name'  => array( 'name', false ),
			'mail'  => array( 'mail', false ),
			'date'  => array( 'date', false ),
			'status'  => array( 'status', false ),
			'product'  => array( 'product', false ),
		);
		return $sortable_columns;
	}

	/** ************************************************************************
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here...
	 *
	 * @global WPDB $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 **************************************************************************/
	public function prepare_items() {

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = get_user_option( 'bms_order_per_page', get_current_user_id() );

		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$data = $this->read_data();
		do_action( 'bms_order_product_filter_form' );

		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 *
		 * @param array $a  a.
		 * @param array $b  b.
		 */
		function usort_reorder( $a, $b ) {
			/* If no sort, default to date */
			if ( isset( $_REQUEST['orderby'] ) && ! empty( $_REQUEST['orderby'] ) ) {
				$orderby = sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) );
			} else {
				$orderby = 'date';
			}
			/* If no order, default to desc */
			if ( isset( $_REQUEST['order'] ) && ! empty( $_REQUEST['order'] ) ) {
				$order = sanitize_text_field( wp_unslash( $_REQUEST['order'] ) );
			} else {
				$order = 'desc';
			}
			$result = strcmp( $a[ $orderby ], $b[ $orderby ] ); /* Determine sort order */
			return ( 'asc' === $order ) ? $result : -$result; /* Send final sort direction to usort */
		}
		usort( $data, 'usort_reorder' );

		/***********************************************************************
		 * ---------------------------------------------------------------------
		 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
		 *
		 * In a real-world situation, this is where you would place your query.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 *
		 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		 * ---------------------------------------------------------------------
		 */

		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count( $data );
		$this->max_items = $total_items;

		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,                  /* WE have to calculate the total number of items */
				'per_page'    => $per_page,                     /* WE have to determine how many items to show on a page */
				'total_pages' => ceil( $total_items / $per_page ),   /* WE have to calculate the total number of pages */
			)
		);
	}

}


