<?php
/**
 * Register the "Optical Shop UI" admin menu and sub-pages.
 *
 * @package Optical_Shop_UI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'osui_register_admin_menu' );

/**
 * Build the top-level menu and CPT sub-menus.
 */
function osui_register_admin_menu() {

	// Top-level menu.
	add_menu_page(
		__( 'Optical Shop UI', 'optical-shop-ui' ),
		__( 'Optical Shop UI', 'optical-shop-ui' ),
		'manage_options',
		'optical-shop-ui',
		'osui_dashboard_page',
		'dashicons-visibility',
		58
	);

	// Sub-menu: Dashboard / overview.
	add_submenu_page(
		'optical-shop-ui',
		__( 'Dashboard', 'optical-shop-ui' ),
		__( 'Dashboard', 'optical-shop-ui' ),
		'manage_options',
		'optical-shop-ui',
		'osui_dashboard_page'
	);

	// Sub-menu: Shapes Manager (CPT list).
	add_submenu_page(
		'optical-shop-ui',
		__( 'Shapes Manager', 'optical-shop-ui' ),
		__( 'Shapes Manager', 'optical-shop-ui' ),
		'manage_options',
		'edit.php?post_type=optical_shape'
	);

	// Sub-menu: Trending Manager (CPT list).
	add_submenu_page(
		'optical-shop-ui',
		__( 'Trending Manager', 'optical-shop-ui' ),
		__( 'Trending Manager', 'optical-shop-ui' ),
		'manage_options',
		'edit.php?post_type=optical_trending'
	);
}

/**
 * Keep the Optical Shop UI menu highlighted when editing CPT items.
 */
add_filter( 'parent_file', 'osui_highlight_parent_menu' );

function osui_highlight_parent_menu( $parent_file ) {
	$screen = get_current_screen();
	if ( $screen && in_array( $screen->post_type, array( 'optical_shape', 'optical_trending' ), true ) ) {
		return 'optical-shop-ui';
	}
	return $parent_file;
}

/**
 * Register the brand name setting.
 */
add_action( 'admin_init', 'osui_register_settings' );

function osui_register_settings() {
	register_setting( 'osui_settings', 'osui_brand_name', array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => 'Lenskart',
	) );
}

/**
 * Get the saved brand name (used by shortcodes as default).
 */
function osui_get_brand_name() {
	return get_option( 'osui_brand_name', 'Lenskart' );
}

/**
 * Dashboard / quick-start page with settings form.
 */
