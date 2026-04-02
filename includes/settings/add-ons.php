<?php
/**
 * Add-ons / Extensions page for When Last Login.
 *
 * Add-ons are hardcoded here for reliability, security, and full control
 * over layout and styling.  Edit the $wll_add_ons array below to
 * add, remove, or update products.
 */

$wll_add_ons = array(

	// Free Add Ons
	array(
		'name'        => __( 'Export User Records', 'when-last-login' ),
		'description' => __( 'Export user records into a CSV or JSON file in seconds.', 'when-last-login' ),
		'icon'        => 'dashicons-download',
		'url'         => 'https://wordpress.org/plugins/when-last-login-export-user-records/?utm_source=when-last-login&utm_medium=plugin&utm_campaign=add-ons',
		'badge_type'  => 'free',
		'badge'       => __( 'Free', 'when-last-login' ),
	),
	array(
		'name'        => __( 'Welcome Emails', 'when-last-login' ),
		'description' => __( 'Send a welcome email to your visitors when logging in for the first time.', 'when-last-login' ),
		'icon'        => 'dashicons-email-alt',
		'url'         => 'https://wordpress.org/plugins/when-last-login-welcome-email-add-on/?utm_source=when-last-login&utm_medium=plugin&utm_campaign=add-ons',
		'badge_type'  => 'free',
		'badge'       => __( 'Free', 'when-last-login' ),
	),

	// Premium Add Ons
	array(
		'name'        => __( 'User Statistics', 'when-last-login' ),
		'description' => __( 'Get detailed reports on what sort of login activity happens on your website.', 'when-last-login' ),
		'icon'        => 'dashicons-chart-bar',
		'url'         => 'https://yoohooplugins.com/plugins/when-last-login-user-statistics/?utm_source=when-last-login&utm_medium=plugin&utm_campaign=add-ons',
		'badge_type'  => 'premium',
		'badge'       => __( 'Premium', 'when-last-login' ),
	),
	array(
		'name'        => __( 'Slack Notifications', 'when-last-login' ),
		'description' => __( 'Get notified on a Slack channel whenever a user logs into your WordPress site.', 'when-last-login' ),
		'icon'        => 'dashicons-format-chat',
		'url'         => 'https://yoohooplugins.com/plugins/when-last-login-slack-notifications/?utm_source=when-last-login&utm_medium=plugin&utm_campaign=add-ons',
		'badge_type'  => 'premium',
		'badge'       => __( 'Premium', 'when-last-login' ),
	),
	array(
		'name'        => __( 'When Last Login Pro', 'when-last-login' ),
		'description' => __( 'Get access to Slack Notifications, User Statistics & the Zapier Integration at a discounted rate.', 'when-last-login' ),
		'icon'        => 'dashicons-archive',
		'url'         => 'https://yoohooplugins.com/plugins/when-last-login-pro/?utm_source=when-last-login&utm_medium=plugin&utm_campaign=add-ons',
		'badge_type'  => 'new',
		'badge'       => __( 'New', 'when-last-login' ),
	),
	array(
		'name'        => __( 'Lead Times for WooCommerce', 'when-last-login' ),
		'description' => __( 'Display a clear lead time for your WooCommerce products and help customers decide whether to place an order.', 'when-last-login' ),
		'icon'        => 'dashicons-clock',
		'url'         => 'https://yoohooplugins.com/plugins/lead-times-for-woocommerce/?utm_source=when-last-login&utm_medium=plugin&utm_campaign=add-ons',
		'badge_type'  => 'premium',
		'badge'       => __( 'Premium', 'when-last-login' ),
	),
	array(
		'name'        => __( 'Name Your Price for WooCommerce', 'when-last-login' ),
		'description' => __( 'Let customers choose the price they would like to pay for products or services directly on your WooCommerce store.', 'when-last-login' ),
		'icon'        => 'dashicons-tag',
		'url'         => 'https://yoohooplugins.com/plugins/name-your-price-woocommerce/?utm_source=when-last-login&utm_medium=plugin&utm_campaign=add-ons',
		'badge_type'  => 'premium',
		'badge'       => __( 'Premium', 'when-last-login' ),
	),
	array(
		'name'        => __( 'Paid Memberships Pro PDF Invoices', 'when-last-login' ),
		'description' => __( 'Automatically generate and email PDF invoices for Paid Memberships Pro orders.', 'when-last-login' ),
		'icon'        => 'dashicons-media-document',
		'url'         => 'https://yoohooplugins.com/plugins/paid-memberships-pro-pdf-invoices/?utm_source=when-last-login&utm_medium=plugin&utm_campaign=add-ons',
		'badge_type'  => 'premium',
		'badge'       => __( 'Premium', 'when-last-login' ),
	),
	array(
		'name'        => __( 'Zapier & Webhooks Integration for WordPress', 'when-last-login' ),
		'description' => __( 'Automatically sync your WordPress users and other data to thousands of applications.', 'when-last-login' ),
		'icon'        => 'dashicons-randomize',
		'url'         => 'https://yoohooplugins.com/plugins/zapier-integration/?utm_source=when-last-login&utm_medium=plugin&utm_campaign=add-ons',
		'badge_type'  => 'popular',
		'badge'       => __( 'Popular', 'when-last-login' ),
	),

);
?>

