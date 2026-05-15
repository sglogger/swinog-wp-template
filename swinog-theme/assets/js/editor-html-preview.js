/**
 * SwiNOG · Default core/html blocks to "Preview" in the block editor.
 *
 * Wraps every core/html BlockEdit with a higher-order component that
 * finds the block's HTML/Preview toggle once on mount and clicks the
 * Preview option. Editors can still click "HTML" if they need to edit
 * raw markup; we only force the initial state.
 *
 * Why: the SwiNOG patterns use core/html blocks for SVG decoration,
 * the event-card stat grid, the sponsor cells, the radial glow, etc.
 * Showing them as raw HTML in the editor is noisy.
 */
( function ( wp ) {
	if ( ! wp || ! wp.hooks || ! wp.compose || ! wp.element ) {
		return;
	}

	var addFilter                   = wp.hooks.addFilter;
	var createHigherOrderComponent  = wp.compose.createHigherOrderComponent;
	var createElement               = wp.element.createElement;
	var useEffect                   = wp.element.useEffect;
	var useRef                      = wp.element.useRef;

	function findPreviewToggle( root ) {
		if ( ! root ) {
			return null;
		}
		// 1) Modern ToggleGroupControlOption — visible radio buttons.
		var radios = root.querySelectorAll( 'button[role="radio"], button[role="tab"]' );
		for ( var i = 0; i < radios.length; i++ ) {
			var label = ( radios[ i ].textContent || '' ).trim().toLowerCase();
			if ( label === 'preview' || label === 'vorschau' || label === 'aperçu' ) {
				return radios[ i ];
			}
		}
		// 2) Fallback — any button whose visible text reads Preview.
		var btns = root.querySelectorAll( 'button' );
		for ( var j = 0; j < btns.length; j++ ) {
			var t = ( btns[ j ].textContent || '' ).trim().toLowerCase();
			if ( t === 'preview' || t === 'vorschau' || t === 'aperçu' ) {
				return btns[ j ];
			}
		}
		return null;
	}

	var withPreviewDefault = createHigherOrderComponent( function ( BlockEdit ) {
		return function ( props ) {
			if ( props.name !== 'core/html' ) {
				return createElement( BlockEdit, props );
			}

			var wrapRef = useRef( null );

			useEffect( function () {
				if ( ! wrapRef.current ) {
					return;
				}
				// The HTML block renders asynchronously; poll briefly until
				// the toggle exists, then click it once.
				var attempts = 0;
				var iv = setInterval( function () {
					attempts++;
					var toggle = findPreviewToggle( wrapRef.current );
					if ( toggle ) {
						var pressed = toggle.getAttribute( 'aria-pressed' ) === 'true'
								   || toggle.getAttribute( 'aria-checked' ) === 'true';
						if ( ! pressed ) {
							toggle.click();
						}
						clearInterval( iv );
					} else if ( attempts > 20 ) {
						clearInterval( iv );
					}
				}, 50 );
				return function () { clearInterval( iv ); };
			}, [] );

			return createElement(
				'div',
				{ ref: wrapRef, className: 'swinog-html-preview-host' },
				createElement( BlockEdit, props )
			);
		};
	}, 'swinogWithPreviewDefault' );

	addFilter(
		'editor.BlockEdit',
		'swinog/default-html-block-to-preview',
		withPreviewDefault
	);
} )( window.wp );
