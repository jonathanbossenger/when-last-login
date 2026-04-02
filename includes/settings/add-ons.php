<?php
/**
 * Add-ons / Extensions page for When Last Login.
 *
 * Add-ons are hardcoded here for reliability, security, and full control
 * over layout and styling.  Edit the $wll_add_ons array below to
 * add, remove, or update products.
 */

$wll_add_ons = apply_filters(
	'wll_add_ons_list',
	array(
		array(
			'name'        => __( 'User Statistics', 'when-last-login' ),
			'description' => __( 'View detailed login statistics per user including login counts, frequency charts, and historical trends.', 'when-last-login' ),
			'icon'        => 'dashicons-chart-bar',
			'url'         => 'https://yoohooplugins.com/plugins/when-last-login-user-statistics/',
			'badge'       => '',
		),
		array(
			'name'        => __( 'Login Notifications', 'when-last-login' ),
			'description' => __( 'Send email notifications when users log in or when login activity is detected on your site.', 'when-last-login' ),
			'icon'        => 'dashicons-email',
			'url'         => 'https://yoohooplugins.com/plugins/when-last-login-notifications/',
			'badge'       => '',
		),
		array(
			'name'        => __( 'Login Records', 'when-last-login' ),
			'description' => __( 'View a detailed log of every login on your site including IP address, browser, operating system, and device type.', 'when-last-login' ),
			'icon'        => 'dashicons-list-view',
			'url'         => 'https://yoohooplugins.com/plugins/when-last-login-login-records/',
			'badge'       => __( 'Popular', 'when-last-login' ),
		),
		array(
			'name'        => __( 'Lock Out', 'when-last-login' ),
			'description' => __( 'Automatically lock out users after a set number of failed login attempts to protect against brute force attacks.', 'when-last-login' ),
			'icon'        => 'dashicons-lock',
			'url'         => 'https://yoohooplugins.com/plugins/when-last-login-lock-out/',
			'badge'       => __( 'New', 'when-last-login' ),
		),
	)
);
?>

<style>
	/* ── Add-ons page wrapper ── */
	.wll-addons-page {
		max-width: 1200px;
		margin: 0 auto;
	}

	.wll-addons-page__intro {
		margin-bottom: 24px;
		color: #50575e;
		font-size: 14px;
		line-height: 1.6;
	}

	/* ── Card grid ── */
	.wll-addons-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
		gap: 20px;
	}

	/* ── Individual card ── */
	.wll-addon-card {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 8px;
		padding: 24px;
		display: flex;
		flex-direction: column;
		transition: box-shadow 0.2s ease, border-color 0.2s ease;
		position: relative;
	}

	.wll-addon-card:hover {
		border-color: #2271b1;
		box-shadow: 0 2px 8px rgba(0,0,0,0.08);
	}

	/* ── Badge (Popular, New, etc.) ── */
	.wll-addon-card__badge {
		position: absolute;
		top: 12px;
		right: 12px;
		background: #2271b1;
		color: #fff;
		font-size: 11px;
		font-weight: 600;
		text-transform: uppercase;
		padding: 3px 8px;
		border-radius: 4px;
		letter-spacing: 0.5px;
	}

	.wll-addon-card__badge--green {
		background: #00a32a;
	}

	/* ── Icon ── */
	.wll-addon-card__icon {
		width: 48px;
		height: 48px;
		background: #f0f6fc;
		border-radius: 8px;
		display: flex;
		align-items: center;
		justify-content: center;
		margin-bottom: 16px;
	}

	.wll-addon-card__icon .dashicons {
		font-size: 28px;
		width: 28px;
		height: 28px;
		color: #2271b1;
	}

	/* ── Typography ── */
	.wll-addon-card__title {
		margin: 0 0 8px;
		font-size: 18px;
		font-weight: 600;
		color: #1d2327;
	}

	.wll-addon-card__desc {
		margin: 0 0 20px;
		font-size: 13px;
		color: #50575e;
		line-height: 1.6;
		flex-grow: 1;
	}

	/* ── CTA button ── */
	.wll-addon-card__cta {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		text-decoration: none;
		font-size: 13px;
		font-weight: 600;
		color: #2271b1;
		transition: color 0.15s ease;
	}

	.wll-addon-card__cta:hover {
		color: #135e96;
	}

	.wll-addon-card__cta .dashicons {
		font-size: 16px;
		width: 16px;
		height: 16px;
		transition: transform 0.15s ease;
	}

	.wll-addon-card__cta:hover .dashicons {
		transform: translateX(3px);
	}

	/* ── Responsive ── */
	@media (max-width: 640px) {
		.wll-addons-grid {
			grid-template-columns: 1fr;
		}
	}
</style>

<div class="wll-addons-page">

	<p class="wll-addons-page__intro">
		<?php esc_html_e( 'Extend When Last Login with these optional add-ons. Each add-on is a separate plugin that works alongside the core plugin.', 'when-last-login' ); ?>
	</p>

	<div class="wll-addons-grid">
		<?php foreach ( $wll_add_ons as $addon ) : ?>
			<div class="wll-addon-card">

				<?php if ( ! empty( $addon['badge'] ) ) : ?>
					<span class="wll-addon-card__badge <?php echo strtolower( $addon['badge'] ) === 'new' ? 'wll-addon-card__badge--green' : ''; ?>">
						<?php echo esc_html( $addon['badge'] ); ?>
					</span>
				<?php endif; ?>

				<div class="wll-addon-card__icon">
					<span class="dashicons <?php echo esc_attr( $addon['icon'] ); ?>"></span>
				</div>

				<h3 class="wll-addon-card__title">
					<?php echo esc_html( $addon['name'] ); ?>
				</h3>

				<p class="wll-addon-card__desc">
					<?php echo esc_html( $addon['description'] ); ?>
				</p>

				<a class="wll-addon-card__cta" href="<?php echo esc_url( $addon['url'] ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Learn more', 'when-last-login' ); ?>
					<span class="dashicons dashicons-arrow-right-alt"></span>
				</a>

			</div>
		<?php endforeach; ?>
	</div>

</div>