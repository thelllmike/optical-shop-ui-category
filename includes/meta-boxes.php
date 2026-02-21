<?php
/**
 * Meta boxes for Shape Tiles and Trending Cards.
 *
 * @package Optical_Shop_UI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ══════════════════════════════════════════════════════════════
   SHAPE TILE META BOX
   ══════════════════════════════════════════════════════════════ */

add_action( 'add_meta_boxes', 'osui_shape_meta_boxes' );

function osui_shape_meta_boxes() {
	add_meta_box(
		'osui_shape_fields',
		__( 'Shape Tile Settings', 'optical-shop-ui' ),
		'osui_shape_meta_box_html',
		'optical_shape',
		'normal',
		'high'
	);
}

function osui_shape_meta_box_html( $post ) {
	wp_nonce_field( 'osui_shape_save', 'osui_shape_nonce' );

	$group   = get_post_meta( $post->ID, '_osui_shape_group', true ) ?: 'eyeglasses';
	$image   = get_post_meta( $post->ID, '_osui_shape_image', true );
	$url     = get_post_meta( $post->ID, '_osui_shape_url', true );
	$order   = get_post_meta( $post->ID, '_osui_shape_order', true ) ?: 0;
	$enabled = get_post_meta( $post->ID, '_osui_shape_enabled', true );

	// Default to enabled for new posts.
	if ( '' === $enabled && 'auto-draft' === $post->post_status ) {
		$enabled = '1';
	}
	?>
	<table class="form-table osui-meta-table">
		<tr>
			<th><label for="osui_shape_group"><?php esc_html_e( 'Group', 'optical-shop-ui' ); ?></label></th>
			<td>
				<select name="osui_shape_group" id="osui_shape_group">
					<option value="eyeglasses" <?php selected( $group, 'eyeglasses' ); ?>><?php esc_html_e( 'Eyeglasses', 'optical-shop-ui' ); ?></option>
					<option value="sunglasses" <?php selected( $group, 'sunglasses' ); ?>><?php esc_html_e( 'Sunglasses', 'optical-shop-ui' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Shape Image', 'optical-shop-ui' ); ?></label></th>
			<td>
				<div class="osui-media-field">
					<input type="hidden" name="osui_shape_image" id="osui_shape_image" value="<?php echo esc_attr( $image ); ?>" />
					<div id="osui_shape_image_preview" class="osui-image-preview">
						<?php if ( $image ) : ?>
							<img src="<?php echo esc_url( $image ); ?>" style="max-width:120px;max-height:120px;" />
						<?php endif; ?>
					</div>
					<button type="button" class="button osui-upload-btn" data-target="#osui_shape_image" data-preview="#osui_shape_image_preview"><?php esc_html_e( 'Select Image', 'optical-shop-ui' ); ?></button>
					<button type="button" class="button osui-remove-btn" data-target="#osui_shape_image" data-preview="#osui_shape_image_preview"><?php esc_html_e( 'Remove', 'optical-shop-ui' ); ?></button>
				</div>
			</td>
		</tr>
		<tr>
			<th><label for="osui_shape_url"><?php esc_html_e( 'Target URL', 'optical-shop-ui' ); ?></label></th>
			<td>
				<input type="url" name="osui_shape_url" id="osui_shape_url" class="large-text" value="<?php echo esc_attr( $url ); ?>" placeholder="https://example.com/product-category/rectangle/" />
				<p class="description"><?php esc_html_e( 'Full URL to the product category or custom page.', 'optical-shop-ui' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="osui_shape_order"><?php esc_html_e( 'Sort Order', 'optical-shop-ui' ); ?></label></th>
			<td>
				<input type="number" name="osui_shape_order" id="osui_shape_order" value="<?php echo esc_attr( $order ); ?>" min="0" step="1" style="width:80px;" />
			</td>
		</tr>
		<tr>
			<th><label for="osui_shape_enabled"><?php esc_html_e( 'Enabled', 'optical-shop-ui' ); ?></label></th>
			<td>
				<label>
					<input type="checkbox" name="osui_shape_enabled" id="osui_shape_enabled" value="1" <?php checked( $enabled, '1' ); ?> />
					<?php esc_html_e( 'Show this tile on the frontend', 'optical-shop-ui' ); ?>
				</label>
			</td>
		</tr>
	</table>
	<?php
}

add_action( 'save_post_optical_shape', 'osui_shape_save', 10, 2 );

function osui_shape_save( $post_id, $post ) {
	if ( ! isset( $_POST['osui_shape_nonce'] ) || ! wp_verify_nonce( $_POST['osui_shape_nonce'], 'osui_shape_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$group = isset( $_POST['osui_shape_group'] ) ? sanitize_text_field( $_POST['osui_shape_group'] ) : 'eyeglasses';
	if ( ! in_array( $group, array( 'eyeglasses', 'sunglasses' ), true ) ) {
		$group = 'eyeglasses';
	}

	update_post_meta( $post_id, '_osui_shape_group', $group );
	update_post_meta( $post_id, '_osui_shape_image', esc_url_raw( $_POST['osui_shape_image'] ?? '' ) );
	update_post_meta( $post_id, '_osui_shape_url', esc_url_raw( $_POST['osui_shape_url'] ?? '' ) );
	update_post_meta( $post_id, '_osui_shape_order', absint( $_POST['osui_shape_order'] ?? 0 ) );
	update_post_meta( $post_id, '_osui_shape_enabled', isset( $_POST['osui_shape_enabled'] ) ? '1' : '0' );
}


/* ══════════════════════════════════════════════════════════════
   TRENDING CARD META BOX
   ══════════════════════════════════════════════════════════════ */

add_action( 'add_meta_boxes', 'osui_trending_meta_boxes' );

function osui_trending_meta_boxes() {
	add_meta_box(
		'osui_trending_fields',
		__( 'Trending Card Settings', 'optical-shop-ui' ),
		'osui_trending_meta_box_html',
		'optical_trending',
		'normal',
		'high'
	);
}

function osui_trending_meta_box_html( $post ) {
	wp_nonce_field( 'osui_trending_save', 'osui_trending_nonce' );

	$group    = get_post_meta( $post->ID, '_osui_trending_group', true ) ?: 'eyeglasses';
	$type     = get_post_meta( $post->ID, '_osui_trending_type', true ) ?: 'image';
	$subtitle = get_post_meta( $post->ID, '_osui_trending_subtitle', true );
	$cta      = get_post_meta( $post->ID, '_osui_trending_cta', true ) ?: 'Shop now';
	$image    = get_post_meta( $post->ID, '_osui_trending_image', true );
	$video    = get_post_meta( $post->ID, '_osui_trending_video', true );
	$poster   = get_post_meta( $post->ID, '_osui_trending_poster', true );
	$url      = get_post_meta( $post->ID, '_osui_trending_url', true );
	$order    = get_post_meta( $post->ID, '_osui_trending_order', true ) ?: 0;
	$enabled  = get_post_meta( $post->ID, '_osui_trending_enabled', true );

	if ( '' === $enabled && 'auto-draft' === $post->post_status ) {
		$enabled = '1';
	}
	?>
	<table class="form-table osui-meta-table">
		<tr>
			<th><label for="osui_trending_group"><?php esc_html_e( 'Group', 'optical-shop-ui' ); ?></label></th>
			<td>
				<select name="osui_trending_group" id="osui_trending_group">
					<option value="eyeglasses" <?php selected( $group, 'eyeglasses' ); ?>><?php esc_html_e( 'Eyeglasses', 'optical-shop-ui' ); ?></option>
					<option value="sunglasses" <?php selected( $group, 'sunglasses' ); ?>><?php esc_html_e( 'Sunglasses', 'optical-shop-ui' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="osui_trending_type"><?php esc_html_e( 'Card Type', 'optical-shop-ui' ); ?></label></th>
			<td>
				<select name="osui_trending_type" id="osui_trending_type">
					<option value="image" <?php selected( $type, 'image' ); ?>><?php esc_html_e( 'Image', 'optical-shop-ui' ); ?></option>
					<option value="video" <?php selected( $type, 'video' ); ?>><?php esc_html_e( 'Video', 'optical-shop-ui' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="osui_trending_subtitle"><?php esc_html_e( 'Subtitle (optional)', 'optical-shop-ui' ); ?></label></th>
			<td>
				<input type="text" name="osui_trending_subtitle" id="osui_trending_subtitle" class="large-text" value="<?php echo esc_attr( $subtitle ); ?>" />
			</td>
		</tr>
		<tr>
			<th><label for="osui_trending_cta"><?php esc_html_e( 'CTA Text', 'optical-shop-ui' ); ?></label></th>
			<td>
				<input type="text" name="osui_trending_cta" id="osui_trending_cta" value="<?php echo esc_attr( $cta ); ?>" placeholder="Shop now" />
			</td>
		</tr>

		<!-- Image field (shown for both types — used as poster for video) -->
		<tr class="osui-field-image">
			<th><label><?php esc_html_e( 'Image', 'optical-shop-ui' ); ?></label></th>
			<td>
				<div class="osui-media-field">
					<input type="hidden" name="osui_trending_image" id="osui_trending_image" value="<?php echo esc_attr( $image ); ?>" />
					<div id="osui_trending_image_preview" class="osui-image-preview">
						<?php if ( $image ) : ?>
							<img src="<?php echo esc_url( $image ); ?>" style="max-width:200px;max-height:140px;" />
						<?php endif; ?>
					</div>
					<button type="button" class="button osui-upload-btn" data-target="#osui_trending_image" data-preview="#osui_trending_image_preview"><?php esc_html_e( 'Select Image', 'optical-shop-ui' ); ?></button>
					<button type="button" class="button osui-remove-btn" data-target="#osui_trending_image" data-preview="#osui_trending_image_preview"><?php esc_html_e( 'Remove', 'optical-shop-ui' ); ?></button>
				</div>
			</td>
		</tr>

		<!-- Video fields (only for video type) -->
		<tr class="osui-field-video">
			<th><label><?php esc_html_e( 'Video MP4', 'optical-shop-ui' ); ?></label></th>
			<td>
				<div class="osui-media-field">
					<input type="hidden" name="osui_trending_video" id="osui_trending_video" value="<?php echo esc_attr( $video ); ?>" />
					<div id="osui_trending_video_preview" class="osui-image-preview">
						<?php if ( $video ) : ?>
							<code><?php echo esc_html( basename( $video ) ); ?></code>
						<?php endif; ?>
					</div>
					<button type="button" class="button osui-upload-btn" data-target="#osui_trending_video" data-preview="#osui_trending_video_preview" data-type="video"><?php esc_html_e( 'Select Video', 'optical-shop-ui' ); ?></button>
					<button type="button" class="button osui-remove-btn" data-target="#osui_trending_video" data-preview="#osui_trending_video_preview"><?php esc_html_e( 'Remove', 'optical-shop-ui' ); ?></button>
				</div>
			</td>
		</tr>
		<tr class="osui-field-video">
			<th><label><?php esc_html_e( 'Poster Image', 'optical-shop-ui' ); ?></label></th>
			<td>
				<div class="osui-media-field">
					<input type="hidden" name="osui_trending_poster" id="osui_trending_poster" value="<?php echo esc_attr( $poster ); ?>" />
					<div id="osui_trending_poster_preview" class="osui-image-preview">
						<?php if ( $poster ) : ?>
							<img src="<?php echo esc_url( $poster ); ?>" style="max-width:200px;max-height:140px;" />
						<?php endif; ?>
					</div>
					<button type="button" class="button osui-upload-btn" data-target="#osui_trending_poster" data-preview="#osui_trending_poster_preview"><?php esc_html_e( 'Select Poster', 'optical-shop-ui' ); ?></button>
					<button type="button" class="button osui-remove-btn" data-target="#osui_trending_poster" data-preview="#osui_trending_poster_preview"><?php esc_html_e( 'Remove', 'optical-shop-ui' ); ?></button>
				</div>
			</td>
		</tr>

		<tr>
			<th><label for="osui_trending_url"><?php esc_html_e( 'Target URL', 'optical-shop-ui' ); ?></label></th>
			<td>
				<input type="url" name="osui_trending_url" id="osui_trending_url" class="large-text" value="<?php echo esc_attr( $url ); ?>" />
			</td>
		</tr>
		<tr>
			<th><label for="osui_trending_order"><?php esc_html_e( 'Sort Order', 'optical-shop-ui' ); ?></label></th>
			<td>
				<input type="number" name="osui_trending_order" id="osui_trending_order" value="<?php echo esc_attr( $order ); ?>" min="0" step="1" style="width:80px;" />
			</td>
		</tr>
		<tr>
			<th><label for="osui_trending_enabled"><?php esc_html_e( 'Enabled', 'optical-shop-ui' ); ?></label></th>
			<td>
				<label>
					<input type="checkbox" name="osui_trending_enabled" id="osui_trending_enabled" value="1" <?php checked( $enabled, '1' ); ?> />
					<?php esc_html_e( 'Show this card on the frontend', 'optical-shop-ui' ); ?>
				</label>
			</td>
		</tr>
	</table>
	<?php
}

add_action( 'save_post_optical_trending', 'osui_trending_save', 10, 2 );

function osui_trending_save( $post_id, $post ) {
	if ( ! isset( $_POST['osui_trending_nonce'] ) || ! wp_verify_nonce( $_POST['osui_trending_nonce'], 'osui_trending_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$group = sanitize_text_field( $_POST['osui_trending_group'] ?? 'eyeglasses' );
	if ( ! in_array( $group, array( 'eyeglasses', 'sunglasses' ), true ) ) {
		$group = 'eyeglasses';
	}

	$type = sanitize_text_field( $_POST['osui_trending_type'] ?? 'image' );
	if ( ! in_array( $type, array( 'image', 'video' ), true ) ) {
		$type = 'image';
	}

	update_post_meta( $post_id, '_osui_trending_group', $group );
	update_post_meta( $post_id, '_osui_trending_type', $type );
	update_post_meta( $post_id, '_osui_trending_subtitle', sanitize_text_field( $_POST['osui_trending_subtitle'] ?? '' ) );
	update_post_meta( $post_id, '_osui_trending_cta', sanitize_text_field( $_POST['osui_trending_cta'] ?? 'Shop now' ) );
	update_post_meta( $post_id, '_osui_trending_image', esc_url_raw( $_POST['osui_trending_image'] ?? '' ) );
	update_post_meta( $post_id, '_osui_trending_video', esc_url_raw( $_POST['osui_trending_video'] ?? '' ) );
	update_post_meta( $post_id, '_osui_trending_poster', esc_url_raw( $_POST['osui_trending_poster'] ?? '' ) );
	update_post_meta( $post_id, '_osui_trending_url', esc_url_raw( $_POST['osui_trending_url'] ?? '' ) );
	update_post_meta( $post_id, '_osui_trending_order', absint( $_POST['osui_trending_order'] ?? 0 ) );
	update_post_meta( $post_id, '_osui_trending_enabled', isset( $_POST['osui_trending_enabled'] ) ? '1' : '0' );
}
