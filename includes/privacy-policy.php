<?php
/**
 * GDPR / privacy exporters and erasers.
 *
 * @package When_Last_Login
 * @since   1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the default suggested privacy policy content.
 *
 * @return string The default policy content.
 */
function wll_get_default_privacy_content() {
	$content = '<h2>' . esc_html__( 'What personal data we collect and why we collect it', 'when-last-login' ) . '</h2>';

	$content .= '<p>' . esc_html__( 'An IP address will be collected and anonymized before storing it to the database. Additional data such as login time and number of logins will be stored for analytical and security reasons.', 'when-last-login' ) . '</p>';

	$content .= '<h2>' . esc_html__( 'How long we retain your data', 'when-last-login' ) . '</h2>';

	$content .= '<p>' . esc_html__( 'Subscriber information is retained in the local database indefinitely for analytic purposes and for future export. Data is retained until requested or user has been deleted.', 'when-last-login' ) . '</p>';

	$content .= '<h2>' . esc_html__( 'Where we send your data', 'when-last-login' ) . '</h2>';

	$content .= '<p>' . esc_html__( 'When Last Login does not send any user data outside of your site by default.', 'when-last-login' ) . '</p>';

	$content .= '<p>' . esc_html__( 'If you have any Add Ons installed to send login data to a 3rd part service such as Zapier, user info may be passed to these external services. These services may be located abroad.', 'when-last-login' ) . '</p>';

	$content = apply_filters( 'wll_default_privacy_text', $content );

	return $content;
}

/**
 * Add the suggested privacy policy text to the policy postbox.
 */
function wll_add_suggested_privacy_content() {
	if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
		$content = wll_get_default_privacy_content();
		wp_add_privacy_policy_content( esc_html__( 'When Last Login', 'when-last-login' ), $content );
	}
}
add_action( 'admin_init', 'wll_add_suggested_privacy_content', 20 );

/**
 * Register the personal data exporter.
 *
 * @param array $exporters Exporters.
 * @return array
 */
function wll_register_exporters( $exporters ) {
	$exporters[] = array(
		'exporter_friendly_name' => esc_html__( 'When Last Login', 'when-last-login' ),
		'callback'               => 'wll_user_data_exporter',
	);
	return $exporters;
}
add_filter( 'wp_privacy_personal_data_exporters', 'wll_register_exporters' );

/**
 * Export user meta, summary row, and login records.
 *
 * @param string $email_address User email.
 * @param int    $page          Page number.
 * @return array
 */
