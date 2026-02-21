<?php
/**
 * Helper / query functions used by shortcodes and blocks.
 *
 * @package Optical_Shop_UI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get published, enabled shape tiles for a group.
 *
 * @param string $group 'eyeglasses' or 'sunglasses'.
 * @return array Array of associative arrays with title, image, url.
 */
function osui_get_shapes( $group = 'eyeglasses' ) {

	$group = in_array( $group, array( 'eyeglasses', 'sunglasses' ), true ) ? $group : 'eyeglasses';

	$query = new WP_Query(
		array(
			'post_type'      => 'optical_shape',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => '_osui_shape_group',
					'value' => $group,
				),
				array(
					'key'   => '_osui_shape_enabled',
					'value' => '1',
				),
				'order_clause' => array(
					'key'     => '_osui_shape_order',
					'type'    => 'NUMERIC',
					'compare' => 'EXISTS',
				),
			),
			'orderby'        => array( 'order_clause' => 'ASC' ),
		)
	);

	$items = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$items[] = array(
				'title' => get_the_title(),
				'image' => get_post_meta( get_the_ID(), '_osui_shape_image', true ),
				'url'   => get_post_meta( get_the_ID(), '_osui_shape_url', true ),
			);
		}
		wp_reset_postdata();
	}

	return $items;
}

/**
 * Get published, enabled trending cards for a group.
 *
 * @param string $group 'eyeglasses' or 'sunglasses'.
 * @return array Array of card data.
 */
function osui_get_trending( $group = 'eyeglasses' ) {

	$group = in_array( $group, array( 'eyeglasses', 'sunglasses' ), true ) ? $group : 'eyeglasses';

	$query = new WP_Query(
		array(
			'post_type'      => 'optical_trending',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => '_osui_trending_group',
					'value' => $group,
				),
				array(
					'key'   => '_osui_trending_enabled',
					'value' => '1',
				),
				'order_clause' => array(
					'key'     => '_osui_trending_order',
					'type'    => 'NUMERIC',
					'compare' => 'EXISTS',
				),
			),
			'orderby'        => array( 'order_clause' => 'ASC' ),
		)
	);

	$items = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$pid     = get_the_ID();
			$items[] = array(
				'title'    => get_the_title(),
				'type'     => get_post_meta( $pid, '_osui_trending_type', true ) ?: 'image',
				'subtitle' => get_post_meta( $pid, '_osui_trending_subtitle', true ),
				'cta'      => get_post_meta( $pid, '_osui_trending_cta', true ) ?: 'Shop now',
				'image'    => get_post_meta( $pid, '_osui_trending_image', true ),
				'video'    => get_post_meta( $pid, '_osui_trending_video', true ),
				'poster'   => get_post_meta( $pid, '_osui_trending_poster', true ),
				'url'      => get_post_meta( $pid, '_osui_trending_url', true ),
			);
		}
		wp_reset_postdata();
	}

	return $items;
}
