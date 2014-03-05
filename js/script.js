(function($) {
	$(document).ready( function() {
		$( '#pdfprnt_settings_form input' ).bind( "change click select", function() {
			if ( $( this ).attr( 'type' ) != 'submit' ) {
				$( '.updated.fade' ).css( 'display', 'none' );
				$( '#pdfprnt_settings_notice' ).css( 'display', 'block' );
			};
		});
		$( '#pdfprnt_settings_form select' ).bind( "change", function() {
			$( '.updated.fade' ).css( 'display', 'none' );
			$( '#pdfprnt_settings_notice' ).css( 'display', 'block' );
		});
	});
})(jQuery);