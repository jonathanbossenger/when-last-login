<?php
/**
 * Database handler for When Last Login
 *
 * Handles table creation, migrations, and database operations.
 *
 * @package When_Last_Login
 * @since   1.3.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WLL_DB
 *
 * Database operations for When Last Login.
 *
 * @since 1.3.0
 */
class WLL_DB {

	/**
	 * Current database version.
	 *
	 * @since  1.3.0
	 * @access private
	 * @var    string
	 */
	private static $db_version = '1.3.0';

	/**
	 * Option name for database version.
	 *
	 * @since  1.3.0
	 * @access private
	 * @var    string
	 */
	const VERSION_OPTION = 'wll_db_version';

	/**
	 * Login records table name.
	 *
	 * @since  1.3.0
	 * @access private
	 * @var    string
	 */
	const LOGIN_RECORDS_TABLE = 'wll_login_records';

	/**
	 * Initialize database handler.
	 *
	 * @since  1.3.0
	 * @access public
	 */
	public static function init() {
		add_action('admin_init', array( __CLASS__, 'check_db_version' ) );
		add_action('wll_migrate_login_records', array( __CLASS__, 'migrate_batch' ) );
	}

	/**
	 * Check and run database migrations if needed.
	 *
	 * @since  1.3.0
	 * @access public
	 */
	public static function check_db_version() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$current_version = get_option( self::VERSION_OPTION, '1.0.0' );

		// Upgrade to 1.3.0 - Create login records table.
		if ( version_compare( $current_version, '1.3.0', '<' ) ) {
			self::upgrade_1_3_0();
		}

