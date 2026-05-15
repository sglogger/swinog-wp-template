/**
 * SwiNOG · Recent talks block (swinog/agenda)
 *
 * Editor side: Inspector controls + a server-side preview. Events are
 * stored as a comma-separated slug string on the block attribute, but
 * edited via FormTokenField (chip multi-select) that autocompletes from
 * the live stgl_presentation_cat term list.
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
	var RangeControl      = components.RangeControl;
	var FormTokenField    = components.FormTokenField;
	var Notice            = components.Notice;
	var Fragment          = wp.element.Fragment;
	var createElement     = wp.element.createElement;
	var useState          = wp.element.useState;
	var useEffect         = wp.element.useEffect;
	var apiFetch          = wp.apiFetch;
	var __                = ( wp.i18n && wp.i18n.__ ) ? wp.i18n.__ : function ( s ) { return s; };

	function slugsFromCsv( csv ) {
		if ( ! csv ) { return []; }
		return String( csv ).split( /[,\s]+/ ).map( function ( s ) { return s.trim(); } ).filter( Boolean );
	}

	function csvFromSlugs( arr ) {
		return ( arr || [] ).join( ',' );
	}

	function AgendaEdit( props ) {
		var a   = props.attributes;
		var set = props.setAttributes;

		var termState     = useState( [] );
		var terms         = termState[ 0 ];
		var setTerms      = termState[ 1 ];
		var errorState    = useState( null );
		var loadErr       = errorState[ 0 ];
		var setLoadErr    = errorState[ 1 ];

		useEffect( function () {
			if ( ! apiFetch ) {
				return;
			}
			apiFetch( {
				path: '/wp/v2/stgl_presentation_cat?per_page=100&orderby=name&order=asc&_fields=slug,name,count',
			} ).then( function ( data ) {
				setTerms( Array.isArray( data ) ? data : [] );
			} ).catch( function ( err ) {
				// Plugin not active or REST endpoint missing.
				setLoadErr( ( err && err.message ) || 'Could not load event tags. Is the wp-swinog-events plugin active?' );
			} );
		}, [] );

		var selectedSlugs = slugsFromCsv( a.events );
		var slugSuggest   = ( terms || [] ).map( function ( t ) { return t.slug; } );
		var displaySuggest = ( terms || [] ).map( function ( t ) {
			return t.count > 0 ? t.slug + ' (' + t.count + ')' : t.slug;
		} );

		var eventsControl;
		if ( FormTokenField ) {
			eventsControl = createElement( FormTokenField, {
				label: __( 'Event tags', 'swinog' ),
				value: selectedSlugs,
				suggestions: slugSuggest,
				__experimentalExpandOnFocus: true,
				__experimentalAutoSelectFirstMatch: true,
				onChange: function ( tokens ) {
					// Each token can come back as a string or an object — keep the slug only.
					var slugs = ( tokens || [] ).map( function ( t ) {
						return typeof t === 'string' ? t.trim() : ( t.value || '' );
					} ).filter( Boolean );
					set( { events: csvFromSlugs( slugs ) } );
				},
			} );
		} else {
			eventsControl = createElement( TextControl, {
				label: __( 'Event slugs (comma-separated)', 'swinog' ),
				value: a.events,
				onChange: function ( v ) { set( { events: v } ); },
			} );
		}

		var inspector = createElement(
			InspectorControls,
			{},
			createElement(
				PanelBody,
				{ title: __( 'Recent talks', 'swinog' ), initialOpen: true },
				loadErr ? createElement( Notice, { status: 'warning', isDismissible: false }, loadErr ) : null,
				eventsControl,
				createElement( 'p', { style: { fontSize: '12px', color: '#646970', margin: '4px 0 14px' } },
					selectedSlugs.length
						? __( 'Talks from these events will be merged — newest first. Leave empty to pull from all events.', 'swinog' )
						: __( 'Leave empty to pull from all events. Add one or more tags to filter.', 'swinog' )
				),
				createElement( RangeControl, {
					label: __( 'Number of talks', 'swinog' ),
					value: a.count,
					min: 1,
					max: 18,
					onChange: function ( v ) { set( { count: v } ); },
				} ),
				createElement( RangeControl, {
					label: __( 'Columns', 'swinog' ),
					value: a.columns,
					min: 1,
					max: 3,
					onChange: function ( v ) { set( { columns: v } ); },
				} ),
				createElement( TextControl, {
					label: __( 'Kicker (optional)', 'swinog' ),
					value: a.kicker,
					onChange: function ( v ) { set( { kicker: v } ); },
					help: __( 'Leave blank to auto-generate from the selected events.', 'swinog' ),
				} ),
				createElement( TextControl, {
					label: __( 'Heading', 'swinog' ),
					value: a.title,
					onChange: function ( v ) { set( { title: v } ); },
				} ),
				createElement( TextControl, {
					label: __( 'Archive button · text', 'swinog' ),
					value: a.archiveLabel,
					onChange: function ( v ) { set( { archiveLabel: v } ); },
				} ),
				createElement( TextControl, {
					label: __( 'Archive button · URL', 'swinog' ),
					value: a.archiveUrl,
					onChange: function ( v ) { set( { archiveUrl: v } ); },
				} )
			)
		);

		var preview = createElement( ServerSideRender, {
			block: 'swinog/agenda',
			attributes: a,
		} );

		return createElement( Fragment, {}, inspector, preview );
	}

	registerBlockType( 'swinog/agenda', {
		edit: AgendaEdit,
		save: function () { return null; },
	} );
} )( window.wp );
