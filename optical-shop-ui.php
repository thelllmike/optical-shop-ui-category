<?php
/**
 * Plugin Name: Optical Shop - Shapes & Trending Cards
 * Plugin URI:  https://example.com/optical-shop-ui
 * Description: Display eyeglass/sunglass shape tiles and trending product cards with video hover-autoplay. Provides shortcodes and Gutenberg blocks.
 * Version:     1.0.0
 * Author:      Starter Dev
 * Author URI:  https://example.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: optical-shop-ui
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── Constants ─────────────────────────────────────────────── */
define( 'OSUI_VERSION', '1.0.0' );
define( 'OSUI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OSUI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'OSUI_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/* ── Includes ──────────────────────────────────────────────── */
require_once OSUI_PLUGIN_DIR . 'includes/helpers.php';
require_once OSUI_PLUGIN_DIR . 'includes/cpt-shapes.php';
require_once OSUI_PLUGIN_DIR . 'includes/cpt-trending.php';
require_once OSUI_PLUGIN_DIR . 'includes/admin-menu.php';
require_once OSUI_PLUGIN_DIR . 'includes/meta-boxes.php';
require_once OSUI_PLUGIN_DIR . 'includes/shortcodes.php';

/* ── Activation hook ───────────────────────────────────────── */
register_activation_hook( __FILE__, 'osui_activate' );

function osui_activate() {
	// Register CPTs so rewrite rules flush correctly.
	osui_register_cpt_shapes();
	osui_register_cpt_trending();
	flush_rewrite_rules();
}

/* ── Deactivation hook ─────────────────────────────────────── */
register_deactivation_hook( __FILE__, 'osui_deactivate' );

function osui_deactivate() {
	flush_rewrite_rules();
}

/* ── Frontend assets (loaded only when shortcode is present) ─ */
add_action( 'wp_enqueue_scripts', 'osui_register_frontend_assets' );

function osui_register_frontend_assets() {
	wp_register_style(
		'osui-frontend',
		OSUI_PLUGIN_URL . 'assets/css/frontend.css',
		array(),
		OSUI_VERSION
	);

	wp_register_script(
		'osui-frontend',
		OSUI_PLUGIN_URL . 'assets/js/frontend.js',
		array(),
		OSUI_VERSION,
		true
	);
}

/* ── Admin assets ──────────────────────────────────────────── */
add_action( 'admin_enqueue_scripts', 'osui_admin_assets' );

function osui_admin_assets( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$is_osui_cpt = in_array( $screen->post_type, array( 'optical_shape', 'optical_trending' ), true );

	if ( $is_osui_cpt ) {
		wp_enqueue_media();
		wp_enqueue_style( 'osui-admin', OSUI_PLUGIN_URL . 'assets/css/admin.css', array(), OSUI_VERSION );
		wp_enqueue_script( 'osui-admin', OSUI_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), OSUI_VERSION, true );
	}
}