function osui_dashboard_page() {
	// Handle form save.
	if ( isset( $_POST['osui_save_settings'] ) ) {
		if ( ! isset( $_POST['osui_settings_nonce'] ) || ! wp_verify_nonce( $_POST['osui_settings_nonce'], 'osui_settings_save' ) ) {
			wp_die( __( 'Security check failed.', 'optical-shop-ui' ) );
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Unauthorized.', 'optical-shop-ui' ) );
		}
		$brand = sanitize_text_field( $_POST['osui_brand_name'] ?? 'Lenskart' );
		update_option( 'osui_brand_name', $brand );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved.', 'optical-shop-ui' ) . '</p></div>';
	}

	$brand = osui_get_brand_name();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Optical Shop UI', 'optical-shop-ui' ); ?></h1>

		<!-- ── Brand Name Setting ──────────────────────────── -->
		<div class="card" style="max-width:720px;">
			<h2><?php esc_html_e( 'Brand Settings', 'optical-shop-ui' ); ?></h2>
			<form method="post">
				<?php wp_nonce_field( 'osui_settings_save', 'osui_settings_nonce' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="osui_brand_name"><?php esc_html_e( 'Brand Name', 'optical-shop-ui' ); ?></label>
						</th>
						<td>
							<input type="text" name="osui_brand_name" id="osui_brand_name" value="<?php echo esc_attr( $brand ); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e( 'Shown in the heading: "#Trending at {Brand Name}". You can also override per-shortcode with brand="…".', 'optical-shop-ui' ); ?></p>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" name="osui_save_settings" class="button-primary" value="<?php esc_attr_e( 'Save Brand Name', 'optical-shop-ui' ); ?>" />
				</p>
			</form>
		</div>

		<!-- ── Video Specs Reference ───────────────────────── -->
		<div class="card" style="max-width:720px;margin-top:16px;">
			<h2><?php esc_html_e( 'Recommended Video Specs for Trending Cards', 'optical-shop-ui' ); ?></h2>
			<table class="widefat striped" style="max-width:480px;">
				<tbody>
					<tr><th><?php esc_html_e( 'Resolution', 'optical-shop-ui' ); ?></th><td><strong>720 × 960 px</strong></td></tr>
					<tr><th><?php esc_html_e( 'Aspect Ratio', 'optical-shop-ui' ); ?></th><td>2 : 3 (portrait)</td></tr>
					<tr><th><?php esc_html_e( 'Format', 'optical-shop-ui' ); ?></th><td>MP4 (H.264)</td></tr>
					<tr><th><?php esc_html_e( 'Bitrate', 'optical-shop-ui' ); ?></th><td>1.5 – 2.5 Mbps</td></tr>
					<tr><th><?php esc_html_e( 'Frame Rate', 'optical-shop-ui' ); ?></th><td>24 – 30 fps</td></tr>
					<tr><th><?php esc_html_e( 'Duration', 'optical-shop-ui' ); ?></th><td>5 – 15 seconds</td></tr>
					<tr><th><?php esc_html_e( 'File Size', 'optical-shop-ui' ); ?></th><td>Under 3 MB</td></tr>
				</tbody>
			</table>
			<p class="description" style="margin-top:8px;">
				<?php esc_html_e( 'Videos auto-play muted on hover (desktop) or when 60%+ visible (mobile). After each video finishes it shows the poster and the next video card starts automatically.', 'optical-shop-ui' ); ?>
			</p>
		</div>

		<!-- ── Quick Start / Shortcodes ────────────────────── -->
		<div class="card" style="max-width:720px;margin-top:16px;">
			<h2><?php esc_html_e( 'Quick Start', 'optical-shop-ui' ); ?></h2>
			<p><?php esc_html_e( 'Use the sub-menus to manage Shape Tiles and Trending Cards. Then add the shortcodes below to any page or post.', 'optical-shop-ui' ); ?></p>

			<h3><?php esc_html_e( 'Shape Tiles', 'optical-shop-ui' ); ?></h3>
			<code>[optical_shapes group="eyeglasses"]</code><br>
			<code>[optical_shapes group="sunglasses"]</code>

			<h3 style="margin-top:1em;"><?php esc_html_e( 'Trending Cards', 'optical-shop-ui' ); ?></h3>
			<code>[optical_trending group="eyeglasses"]</code><br>
			<code>[optical_trending group="sunglasses"]</code>

			<h3 style="margin-top:1em;"><?php esc_html_e( 'Override Brand Per Shortcode', 'optical-shop-ui' ); ?></h3>
			<code>[optical_trending group="eyeglasses" brand="YourBrand"]</code>
			<p class="description"><?php esc_html_e( 'If no brand attribute is set, the saved Brand Name above is used.', 'optical-shop-ui' ); ?></p>

			<h3 style="margin-top:1em;"><?php esc_html_e( 'Gutenberg Blocks', 'optical-shop-ui' ); ?></h3>
			<p><?php esc_html_e( 'Search for "Optical Shapes" or "Optical Trending" in the block inserter.', 'optical-shop-ui' ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * Add custom columns to the Shapes list table.
 */
add_filter( 'manage_optical_shape_posts_columns', 'osui_shapes_columns' );

function osui_shapes_columns( $columns ) {
	$new = array();
	$new['cb']              = $columns['cb'];
	$new['title']           = $columns['title'];
	$new['osui_thumb']      = __( 'Image', 'optical-shop-ui' );
	$new['osui_group']      = __( 'Group', 'optical-shop-ui' );
	$new['osui_order']      = __( 'Order', 'optical-shop-ui' );
	$new['osui_enabled']    = __( 'Enabled', 'optical-shop-ui' );
	$new['date']            = $columns['date'];
	return $new;
}

add_action( 'manage_optical_shape_posts_custom_column', 'osui_shapes_column_content', 10, 2 );

function osui_shapes_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'osui_thumb':
			$img = get_post_meta( $post_id, '_osui_shape_image', true );
			if ( $img ) {
				printf( '<img src="%s" style="width:50px;height:50px;object-fit:contain;border-radius:50%%;" />', esc_url( $img ) );
			} else {
				echo '—';
			}
			break;
		case 'osui_group':
			echo esc_html( ucfirst( get_post_meta( $post_id, '_osui_shape_group', true ) ) );
			break;
		case 'osui_order':
			echo esc_html( get_post_meta( $post_id, '_osui_shape_order', true ) );
			break;
		case 'osui_enabled':
			$enabled = get_post_meta( $post_id, '_osui_shape_enabled', true );
			echo $enabled ? '&#9989;' : '&#10060;';
			break;
	}
}

/**
 * Add custom columns to the Trending list table.
 */
add_filter( 'manage_optical_trending_posts_columns', 'osui_trending_columns' );

function osui_trending_columns( $columns ) {
	$new = array();
	$new['cb']              = $columns['cb'];
	$new['title']           = $columns['title'];
	$new['osui_type']       = __( 'Type', 'optical-shop-ui' );
	$new['osui_group']      = __( 'Group', 'optical-shop-ui' );
	$new['osui_order']      = __( 'Order', 'optical-shop-ui' );
	$new['osui_enabled']    = __( 'Enabled', 'optical-shop-ui' );
	$new['date']            = $columns['date'];
	return $new;
}

add_action( 'manage_optical_trending_posts_custom_column', 'osui_trending_column_content', 10, 2 );

function osui_trending_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'osui_type':
			echo esc_html( ucfirst( get_post_meta( $post_id, '_osui_trending_type', true ) ) );
			break;
		case 'osui_group':
			echo esc_html( ucfirst( get_post_meta( $post_id, '_osui_trending_group', true ) ) );
			break;
		case 'osui_order':
			echo esc_html( get_post_meta( $post_id, '_osui_trending_order', true ) );
			break;
		case 'osui_enabled':
			$enabled = get_post_meta( $post_id, '_osui_trending_enabled', true );
			echo $enabled ? '&#9989;' : '&#10060;';
			break;
	}
}

/**
 * Make the order columns sortable.
 */
add_filter( 'manage_edit-optical_shape_sortable_columns', 'osui_shapes_sortable' );
function osui_shapes_sortable( $columns ) {
	$columns['osui_order'] = 'osui_order';
	return $columns;
}

add_filter( 'manage_edit-optical_trending_sortable_columns', 'osui_trending_sortable' );
function osui_trending_sortable( $columns ) {
	$columns['osui_order'] = 'osui_order';
	return $columns;
}

add_action( 'pre_get_posts', 'osui_orderby_meta' );
function osui_orderby_meta( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( 'osui_order' === $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', $query->get( 'post_type' ) === 'optical_shape' ? '_osui_shape_order' : '_osui_trending_order' );
		$query->set( 'orderby', 'meta_value_num' );
	}
}
