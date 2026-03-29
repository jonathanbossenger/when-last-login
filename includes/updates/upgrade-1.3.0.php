<?php
/**
 * Upgrade script for version 1.3.0
 *
 * Migrates login records from custom post type to custom table.
 *
 * @package When_Last_Login
 * @since   1.3.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run the 1.3.0 upgrade.
 *
 * @since 1.3.0
 */
function wll_upgrade_1_3_0() {
	global $wpdb;

	// Check if already upgraded.
	$db_version = get_option( 'wll_db_version', '1.0.0' );
	if ( version_compare( $db_version, '1.3.0', '>=' ) ) {
		return;
	}

	// Create the login records table.
	$table_name = $wpdb->prefix . 'wll_login_records';
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

	// Verify table creation.
	$table_exists = $wpdb->get_var(
		$wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name )
	) === $table_name;

	if ( ! $table_exists ) {
		error_log( 'WLL: Failed to create wll_login_records table during 1.3.0 upgrade' );
		return;
	}

	// Count posts to migrate.
	$args = array(
		'post_type'      => 'wll_records',
		'post_status'     => 'any',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	);
	$query = new WP_Query( $args );
	$posts_count = $query->found_posts;

	if ( $posts_count > 0 ) {
		// Store migration status.
		update_option( 'wll_migration_status', array(
			'total'     => $posts_count,
			'migrated'  => 0,
			'started'   => current_time( 'mysql' ),
			'status'    => 'pending',
		) );

		// Schedule migration.
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
	update_option( 'wll_db_version', '1.3.0' );

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
 * Migrate a batch of records from posts to table.
 *
 * @since 1.3.0
 */
function wll_migrate_records_batch() {
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

	$table_name = $wpdb->prefix . 'wll_login_records';
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

		// Insert into table.
		$wpdb->insert(
			$table_name,
			array(
				'user_id'    => $user_id,
				'login_time' => $login_time,
				'ip_address' => $ip_address,
			),
			array( '%d', '%s', '%s' )
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

// Hook up the migration.
add_action( 'wll_migrate_login_records', 'wll_migrate_records_batch' );