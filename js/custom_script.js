( function( $ ) {
	$( document ).ready( function() {
		if( typeof pdfprnt_display_buttons != 'undefined' && 0 == $( '.pdfprnt-buttons' ).length ) {
			if( pdfprnt_display_buttons.top ) {
				$( pdfprnt_display_buttons.top ).insertBefore( 'main' );
			}
			if( pdfprnt_display_buttons.bottom ) {
				$( pdfprnt_display_buttons.bottom ).insertAfter( 'main' );
			}
		}
	} );
} )( jQuery );