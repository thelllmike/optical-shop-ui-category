<?php
/**
 * Shortcodes and Gutenberg block registrations.
 *
 * Shortcodes:
 *   [optical_shapes group="eyeglasses"]
 *   [optical_shapes group="sunglasses"]
 *   [optical_trending group="eyeglasses" brand="Lenskart"]
 *   [optical_trending group="sunglasses" brand="Lenskart"]
 *
 * @package Optical_Shop_UI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ──────────────────────────────────────────────────────────────
   SHORTCODES
   ────────────────────────────────────────────────────────────── */

add_shortcode( 'optical_shapes', 'osui_shortcode_shapes' );

/**
 * Render the shape tiles grid.
 */
function osui_shortcode_shapes( $atts ) {
	$atts = shortcode_atts(
		array(
			'group' => 'eyeglasses',
		),
		$atts,
		'optical_shapes'
	);

	$group = sanitize_text_field( $atts['group'] );
	$items = osui_get_shapes( $group );

	if ( empty( $items ) ) {
		return '';
	}

	// Enqueue assets only when shortcode is actually rendered.
	wp_enqueue_style( 'osui-frontend' );
	wp_enqueue_script( 'osui-frontend' );

	$label = 'eyeglasses' === $group
		? __( 'Get the perfect shape – Eyeglasses', 'optical-shop-ui' )
		: __( 'Get the perfect shape – Sunglasses', 'optical-shop-ui' );

	ob_start();
	?>
	<section class="osui-shapes osui-shapes--<?php echo esc_attr( $group ); ?>">
		<h2 class="osui-shapes__heading"><?php echo esc_html( $label ); ?></h2>
		<div class="osui-shapes__slider osui-slider">
			<button type="button" class="osui-slider__arrow osui-slider__arrow--prev" aria-label="<?php esc_attr_e( 'Previous', 'optical-shop-ui' ); ?>">&#10094;</button>
			<div class="osui-shapes__grid osui-slider__track">
				<?php foreach ( $items as $item ) : ?>
					<a href="<?php echo esc_url( $item['url'] ); ?>" class="osui-shapes__tile">
						<span class="osui-shapes__circle">
							<?php if ( $item['image'] ) : ?>
								<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy" />
							<?php endif; ?>
						</span>
						<span class="osui-shapes__label"><?php echo esc_html( $item['title'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
			<button type="button" class="osui-slider__arrow osui-slider__arrow--next" aria-label="<?php esc_attr_e( 'Next', 'optical-shop-ui' ); ?>">&#10095;</button>
		</div>
	</section>
	<?php
	return ob_get_clean();
}


add_shortcode( 'optical_trending', 'osui_shortcode_trending' );

/**
 * Render the trending cards row.
 */
function osui_shortcode_trending( $atts ) {
	// Default brand comes from the admin setting, not hardcoded.
	$saved_brand = function_exists( 'osui_get_brand_name' ) ? osui_get_brand_name() : 'Lenskart';

	$atts = shortcode_atts(
		array(
			'group' => 'eyeglasses',
			'brand' => $saved_brand,
		),
		$atts,
		'optical_trending'
	);

	$group = sanitize_text_field( $atts['group'] );
	$brand = sanitize_text_field( $atts['brand'] );
	$items = osui_get_trending( $group );

	if ( empty( $items ) ) {
		return '';
	}

	wp_enqueue_style( 'osui-frontend' );
	wp_enqueue_script( 'osui-frontend' );

	/* translators: %s: brand name */
	$heading = sprintf( __( '#Trending at %s', 'optical-shop-ui' ), $brand );

	ob_start();
	?>
	<section class="osui-trending osui-trending--<?php echo esc_attr( $group ); ?>">
		<h2 class="osui-trending__heading"><?php echo esc_html( $heading ); ?></h2>
		<div class="osui-trending__slider osui-slider">
			<button type="button" class="osui-slider__arrow osui-slider__arrow--prev" aria-label="<?php esc_attr_e( 'Previous', 'optical-shop-ui' ); ?>">&#10094;</button>
			<div class="osui-trending__row osui-slider__track">
				<?php foreach ( $items as $card ) : ?>
					<a href="<?php echo esc_url( $card['url'] ); ?>" class="osui-trending__card osui-trending__card--<?php echo esc_attr( $card['type'] ); ?>" data-card-type="<?php echo esc_attr( $card['type'] ); ?>">

						<?php if ( 'video' === $card['type'] && $card['video'] ) : ?>
							<!-- Video card -->
							<video
								class="osui-trending__video"
								src="<?php echo esc_url( $card['video'] ); ?>"
								<?php if ( $card['poster'] ) : ?>poster="<?php echo esc_url( $card['poster'] ); ?>"<?php endif; ?>
								muted
								playsinline
								preload="metadata"
							></video>
						<?php else : ?>
							<!-- Image card -->
							<?php if ( $card['image'] ) : ?>
								<img class="osui-trending__img" src="<?php echo esc_url( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" loading="lazy" />
							<?php endif; ?>
						<?php endif; ?>

						<span class="osui-trending__overlay">
							<?php if ( $card['title'] ) : ?>
								<span class="osui-trending__title"><?php echo esc_html( $card['title'] ); ?></span>
							<?php endif; ?>
							<?php if ( $card['subtitle'] ) : ?>
								<span class="osui-trending__subtitle"><?php echo esc_html( $card['subtitle'] ); ?></span>
							<?php endif; ?>
							<span class="osui-trending__cta"><?php echo esc_html( $card['cta'] ); ?> &#9654;</span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
			<button type="button" class="osui-slider__arrow osui-slider__arrow--next" aria-label="<?php esc_attr_e( 'Next', 'optical-shop-ui' ); ?>">&#10095;</button>
		</div>
	</section>
	<?php
	return ob_get_clean();
}


/* ──────────────────────────────────────────────────────────────
   GUTENBERG BLOCKS (server-side rendered)
   ────────────────────────────────────────────────────────────── */

add_action( 'init', 'osui_register_blocks' );

function osui_register_blocks() {

	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	// Block: optical-shop-ui/shapes
	register_block_type( 'optical-shop-ui/shapes', array(
		'api_version'     => 2,
		'editor_script'   => 'osui-blocks',
		'attributes'      => array(
			'group' => array(
				'type'    => 'string',
				'default' => 'eyeglasses',
			),
		),
		'render_callback' => 'osui_block_render_shapes',
	) );

	// Block: optical-shop-ui/trending
	register_block_type( 'optical-shop-ui/trending', array(
		'api_version'     => 2,
		'editor_script'   => 'osui-blocks',
		'attributes'      => array(
			'group' => array(
				'type'    => 'string',
				'default' => 'eyeglasses',
			),
			'brand' => array(
				'type'    => 'string',
				'default' => '',
			),
		),
		'render_callback' => 'osui_block_render_trending',
	) );

	// Register the shared block editor script.
	wp_register_script(
		'osui-blocks',
		OSUI_PLUGIN_URL . 'assets/js/blocks.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render' ),
		OSUI_VERSION,
		true
	);
}

/**
 * Server-side render callback for the shapes block.
 */
function osui_block_render_shapes( $attributes ) {
	return osui_shortcode_shapes( $attributes );
}

/**
 * Server-side render callback for the trending block.
 */
function osui_block_render_trending( $attributes ) {
	return osui_shortcode_trending( $attributes );
}
