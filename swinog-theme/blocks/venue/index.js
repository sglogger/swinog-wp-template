/**
 * SwiNOG · Event venue (dynamic) block — editor side.
 *
 * Vanilla JS (no JSX/build step). Renders the block via
 * ServerSideRender so the editor preview matches the front-end,
 * with two text inspector fields for the kicker + intro attrs.
 */
( function ( wp ) {
	if ( ! wp || ! wp.blocks ) {
		return;
	}

	var registerBlockType = wp.blocks.registerBlockType;
	var InspectorControls = wp.blockEditor && wp.blockEditor.InspectorControls;
	var ServerSideRender  = ( wp.serverSideRender && wp.serverSideRender.default ) || wp.serverSideRender;
	var components        = wp.components || {};
	var PanelBody         = components.PanelBody;
	var TextControl       = components.TextControl;
	var TextareaControl   = components.TextareaControl;
	var Fragment          = wp.element.Fragment;
	var createElement     = wp.element.createElement;
	var __                = ( wp.i18n && wp.i18n.__ ) ? wp.i18n.__ : function ( s ) { return s; };

	function VenueEdit( props ) {
		var a   = props.attributes;
		var set = props.setAttributes;

		var inspector = InspectorControls
			? createElement(
				InspectorControls,
				null,
				createElement(
					PanelBody,
					{ title: __( 'Venue', 'swinog' ), initialOpen: true },
					createElement( TextControl, {
						label: __( 'Kicker', 'swinog' ),
						value: a.kicker || '',
						onChange: function ( v ) { set( { kicker: v } ); },
						help: __( 'Small accent label above the venue name. Leave blank to hide.', 'swinog' ),
					} ),
					createElement( TextareaControl, {
						label: __( 'Intro paragraph', 'swinog' ),
						value: a.intro || '',
						onChange: function ( v ) { set( { intro: v } ); },
						help: __( 'Optional short paragraph shown above the address.', 'swinog' ),
					} )
				)
			)
			: null;

		var preview = ServerSideRender
			? createElement( ServerSideRender, {
				block: 'swinog/venue',
				attributes: a,
			} )
			: createElement(
				'div',
				{ style: { padding: 24, border: '1px dashed #c3c4c7', color: '#646970' } },
				__( 'SwiNOG · Event venue (dynamic) — preview unavailable in this editor.', 'swinog' )
			);

		return createElement( Fragment, null, inspector, preview );
	}

	registerBlockType( 'swinog/venue', {
		edit: VenueEdit,
		save: function () { return null; },
	} );
} )( window.wp );
