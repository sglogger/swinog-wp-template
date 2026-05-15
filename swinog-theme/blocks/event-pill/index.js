/**
 * SwiNOG · Event status pill (dynamic) — editor side.
 * Pure ServerSideRender preview; no inspector controls.
 */
( function ( wp ) {
	if ( ! wp || ! wp.blocks ) {
		return;
	}
	var registerBlockType = wp.blocks.registerBlockType;
	var ServerSideRender  = ( wp.serverSideRender && wp.serverSideRender.default ) || wp.serverSideRender;
	var createElement     = wp.element.createElement;
	var __                = ( wp.i18n && wp.i18n.__ ) ? wp.i18n.__ : function ( s ) { return s; };

	registerBlockType( 'swinog/event-pill', {
		edit: function () {
			return ServerSideRender
				? createElement( ServerSideRender, { block: 'swinog/event-pill' } )
				: createElement( 'div', null, __( 'SwiNOG · Event pill — preview unavailable.', 'swinog' ) );
		},
		save: function () { return null; },
	} );
} )( window.wp );