function wll_user_data_exporter( $email_address, $page = 1 ) {
	$export_items = array();
	$done         = true;
	$user         = get_user_by( 'email', $email_address );

	if ( ! $user || ! $user->ID ) {
		return array(
			'data' => $export_items,
			'done' => true,
		);
	}

	$page     = max( 1, absint( $page ) );
	$per_page = 100;

	if ( 1 === $page ) {
		$item_id     = "when-last-login-{$user->ID}";
		$group_id    = 'when-last-login';
		$group_label = esc_html__( 'Plugin: When Last Login', 'when-last-login' );
		$data        = array();

		$login_time = get_user_meta( $user->ID, 'when_last_login', true );
		if ( $login_time ) {
			$data[] = array(
				'name'  => esc_html__( 'Last Login Time', 'when-last-login' ),
				'value' => is_numeric( $login_time ) ? gmdate( 'Y-m-d H:i:s', (int) $login_time ) : $login_time,
			);
		}

		$login_count = get_user_meta( $user->ID, 'when_last_login_count', true );
		if ( $login_count ) {
			$data[] = array(
				'name'  => esc_html__( 'Login Count', 'when-last-login' ),
				'value' => $login_count,
			);
		}

		$ip_address = get_user_meta( $user->ID, 'wll_user_ip_address', true );
		if ( $ip_address ) {
			$data[] = array(
				'name'  => esc_html__( 'Last Login IP Address', 'when-last-login' ),
				'value' => $ip_address,
			);
		}

		if ( class_exists( 'WLL_DB' ) ) {
			$summary = WLL_DB::get_last_login( $user->ID );
			if ( $summary ) {
				$data[] = array(
					'name'  => esc_html__( 'Summary Last Login', 'when-last-login' ),
					'value' => $summary->last_login,
				);
				$data[] = array(
					'name'  => esc_html__( 'Summary Login Count', 'when-last-login' ),
					'value' => $summary->login_count,
				);
			}
		}

		$data = apply_filters( 'wll_data_privacy_export', $data );

		if ( ! empty( $data ) ) {
			$export_items[] = array(
				'group_id'    => $group_id,
				'group_label' => $group_label,
				'item_id'     => $item_id,
				'data'        => $data,
			);
		}
	}

	if ( class_exists( 'WLL_DB' ) ) {
		$records = WLL_DB::get_login_history(
			array(
				'user_id'  => $user->ID,
				'per_page' => $per_page,
				'page'     => $page,
				'orderby'  => 'login_time',
				'order'    => 'DESC',
			)
		);

		foreach ( $records as $record ) {
			$record_data = array(
				array(
					'name'  => esc_html__( 'Login Time', 'when-last-login' ),
					'value' => $record->login_time,
				),
			);

			if ( ! empty( $record->ip_address ) ) {
				$record_data[] = array(
					'name'  => esc_html__( 'IP Address', 'when-last-login' ),
					'value' => $record->ip_address,
				);
			}
			if ( ! empty( $record->browser ) ) {
				$record_data[] = array(
					'name'  => esc_html__( 'Browser', 'when-last-login' ),
					'value' => $record->browser,
				);
			}
			if ( ! empty( $record->os ) ) {
				$record_data[] = array(
					'name'  => esc_html__( 'Operating System', 'when-last-login' ),
					'value' => $record->os,
				);
			}
			if ( ! empty( $record->device ) ) {
				$record_data[] = array(
					'name'  => esc_html__( 'Device', 'when-last-login' ),
					'value' => $record->device,
				);
			}

			$export_items[] = array(
				'group_id'    => 'when-last-login-records',
				'group_label' => esc_html__( 'Plugin: When Last Login Records', 'when-last-login' ),
				'item_id'     => 'when-last-login-record-' . absint( $record->id ),
				'data'        => $record_data,
			);
		}

		$done = count( $records ) < $per_page;
	}

	return array(
		'data' => $export_items,
		'done' => $done,
	);
}

/**
 * Register the personal data eraser.
 *
 * @param array $erasers Erasers.
 * @return array
 */
function wll_register_erasers( $erasers = array() ) {
	$erasers[] = array(
		'eraser_friendly_name' => esc_html__( 'When Last Login', 'when-last-login' ),
		'callback'             => 'wll_user_data_eraser',
	);
	return $erasers;
}
add_filter( 'wp_privacy_personal_data_erasers', 'wll_register_erasers' );

/**
 * Erase user meta and custom table rows.
 *
 * @param string $email_address User email.
 * @param int    $page          Page number.
 * @return array
 */
function wll_user_data_eraser( $email_address, $page = 1 ) {
	if ( empty( $email_address ) ) {
		return array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	$user            = get_user_by( 'email', $email_address );
	$messages        = array();
	$items_removed   = false;
	$items_retained  = false;

	if ( $user && $user->ID ) {
		$meta_keys = array(
			'when_last_login'       => __( 'Your last login timestamp was unable to be removed at this time.', 'when-last-login' ),
			'when_last_login_count' => __( 'Your login count was unable to be removed at this time.', 'when-last-login' ),
			'wll_user_ip_address'   => __( 'Your IP address was unable to be removed at this time.', 'when-last-login' ),
		);

		foreach ( $meta_keys as $meta_key => $error_message ) {
			if ( ! metadata_exists( 'user', $user->ID, $meta_key ) ) {
				continue;
			}

			if ( delete_user_meta( $user->ID, $meta_key ) ) {
				$items_removed = true;
			} else {
				$messages[]      = esc_html( $error_message );
				$items_retained  = true;
			}
		}

		if ( class_exists( 'WLL_DB' ) ) {
			$deleted_rows = WLL_DB::delete_user_data( $user->ID );
			if ( $deleted_rows > 0 ) {
				$items_removed = true;
			}
		}
	}

	return array(
		'items_removed'  => $items_removed,
		'items_retained' => $items_retained,
		'messages'       => $messages,
		'done'           => true,
	);
}