<style>
	/* ── Page wrapper ── */
	.wll-addons-page {
		max-width: 1200px;
		margin: 0 auto;
	}

	/* ── Hero ── */
	.wll-addons-hero {
		background: linear-gradient(135deg, #1d2327 0%, #2271b1 100%);
		border-radius: 10px;
		padding: 36px 40px;
		margin-top: 16px;
		margin-bottom: 20px;
		color: #fff;
	}

	.wll-addons-hero__title {
		margin: 0 0 10px;
		font-size: 22px;
		font-weight: 700;
		color: #fff;
		line-height: 1.3;
	}

	.wll-addons-hero__subtitle {
		margin: 0 0 20px;
		font-size: 14px;
		color: rgba(255,255,255,0.85);
		line-height: 1.6;
		max-width: 560px;
	}

	.wll-addons-hero__cta {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		background: #fff;
		color: #2271b1;
		font-size: 13px;
		font-weight: 700;
		text-decoration: none;
		padding: 9px 18px;
		border-radius: 6px;
		transition: background 0.15s ease, color 0.15s ease;
	}

	.wll-addons-hero__cta:hover {
		background: #f0f6fc;
		color: #135e96;
	}

	/* ── Trust bar ── */
	.wll-addons-trust {
		display: flex;
		flex-wrap: wrap;
		gap: 6px 24px;
		margin-bottom: 24px;
		padding: 14px 18px;
		background: #f6f7f7;
		border: 1px solid #e0e0e0;
		border-radius: 8px;
	}

	.wll-addons-trust__item {
		display: flex;
		align-items: center;
		gap: 6px;
		font-size: 12px;
		font-weight: 600;
		color: #3c434a;
	}

	.wll-addons-trust__item .dashicons {
		font-size: 16px;
		width: 16px;
		height: 16px;
		color: #00a32a;
	}

	/* ── Promo bar ── */
	.wll-addons-promo {
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 10px;
		margin-bottom: 20px;
		padding: 11px 18px;
		background: #fff8e5;
		border: 1px dashed #d97706;
		border-radius: 8px;
		font-size: 13px;
		color: #3c434a;
		flex-wrap: wrap;
		text-align: center;
	}

	.wll-addons-promo .dashicons {
		color: #d97706;
		font-size: 16px;
		width: 16px;
		height: 16px;
		flex-shrink: 0;
	}

	.wll-addons-promo__code {
		display: inline-block;
		background: #d97706;
		color: #fff;
		font-weight: 700;
		font-size: 12px;
		letter-spacing: 0.8px;
		padding: 2px 9px;
		border-radius: 4px;
	}

	/* ── Filter bar ── */
	.wll-addons-filter {
		display: flex;
		flex-wrap: wrap;
		gap: 8px;
		margin-bottom: 20px;
	}

	.wll-addons-filter__btn {
		padding: 5px 14px;
		font-size: 12px;
		font-weight: 600;
		border: 1px solid #ddd;
		border-radius: 20px;
		background: #fff;
		color: #50575e;
		cursor: pointer;
		transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
	}

	.wll-addons-filter__btn:hover {
		border-color: #2271b1;
		color: #2271b1;
	}

	.wll-addons-filter__btn.is-active {
		background: #2271b1;
		border-color: #2271b1;
		color: #fff;
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
		transition: box-shadow 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
		position: relative;
	}

	.wll-addon-card:hover {
		border-color: #2271b1;
		box-shadow: 0 4px 16px rgba(0,0,0,0.10);
		transform: translateY(-2px);
	}

	/* ── Badge ── */
	.wll-addon-card__badge {
		position: absolute;
		top: 12px;
		right: 12px;
		background: #2271b1;
		color: #fff;
		font-size: 10px;
		font-weight: 700;
		text-transform: uppercase;
		padding: 3px 8px;
		border-radius: 4px;
		letter-spacing: 0.6px;
	}

	.wll-addon-card__badge--green  { background: #00a32a; }
	.wll-addon-card__badge--orange { background: #d97706; }
	.wll-addon-card__badge--purple { background: #7c3aed; }

	/* ── Icon ── */
	.wll-addon-card__icon {
		width: 48px;
		height: 48px;
		background: #f0f6fc;
		border-radius: 8px;
		display: flex;
		align-items: center;
		justify-content: center;
		margin-bottom: 14px;
	}

	.wll-addon-card__icon .dashicons {
		font-size: 26px;
		width: 26px;
		height: 26px;
		color: #2271b1;
	}

	/* ── Typography ── */
	.wll-addon-card__title {
		margin: 0 0 8px;
		font-size: 15px;
		font-weight: 700;
		color: #1d2327;
		line-height: 1.4;
		padding-right: 48px;
	}

	.wll-addon-card__desc {
		margin: 0 0 20px;
		font-size: 13px;
		color: #50575e;
		line-height: 1.65;
		flex-grow: 1;
	}

	/* ── CTA button ── */
	.wll-addon-card__cta {
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 6px;
		text-decoration: none;
		font-size: 13px;
		font-weight: 600;
		color: #fff;
		background: #2271b1;
		padding: 9px 16px;
		border-radius: 6px;
		transition: background 0.15s ease;
		margin-top: auto;
	}

	.wll-addon-card__cta:hover {
		background: #135e96;
		color: #fff;
	}

	.wll-addon-card[data-badge-type="free"] .wll-addon-card__cta {
		background: #00a32a;
	}

	.wll-addon-card[data-badge-type="free"] .wll-addon-card__cta:hover {
		background: #008a20;
	}

	.wll-addon-card[data-badge-type="popular"] .wll-addon-card__cta {
		background: #d97706;
	}

	.wll-addon-card[data-badge-type="popular"] .wll-addon-card__cta:hover {
		background: #b45309;
	}

	.wll-addon-card[data-badge-type="premium"] .wll-addon-card__cta {
		background: #7c3aed;
	}

	.wll-addon-card[data-badge-type="premium"] .wll-addon-card__cta:hover {
		background: #6d28d9;
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
		.wll-addons-hero { padding: 24px 20px; }
		.wll-addons-grid { grid-template-columns: 1fr; }
	}
</style>

<div class="wll-addons-page">

	<div class="wll-addons-hero">
		<h2 class="wll-addons-hero__title"><?php esc_html_e( 'Supercharge When Last Login', 'when-last-login' ); ?></h2>
		<p class="wll-addons-hero__subtitle"><?php esc_html_e( 'Powerful Add Ons that extend every aspect of user login tracking — from notifications and statistics to exports and integrations.', 'when-last-login' ); ?></p>
		<a class="wll-addons-hero__cta" href="https://yoohooplugins.com/plugins/when-last-login-pro/?utm_source=when-last-login&utm_medium=plugin&utm_campaign=add-ons-hero" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'View When Last Login Pro', 'when-last-login' ); ?>
			<span class="dashicons dashicons-arrow-right-alt"></span>
		</a>
	</div>

	<div class="wll-addons-trust">
		<span class="wll-addons-trust__item"><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'Instant Delivery', 'when-last-login' ); ?></span>
		<span class="wll-addons-trust__item"><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( '1 Year of Updates & Support', 'when-last-login' ); ?></span>
		<span class="wll-addons-trust__item"><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'Money-Back Guarantee', 'when-last-login' ); ?></span>
		<span class="wll-addons-trust__item"><span class="dashicons dashicons-yes-alt"></span><?php esc_html_e( 'Built for WordPress', 'when-last-login' ); ?></span>
	</div>

	<div class="wll-addons-promo">
		<span class="dashicons dashicons-tag"></span>
		<?php esc_html_e( 'Get 5% off your first order — use code', 'when-last-login' ); ?>
		<span class="wll-addons-promo__code">WLL5</span>
		<?php esc_html_e( 'at checkout.', 'when-last-login' ); ?>
	</div>

	<div class="wll-addons-filter">
		<button class="wll-addons-filter__btn is-active" data-filter="all"><?php esc_html_e( 'All', 'when-last-login' ); ?></button>
		<?php
		$seen_types = array();
		foreach ( $wll_add_ons as $addon ) {
			if ( ! empty( $addon['badge_type'] ) && ! in_array( $addon['badge_type'], $seen_types, true ) ) {
				$seen_types[] = $addon['badge_type'];
				echo '<button class="wll-addons-filter__btn" data-filter="' . esc_attr( $addon['badge_type'] ) . '">' . esc_html( $addon['badge'] ) . '</button>';
			}
		}
		?>
	</div>

	<div class="wll-addons-grid">
		<?php foreach ( $wll_add_ons as $addon ) : ?>
			<div class="wll-addon-card" data-badge-type="<?php echo esc_attr( $addon['badge_type'] ?? '' ); ?>">

				<?php if ( ! empty( $addon['badge'] ) ) : ?>
					<?php
					$badge_type_map = array(
						'free'    => 'wll-addon-card__badge--green',
						'popular' => 'wll-addon-card__badge--orange',
						'premium' => 'wll-addon-card__badge--purple',
						'new'     => '',
					);
					$badge_class = isset( $badge_type_map[ $addon['badge_type'] ] ) ? $badge_type_map[ $addon['badge_type'] ] : '';
					?>
					<span class="wll-addon-card__badge <?php echo esc_attr( $badge_class ); ?>">
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
					<?php echo 'free' === $addon['badge_type'] ? esc_html__( 'Install Free', 'when-last-login' ) : esc_html__( 'View Plugin', 'when-last-login' ); ?>
					<span class="dashicons dashicons-arrow-right-alt"></span>
				</a>

			</div>
		<?php endforeach; ?>
	</div>

</div>

<script>
( function () {
	var btns  = document.querySelectorAll( '.wll-addons-filter__btn' );
	var cards = document.querySelectorAll( '.wll-addon-card' );

	btns.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var filter = btn.getAttribute( 'data-filter' );

			btns.forEach( function ( b ) { b.classList.remove( 'is-active' ); } );
			btn.classList.add( 'is-active' );

			cards.forEach( function ( card ) {
				var match = filter === 'all' || card.getAttribute( 'data-badge-type' ) === filter;
				card.style.display = match ? '' : 'none';
			} );
		} );
	} );
} )();
</script>