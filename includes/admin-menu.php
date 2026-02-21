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
 * Dashboard / quick-start page.
 */
function osui_dashboard_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Optical Shop UI', 'optical-shop-ui' ); ?></h1>
		<div class="card" style="max-width:720px;">
			<h2><?php esc_html_e( 'Quick Start', 'optical-shop-ui' ); ?></h2>
			<p><?php esc_html_e( 'Use the sub-menus to manage Shape Tiles and Trending Cards. Then add the shortcodes below to any page or post.', 'optical-shop-ui' ); ?></p>

			<h3><?php esc_html_e( 'Shape Tiles', 'optical-shop-ui' ); ?></h3>
			<code>[optical_shapes group="eyeglasses"]</code><br>
			<code>[optical_shapes group="sunglasses"]</code>

			<h3 style="margin-top:1em;"><?php esc_html_e( 'Trending Cards', 'optical-shop-ui' ); ?></h3>
			<code>[optical_trending group="eyeglasses"]</code><br>
			<code>[optical_trending group="sunglasses"]</code>

			<h3 style="margin-top:1em;"><?php esc_html_e( 'Gutenberg Blocks', 'optical-shop-ui' ); ?></h3>
			<p><?php esc_html_e( 'Search for "Optical Shapes" or "Optical Trending" in the block inserter.', 'optical-shop-ui' ); ?></p>

			<h3 style="margin-top:1em;"><?php esc_html_e( 'Trending Card Heading', 'optical-shop-ui' ); ?></h3>
			<p><?php esc_html_e( 'By default the heading says "#Trending at Lenskart". You can change the brand name with the "brand" attribute:', 'optical-shop-ui' ); ?></p>
			<code>[optical_trending group="eyeglasses" brand="YourBrand"]</code>
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
