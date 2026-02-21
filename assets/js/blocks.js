/**
 * Optical Shop UI – Gutenberg Blocks
 *
 * Registers two server-side-rendered blocks:
 *  - optical-shop-ui/shapes
 *  - optical-shop-ui/trending
 */
( function () {
	var el                 = wp.element.createElement;
	var registerBlockType  = wp.blocks.registerBlockType;
	var InspectorControls  = wp.blockEditor.InspectorControls;
	var PanelBody          = wp.components.PanelBody;
	var SelectControl      = wp.components.SelectControl;
	var TextControl        = wp.components.TextControl;
	var ServerSideRender   = wp.serverSideRender || wp.components.ServerSideRender;

	var groupOptions = [
		{ label: 'Eyeglasses', value: 'eyeglasses' },
		{ label: 'Sunglasses', value: 'sunglasses' },
	];

	/* ── Optical Shapes Block ────────────────────────────── */

	registerBlockType( 'optical-shop-ui/shapes', {
		title: 'Optical Shapes',
		icon: 'visibility',
		category: 'widgets',
		description: 'Displays shape tiles (Rectangle, Aviator, etc.) for eyeglasses or sunglasses.',
		attributes: {
			group: { type: 'string', default: 'eyeglasses' },
		},
		edit: function ( props ) {
			return el(
				wp.element.Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Settings', initialOpen: true },
						el( SelectControl, {
							label: 'Group',
							value: props.attributes.group,
							options: groupOptions,
							onChange: function ( val ) {
								props.setAttributes( { group: val } );
							},
						} )
					)
				),
				el( ServerSideRender, {
					block: 'optical-shop-ui/shapes',
					attributes: props.attributes,
				} )
			);
		},
		save: function () {
			return null; // Server-rendered.
		},
	} );

	/* ── Optical Trending Block ──────────────────────────── */

	registerBlockType( 'optical-shop-ui/trending', {
		title: 'Optical Trending',
		icon: 'megaphone',
		category: 'widgets',
		description: 'Displays trending product cards with video hover-play for eyeglasses or sunglasses.',
		attributes: {
			group: { type: 'string', default: 'eyeglasses' },
			brand: { type: 'string', default: 'Lenskart' },
		},
		edit: function ( props ) {
			return el(
				wp.element.Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Settings', initialOpen: true },
						el( SelectControl, {
							label: 'Group',
							value: props.attributes.group,
							options: groupOptions,
							onChange: function ( val ) {
								props.setAttributes( { group: val } );
							},
						} ),
						el( TextControl, {
							label: 'Brand Name',
							value: props.attributes.brand,
							onChange: function ( val ) {
								props.setAttributes( { brand: val } );
							},
						} )
					)
				),
				el( ServerSideRender, {
					block: 'optical-shop-ui/trending',
					attributes: props.attributes,
				} )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
