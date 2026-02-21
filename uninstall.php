<?php
/**
 * Uninstall — runs when the plugin is deleted through WP Admin.
 *
 * Removes all custom post type entries and their meta.
 *
 * @package Optical_Shop_UI
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete all optical_shape posts + meta.
$shapes = get_posts(
	array(
		'post_type'      => 'optical_shape',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $shapes as $id ) {
	wp_delete_post( $id, true );
}

// Delete all optical_trending posts + meta.
$trending = get_posts(
	array(
		'post_type'      => 'optical_trending',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $trending as $id ) {
	wp_delete_post( $id, true );
}

// Delete plugin options.
delete_option( 'osui_brand_name' );
