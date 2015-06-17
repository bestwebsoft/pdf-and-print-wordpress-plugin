/**
 * Functionality for settings page
 */
(function($) {
	$(document).ready( function() {
		/** 
		 * For responsive design 
		 */
		pdfprnt_add_labels();
		$( window ).resize( function() {
			pdfprnt_add_labels();
		});
		/**
 		 * Ajax request for load additional fonts
 		 */
		var input = $( 'input[name="pdfprnt_load_fonts"]' );
		input.click( function() {
			input.attr( 'disabled', true );
				$.ajax({
					url: ajaxurl,
					type: "POST",
					data: { action: 'pdfprnt_load_fonts', pdfprnt_ajax_nonce: pdfprnt_var['ajax_nonce'] }, 
					beforeSend: function() {
						$( '#pdfprnt_font_loader' ).show();
						$( '.updated, .error' ).hide();
						$( '<div class="updated fade"><p><strong>' + pdfprnt_var['loading_fonts'] + '.</strong></p></div>' ).insertAfter( ".nav-tab-wrapper" );
						/* display 'warning'-window while fonts loading */
						window.onbeforeunload = function(e) {
							if ( $( '#pdfprnt_font_loader' ).is( ':visible' ) )
							return true;
						};
					},
					success: function( result ) { 
						$( '#pdfprnt_font_loader, .updated, .error' ).hide();
						var message = $.parseJSON( result );
						if ( message['done'] ) {
							$( '<div class="updated fade"><p><strong>' + message['done'] + '.</strong></p></div>' ).insertAfter( ".nav-tab-wrapper" );
							$( '#pdfprnt_load_fonts_button' ).hide();
						}
						if ( message['error'] ) {
							$( '<div class="error"><p><strong>' + message['error'] + '.</strong></p></div>' ).insertAfter( ".nav-tab-wrapper" );
							input.attr( 'disabled', false );
						}
					}
				});
			return false;
		});
		/**
		 + Display notices 
		 */
		$( '#pdfprnt_settings_form input, #pdfprnt_settings_form select' ).bind( "change click select", function() {
			if ( $( this ).attr( 'type' ) != 'submit' ) {
				$( '.updated.fade, .error' ).hide();
				$( '#pdfprnt_settings_notice' ).show();
			};
		});
	});
})(jQuery);

/**
 * Add labels to 'position of buttons'-table on settings page  
 */
function pdfprnt_add_labels() {
	(function($) {
		var labels = [], 
			i = 0;
		if ( $(window).width() <= 785 ) {
			if ( ! $( '.pdfprnt_label' ).length ) {
				/* get text of column headers */
				$( '.pdfprnt_table_head' ).children().each( function() {
				 	labels[i] = $(this).text();
				 	 i ++;
				});
				/* add labels */
				for ( i = 1; i < 5; i ++ ) {
					html = '<label class="pdfprnt_label">' + labels[ i - 1 ] +'</label>';
					$( '.pdfprnt_pdf_buttton td:nth-child(' + i + '), .pdfprnt_print_buttton td:nth-child(' + i + ')' ).append( html );
					$( '.pdfprnt_position_buttton td:nth-child(' + i + ')' ).prepend( html );
				}	
			}		
		}
	})(jQuery);
}
