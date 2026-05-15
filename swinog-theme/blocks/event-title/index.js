/**
 * SwiNOG · Event title (dynamic) — editor side.
 */
( function ( wp ) {
	if ( ! wp || ! wp.blocks ) return;
	var registerBlockType = wp.blocks.registerBlockType;
	var ServerSideRender  = ( wp.serverSideRender && wp.serverSideRender.default ) || wp.serverSideRender;
	var createElement     = wp.element.createElement;
	var __                = ( wp.i18n && wp.i18n.__ ) ? wp.i18n.__ : function ( s ) { return s; };

	registerBlockType( 'swinog/event-title', {
		edit: function () {
			return ServerSideRender
				? createElement( ServerSideRender, { block: 'swinog/event-title' } )
				: createElement( 'div', null, __( 'SwiNOG · Event title — preview unavailable.', 'swinog' ) );
		},
		save: function () { return null; },
	} );
} )( window.wp );
