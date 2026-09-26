<?php
/**
 * Uninstall When Last Login.
 *
 * @package When_Last_Login
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$meta_keys = array(
	'when_last_login',
	'when_last_login_count',
	'wll_user_ip_address',
	'wll_consent_to_track',
	'wll_consent_to_track_date',
);

$placeholders = implode( ',', array_fill( 0, count( $meta_keys ), '%s' ) );
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->usermeta} WHERE meta_key IN ($placeholders)",
		$meta_keys
	)
);

$summary_table = $wpdb->prefix . 'when_last_login';
$records_table = $wpdb->prefix . 'wll_login_records';
$legacy_table  = $wpdb->prefix . 'wll_login_attempts';

$wpdb->query( "DROP TABLE IF EXISTS `$summary_table`" );
$wpdb->query( "DROP TABLE IF EXISTS `$records_table`" );
$wpdb->query( "DROP TABLE IF EXISTS `$legacy_table`" );

$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wll%'" );

wp_clear_scheduled_hook( 'wll_migrate_login_records' );
