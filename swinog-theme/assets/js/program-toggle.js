/**
 * SwiNOG · Program row click-to-expand
 *
 * Toggles .is-expanded on any .swinog-program__row--expandable when
 * clicked or activated via Enter / Space. Links inside the row don't
 * trigger expansion (event.stopPropagation in the markup + a defensive
 * check here).
 */
( function () {
	function toggle( row ) {
		var open = row.classList.toggle( 'is-expanded' );
		row.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	}

	document.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( 'a, button' ) ) {
			return;
		}
		var row = e.target.closest( '.swinog-program__row--expandable' );
		if ( ! row ) {
			return;
		}
		toggle( row );
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key !== 'Enter' && e.key !== ' ' ) {
			return;
		}
		var row = e.target.closest && e.target.closest( '.swinog-program__row--expandable' );
		if ( ! row || row !== e.target ) {
			return;
		}
		e.preventDefault();
		toggle( row );
	} );
} )();