		/**
		 * Fires after database version check.
		 *
		 * @since 1.3.0
		 *
		 * @param string $current_version Current database version.
		 */
		do_action( 'wll_db_upgrade_check', $current_version );
	}

	/**
	 * Get the full table name with prefix.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @param  string $table Table name without prefix.
	 * @return string        Full table name.
	 */
	public static function get_table_name( $table = self::LOGIN_RECORDS_TABLE ) {
		global $wpdb;
		return $wpdb->prefix . $table;
	}

	/**
	 * Check if a table exists.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @param  string $table Table name (without prefix).
	 * @return bool         True if table exists.
	 */
	public static function table_exists( $table ) {
		global $wpdb;

		$table_name = self::get_table_name( $table );
		$result     = $wpdb->get_var(
			$wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name )
		);

		return $result === $table_name;
	}

	/**
	 * Create the login records table.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @return bool True on success.
	 */
	public static function create_login_records_table() {
		global $wpdb;

		$table_name = self::get_table_name( self::LOGIN_RECORDS_TABLE );
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL,
			login_time datetime NOT NULL,
			ip_address varchar(45) DEFAULT NULL,
			user_agent varchar(255) DEFAULT NULL,
			browser varchar(50) DEFAULT NULL,
			os varchar(50) DEFAULT NULL,
			device varchar(20) DEFAULT NULL,
			location_data text DEFAULT NULL,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY login_time (login_time),
			KEY user_id_login_time (user_id, login_time)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		// Verify table was created.
		return self::table_exists( self::LOGIN_RECORDS_TABLE );
	}

	/**
	 * Upgrade to version 1.3.0.
	 *
	 * Creates the login records table and schedules migration.
	 *
	 * @since  1.3.0
	 * @access private
	 */
	private static function upgrade_1_3_0() {
		// Create table.
		$table_created = self::create_login_records_table();

		if ( ! $table_created ) {
			// Log error but don't stop execution.
			error_log( 'WLL: Failed to create login_records table' );
			return;
		}

		// Check if there are records to migrate.
		$posts_count = self::count_posts_to_migrate();

		if ( $posts_count > 0 ) {
			// Store migration status.
			update_option( 'wll_migration_status', array(
				'total'     => $posts_count,
				'migrated'  => 0,
				'started'   => current_time( 'mysql' ),
				'status'    => 'pending',
			) );

			// Schedule migration if not already scheduled.
			if ( ! wp_next_scheduled( 'wll_migrate_login_records' ) ) {
				wp_schedule_single_event( time() + 30, 'wll_migrate_login_records' );
			}
		} else {
			// No posts to migrate, mark complete.
			update_option( 'wll_migration_status', array(
				'total'     => 0,
				'migrated'  => 0,
				'started'   => current_time( 'mysql' ),
				'completed' => current_time( 'mysql' ),
				'status'    => 'complete',
			) );
		}

		// Update database version.
		update_option( self::VERSION_OPTION, self::$db_version );

		/**
		 * Fires after 1.3.0 upgrade completes.
		 *
		 * @since 1.3.0
		 *
		 * @param int $posts_count Number of posts to migrate.
		 */
		do_action( 'wll_upgrade_1_3_0_complete', $posts_count );
	}

	/**
	 * Count posts to migrate.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @return int Number of posts to migrate.
	 */
	public static function count_posts_to_migrate() {
		$args = array(
			'post_type'      => 'wll_records',
			'post_status'     => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		);

		$query = new WP_Query( $args );
		return $query->found_posts;
	}

	/**
	 * Migrate a batch of records.
	 *
	 * @since  1.3.0
	 * @access public
	 */
	public static function migrate_batch() {
		global $wpdb;

		// Get migration status.
		$status = get_option( 'wll_migration_status', array() );

		if ( empty( $status ) || $status['status'] === 'complete' ) {
			return;
		}

		$batch_size = apply_filters( 'wll_migration_batch_size', 500 );

		// Get posts to migrate.
		$args = array(
			'post_type'      => 'wll_records',
			'post_status'    => 'any',
			'posts_per_page' => $batch_size,
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'fields'         => 'ids',
		);

		$query = new WP_Query( $args );
		$post_ids = $query->posts;

		if ( empty( $post_ids ) ) {
			// No more posts, migration complete.
			$status['status']    = 'complete';
			$status['completed'] = current_time( 'mysql' );
			update_option( 'wll_migration_status', $status );
			return;
		}

		$table_name = self::get_table_name( self::LOGIN_RECORDS_TABLE );
		$migrated = 0;

		foreach ( $post_ids as $post_id ) {
			$post = get_post( $post_id );

			if ( ! $post ) {
				continue;
			}

			// Extract data from post.
			$user_id    = $post->post_author;
			$login_time = $post->post_date;
			$ip_address = get_post_meta( $post_id, 'wll_user_ip_address', true );

			// Parse user agent if available.
			$user_agent = '';
			$browser    = '';
			$os         = '';
			$device     = '';

			// Insert into table.
			$wpdb->insert(
				$table_name,
				array(
					'user_id'    => $user_id,
					'login_time' => $login_time,
					'ip_address' => $ip_address,
					'user_agent' => $user_agent,
					'browser'    => $browser,
					'os'         => $os,
					'device'     => $device,
				),
				array( '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
			);

			$migrated++;
		}

		// Update status.
		$status['migrated'] += $migrated;
		$status['status']    = 'in_progress';
		update_option( 'wll_migration_status', $status );

		// Schedule next batch if more to migrate.
		if ( $migrated > 0 ) {
			wp_schedule_single_event( time() + 10, 'wll_migrate_login_records' );
		} else {
			$status['status']    = 'complete';
			$status['completed'] = current_time( 'mysql' );
			update_option( 'wll_migration_status', $status );
		}
	}

	/**
	 * Insert a login record.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @param  array $data Record data.
	 * @return int|false   Inserted ID or false on failure.
	 */
	public static function insert_login_record( $data ) {
		global $wpdb;

		$table_name = self::get_table_name( self::LOGIN_RECORDS_TABLE );

		$defaults = array(
			'user_id'       => 0,
			'login_time'    => current_time( 'mysql' ),
			'ip_address'    => '',
			'user_agent'    => '',
			'browser'       => '',
			'os'            => '',
			'device'        => '',
			'location_data' => '',
		);

		$data = wp_parse_args( $data, $defaults );

		$result = $wpdb->insert(
			$table_name,
			array(
				'user_id'       => absint( $data['user_id'] ),
				'login_time'    => sanitize_text_field( $data['login_time'] ),
				'ip_address'    => sanitize_text_field( $data['ip_address'] ),
				'user_agent'    => sanitize_text_field( $data['user_agent'] ),
				'browser'       => sanitize_text_field( $data['browser'] ),
				'os'            => sanitize_text_field( $data['os'] ),
				'device'        => sanitize_text_field( $data['device'] ),
				'location_data' => maybe_serialize( $data['location_data'] ),
			),
			array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		if ( false === $result ) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Get login records for a user.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @param  array $args Query arguments.
	 * @return array       Login records.
	 */
	public static function get_login_records( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'user_id'     => 0,
			'per_page'    => 50,
			'page'        => 1,
			'orderby'     => 'login_time',
			'order'       => 'DESC',
			'date_from'   => '',
			'date_to'     => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$table_name = self::get_table_name( self::LOGIN_RECORDS_TABLE );

		$where = 'WHERE 1=1';
		$prepare = array();

		if ( ! empty( $args['user_id'] ) ) {
			$where .= ' AND user_id = %d';
			$prepare[] = absint( $args['user_id'] );
		}

		if ( ! empty( $args['date_from'] ) ) {
			$where .= ' AND login_time >= %s';
			$prepare[] = sanitize_text_field( $args['date_from'] );
		}

		if ( ! empty( $args['date_to'] ) ) {
			$where .= ' AND login_time <= %s';
			$prepare[] = sanitize_text_field( $args['date_to'] );
		}

		$orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
		$offset = ( $args['page'] - 1 ) * $args['per_page'];

		$sql = "SELECT * FROM $table_name $where ORDER BY $orderby LIMIT %d OFFSET %d";
		$prepare[] = absint( $args['per_page'] );
		$prepare[] = absint( $offset );

		if ( ! empty( $prepare ) ) {
			$sql = $wpdb->prepare( $sql, $prepare );
		}

		return $wpdb->get_results( $sql );
	}

	/**
	 * Get login count for a user.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @param  int $user_id User ID.
	 * @return int         Login count.
	 */
	public static function get_login_count( $user_id ) {
		global $wpdb;

		$table_name = self::get_table_name( self::LOGIN_RECORDS_TABLE );

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $table_name WHERE user_id = %d",
				absint( $user_id )
			)
		);

		return absint( $count );
	}

	/**
	 * Get last login for a user.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @param  int $user_id User ID.
	 * @return object|null Last login record.
	 */
	public static function get_last_login( $user_id ) {
		global $wpdb;

		$table_name = self::get_table_name( self::LOGIN_RECORDS_TABLE );

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE user_id = %d ORDER BY login_time DESC LIMIT 1",
				absint( $user_id )
			)
		);
	}

	/**
	 * Delete old records.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @param  int $days Days to keep.
	 * @return int      Number of records deleted.
	 */
	public static function delete_old_records( $days = 90 ) {
		global $wpdb;

		$table_name = self::get_table_name( self::LOGIN_RECORDS_TABLE );
		$cutoff_date = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

		$result = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $table_name WHERE login_time < %s",
				$cutoff_date
			)
		);

		return absint( $result );
	}

	/**
	 * Delete all records.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @return int Number of records deleted.
	 */
	public static function delete_all_records() {
		global $wpdb;

		$table_name = self::get_table_name( self::LOGIN_RECORDS_TABLE );

		return $wpdb->query( "TRUNCATE TABLE $table_name" );
	}

	/**
	 * Clean up migrated posts.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @return int Number of posts deleted.
	 */
	public static function cleanup_migrated_posts() {
		$migration_status = get_option( 'wll_migration_status', array() );

		if ( empty( $migration_status ) || $migration_status['status'] !== 'complete' ) {
			return 0;
		}

		global $wpdb;

		// Delete posts and postmeta.
		$sql = $wpdb->prepare(
			"DELETE p, pm FROM {$wpdb->posts} p 
			 LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID 
			 WHERE p.post_type = %s",
			'wll_records'
		);

		$result = $wpdb->query( $sql );

		/**
		 * Fires after cleanup.
		 *
		 * @since 1.3.0
		 *
		 * @param int $result Number of posts deleted.
		 */
		do_action( 'wll_cleanup_migrated_posts', $result );

		return $result;
	}
}

// Initialize.
WLL_DB::init();