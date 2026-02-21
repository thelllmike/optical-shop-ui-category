<?php
/**
 * CPT: optical_trending — Trending card items.
 *
 * @package Optical_Shop_UI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'osui_register_cpt_trending' );

/**
 * Register the optical_trending custom post type.
 */
function osui_register_cpt_trending() {

	$labels = array(
		'name'               => __( 'Trending Cards', 'optical-shop-ui' ),
		'singular_name'      => __( 'Trending Card', 'optical-shop-ui' ),
		'add_new'            => __( 'Add New Card', 'optical-shop-ui' ),
		'add_new_item'       => __( 'Add New Trending Card', 'optical-shop-ui' ),
		'edit_item'          => __( 'Edit Trending Card', 'optical-shop-ui' ),
		'new_item'           => __( 'New Trending Card', 'optical-shop-ui' ),
		'view_item'          => __( 'View Trending Card', 'optical-shop-ui' ),
		'search_items'       => __( 'Search Trending Cards', 'optical-shop-ui' ),
		'not_found'          => __( 'No trending cards found.', 'optical-shop-ui' ),
		'not_found_in_trash' => __( 'No trending cards found in Trash.', 'optical-shop-ui' ),
		'all_items'          => __( 'All Trending Cards', 'optical-shop-ui' ),
		'menu_name'          => __( 'Trending', 'optical-shop-ui' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => false,
		'show_in_rest'        => true,
		'supports'            => array( 'title' ),
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'has_archive'         => false,
		'rewrite'             => false,
		'exclude_from_search' => true,
	);

	register_post_type( 'optical_trending', $args );
}
