<?php
/**
 * Upgrade script for version 1.3.0
 *
 * Kept as a thin wrapper around WLL_DB so there is a single upgrade path.
 *
 * @package When_Last_Login
 * @since   1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run the 1.3.0 upgrade.
 *
 * @since 1.3.0
 */
function wll_upgrade_1_3_0() {
	if ( class_exists( 'WLL_DB' ) ) {
		WLL_DB::check_db_version();
	}
}
