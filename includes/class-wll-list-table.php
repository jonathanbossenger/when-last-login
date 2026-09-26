<?php
/**
 * Login Records List Table
 *
 * Displays login records using WP_List_Table API.
 *
 * @package When_Last_Login
 * @since   1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_admin() ) {
	return;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class WLL_List_Table
 *
 * Displays login records in the admin.
 *
 * @since 1.3.0
 */
class WLL_List_Table extends WP_List_Table {

	/**
	 * Constructor.
	 *
	 * @since  1.3.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'wll_login_record',
				'plural'   => 'wll_login_records',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Get columns for the list table.
	 *
	 * @since  1.3.0
	 * @return array Columns array.
	 */
	public function get_columns() {
		return array(
			'cb'         => '<input type="checkbox" />',
			'user'       => __( 'User', 'when-last-login' ),
			'login_time' => __( 'Login Time', 'when-last-login' ),
			'ip_address' => __( 'IP Address', 'when-last-login' ),
			'browser'    => __( 'Browser', 'when-last-login' ),
			'os'         => __( 'Operating System', 'when-last-login' ),
			'device'     => __( 'Device', 'when-last-login' ),
		);
	}

	/**
	 * Get sortable columns.
	 *
	 * @since  1.3.0
	 * @return array Sortable columns.
	 */
	public function get_sortable_columns() {
		return array(
			'user'       => array( 'user_id', false ),
			'login_time' => array( 'login_time', true ),
			'ip_address' => array( 'ip_address', false ),
			'browser'    => array( 'browser', false ),
			'os'         => array( 'os', false ),
			'device'     => array( 'device', false ),
		);
	}

	/**
	 * Column default.
	 *
	 * @since  1.3.0
	 *
	 * @param  object $item        Row item.
	 * @param  string $column_name Column name.
	 * @return string             Column value.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'login_time':
				return esc_html( get_date_from_gmt( $item->login_time, 'Y-m-d H:i:s' ) );

			case 'ip_address':
				if ( ! empty( $item->ip_address ) ) {
					return esc_html( $item->ip_address );
				}
				return esc_html__( 'Not Recorded', 'when-last-login' );

			case 'browser':
			case 'os':
			case 'device':
				return ! empty( $item->$column_name ) ? esc_html( $item->$column_name ) : '—';

			default:
				return isset( $item->$column_name ) ? esc_html( $item->$column_name ) : '';
		}
	}

	/**
	 * Column checkbox.
	 *
	 * @since  1.3.0
	 *
	 * @param  object $item Row item.
	 * @return string      Checkbox HTML.
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="record_id[]" value="%d" />',
			$item->id
		);
	}

	/**
	 * Column user.
	 *
	 * @since  1.3.0
	 *
	 * @param  object $item Row item.
	 * @return string      User HTML.
	 */
	public function column_user( $item ) {
		$user = get_user_by( 'id', $item->user_id );

		if ( ! $user ) {
			return sprintf(
				/* translators: %d: User ID */
				esc_html__( 'User #%d (deleted)', 'when-last-login' ),
				$item->user_id
			);
		}

		return sprintf(
			'<a href="%s">%s</a>',
			esc_url( add_query_arg( 'user_id', $user->ID, admin_url( 'user-edit.php' ) ) ),
			esc_html( $user->display_name )
		);
	}

