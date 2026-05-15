/**
 * SwiNOG · Event Quick Facts (dynamic) — editor side.
 * Pure ServerSideRender preview; no inspector controls (no attributes).
 */
( function ( wp ) {
	if ( ! wp || ! wp.blocks ) {
		return;
	}
	var registerBlockType = wp.blocks.registerBlockType;
	var ServerSideRender  = ( wp.serverSideRender && wp.serverSideRender.default ) || wp.serverSideRender;
	var createElement     = wp.element.createElement;
	var __                = ( wp.i18n && wp.i18n.__ ) ? wp.i18n.__ : function ( s ) { return s; };

	registerBlockType( 'swinog/event-quickfacts', {
		edit: function () {
			return ServerSideRender
				? createElement( ServerSideRender, { block: 'swinog/event-quickfacts' } )
				: createElement(
					'div',
					{ style: { padding: 24, border: '1px dashed #c3c4c7', color: '#646970' } },
					__( 'SwiNOG · Event Quick Facts — preview unavailable.', 'swinog' )
				);
		},
		save: function () { return null; },
	} );
} )( window.wp );
