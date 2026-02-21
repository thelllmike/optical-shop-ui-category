<?php
/**
 * CPT: optical_shape — Shape tile items.
 *
 * @package Optical_Shop_UI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'osui_register_cpt_shapes' );

/**
 * Register the optical_shape custom post type.
 */
function osui_register_cpt_shapes() {

	$labels = array(
		'name'               => __( 'Shape Tiles', 'optical-shop-ui' ),
		'singular_name'      => __( 'Shape Tile', 'optical-shop-ui' ),
		'add_new'            => __( 'Add New Shape', 'optical-shop-ui' ),
		'add_new_item'       => __( 'Add New Shape Tile', 'optical-shop-ui' ),
		'edit_item'          => __( 'Edit Shape Tile', 'optical-shop-ui' ),
		'new_item'           => __( 'New Shape Tile', 'optical-shop-ui' ),
		'view_item'          => __( 'View Shape Tile', 'optical-shop-ui' ),
		'search_items'       => __( 'Search Shape Tiles', 'optical-shop-ui' ),
		'not_found'          => __( 'No shapes found.', 'optical-shop-ui' ),
		'not_found_in_trash' => __( 'No shapes found in Trash.', 'optical-shop-ui' ),
		'all_items'          => __( 'All Shapes', 'optical-shop-ui' ),
		'menu_name'          => __( 'Shapes', 'optical-shop-ui' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => false, // We add it manually under our menu.
		'show_in_rest'        => true,
		'supports'            => array( 'title' ),
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'has_archive'         => false,
		'rewrite'             => false,
		'exclude_from_search' => true,
	);

	register_post_type( 'optical_shape', $args );
}