	/**
	 * Bulk actions.
	 *
	 * @since  1.3.0
	 * @return array Bulk actions.
	 */
	public function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', 'when-last-login' ),
		);
	}

	/**
	 * Process bulk actions.
	 *
	 * @since  1.3.0
	 *
	 * @return int Number of records deleted.
	 */
	public function process_bulk_action() {
		if ( 'delete' !== $this->current_action() ) {
			return 0;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to delete login records.', 'when-last-login' ) );
		}

		$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'bulk-' . $this->_args['plural'] ) ) {
			wp_die( esc_html__( 'Invalid nonce', 'when-last-login' ) );
		}

		$record_ids = isset( $_REQUEST['record_id'] ) ? array_map( 'intval', (array) $_REQUEST['record_id'] ) : array();

		if ( ! empty( $record_ids ) ) {
			return WLL_DB::delete_records( $record_ids );
		}

		return 0;
	}

	/**
	 * Prepare items for display.
	 *
	 * @since  1.3.0
	 */
	public function prepare_items() {
		global $wpdb;

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$per_page = $this->get_items_per_page( 'wll_records_per_page', 20 );
		$page     = $this->get_pagenum();
		$offset   = ( $page - 1 ) * $per_page;

		$orderby = ! empty( $_REQUEST['orderby'] ) ? sanitize_key( wp_unslash( $_REQUEST['orderby'] ) ) : 'login_time';
		$order   = ! empty( $_REQUEST['order'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) ) : 'DESC';

		$valid_orderby = array( 'user_id', 'login_time', 'ip_address', 'browser', 'os', 'device' );
		if ( ! in_array( $orderby, $valid_orderby, true ) ) {
			$orderby = 'login_time';
		}

		if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
			$order = 'DESC';
		}

		$records_table = WLL_DB::get_table_name( WLL_DB::LOGIN_RECORDS_TABLE );
		$where         = array();
		$values        = array();
		$search        = ! empty( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';

		if ( '' !== $search ) {
			$like         = '%' . $wpdb->esc_like( $search ) . '%';
			$search_parts = array(
				'ip_address LIKE %s',
				'browser LIKE %s',
				'os LIKE %s',
				'device LIKE %s',
			);
			$values[]     = $like;
			$values[]     = $like;
			$values[]     = $like;
			$values[]     = $like;

			$user_ids = $this->get_search_user_ids( $search );
			if ( ! empty( $user_ids ) ) {
				$placeholders   = implode( ',', array_fill( 0, count( $user_ids ), '%d' ) );
				$search_parts[] = "user_id IN ($placeholders)";
				foreach ( $user_ids as $user_id ) {
					$values[] = $user_id;
				}
			}

			$where[] = '(' . implode( ' OR ', $search_parts ) . ')';
		}

		$where_sql = ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '';
		$count_sql = "SELECT COUNT(*) FROM $records_table $where_sql";

		if ( ! empty( $values ) ) {
			$total_items = (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $values ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		} else {
			$total_items = (int) $wpdb->get_var( $count_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		$query_sql    = "SELECT * FROM $records_table $where_sql ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
		$query_values = array_merge( $values, array( $per_page, $offset ) );

		$this->items = $wpdb->get_results( $wpdb->prepare( $query_sql, $query_values ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => $total_items ? (int) ceil( $total_items / $per_page ) : 0,
			)
		);
	}

	/**
	 * Resolve user IDs matching a search term.
	 *
	 * @since  1.3.0
	 *
	 * @param  string $search Search term.
	 * @return int[]
	 */
	private function get_search_user_ids( $search ) {
		$ids = array();

		if ( is_numeric( $search ) ) {
			$ids[] = absint( $search );
		}

		foreach ( array( 'login', 'email', 'slug' ) as $field ) {
			$user = get_user_by( $field, $search );
			if ( $user ) {
				$ids[] = (int) $user->ID;
			}
		}

		$users = get_users(
			array(
				'search'         => '*' . $search . '*',
				'search_columns' => array( 'user_login', 'user_email', 'user_nicename', 'display_name' ),
				'fields'         => 'ID',
				'number'         => 50,
			)
		);

		foreach ( $users as $user_id ) {
			$ids[] = (int) $user_id;
		}

		return array_values( array_unique( array_filter( $ids ) ) );
	}

	/**
	 * Message when no items found.
	 *
	 * @since  1.3.0
	 */
	public function no_items() {
		esc_html_e( 'No login records found.', 'when-last-login' );
	}
}
