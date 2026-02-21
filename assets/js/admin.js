/**
 * Optical Shop UI – Admin JS
 *
 * Handles:
 *  - WP Media uploader integration for image / video / poster fields.
 *  - Show/hide video-only fields based on card type dropdown.
 */
( function ( $ ) {
	'use strict';

	/* ── Media uploader ──────────────────────────────────── */

	$( document ).on( 'click', '.osui-upload-btn', function ( e ) {
		e.preventDefault();

		var button   = $( this );
		var target   = $( button.data( 'target' ) );
		var preview  = $( button.data( 'preview' ) );
		var isVideo  = button.data( 'type' ) === 'video';

		var frame = wp.media( {
			title: isVideo ? 'Select Video' : 'Select Image',
			multiple: false,
			library: {
				type: isVideo ? 'video' : 'image',
			},
		} );

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			target.val( attachment.url );

			if ( isVideo ) {
				preview.html( '<code>' + attachment.filename + '</code>' );
			} else {
				preview.html( '<img src="' + attachment.url + '" style="max-width:200px;max-height:140px;" />' );
			}
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.osui-remove-btn', function ( e ) {
		e.preventDefault();

		var button  = $( this );
		var target  = $( button.data( 'target' ) );
		var preview = $( button.data( 'preview' ) );

		target.val( '' );
		preview.html( '' );
	} );

	/* ── Toggle video/image fields ───────────────────────── */

	function toggleVideoFields() {
		var type = $( '#osui_trending_type' ).val();
		var table = $( '#osui_trending_type' ).closest( '.osui-meta-table' );

		if ( type === 'video' ) {
			table.addClass( 'osui-show-video' );
		} else {
			table.removeClass( 'osui-show-video' );
		}
	}

	$( '#osui_trending_type' ).on( 'change', toggleVideoFields );

	// Run on load.
	toggleVideoFields();

} )( jQuery );
