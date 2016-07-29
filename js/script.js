/**
 * Functionality for settings page
 */
( function( $ ) {
	$( document ).ready( function() {
		/**
		 * For responsive design
		 */
		pdfprnt_add_labels();
		$( window ).resize( function() {
			pdfprnt_add_labels();
		} );
		/**
 		 * Ajax request for load additional fonts
 		 */
		var input = $( 'input[name="pdfprnt_load_fonts"]' );
		input.click( function() {
			input.attr( 'disabled', true );
				$.ajax( {
					url: ajaxurl,
					type: "POST",
					data: { action: 'pdfprnt_load_fonts', pdfprnt_ajax_nonce: pdfprnt_var['ajax_nonce'] },
					beforeSend: function() {
						$( '#pdfprnt_font_loader' ).css( 'display', 'inline-block' );
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
						try {
							var message = $.parseJSON( result );
						} catch ( e ) {
							$( '<div class="error"><p><strong>' + result + pdfprnt_var['need_reload'] + '.</strong></p></div>' ).insertAfter( ".nav-tab-wrapper" );
							input.attr( 'disabled', false );
							return false;
						}
						if ( message['done'] ) {
							$( '<div class="updated fade"><p><strong>' + message['done'] + '.</strong></p></div>' ).insertAfter( ".nav-tab-wrapper" );
							$( '#pdfprnt_load_fonts_button' ).hide();
						}
						if ( message['error'] ) {
							$( '<div class="error"><p><strong>' + message['error'] + pdfprnt_var['need_reload'] + '.</strong></p></div>' ).insertAfter( ".nav-tab-wrapper" );
							input.attr( 'disabled', false );
						}
					}
				} );
			return false;
		} );

		if ( $( 'input[name="pdfprnt_use_custom_styles"]' ).length ) {
			var textarea   = $( '.pdfprnt_custom_styles' ),
				add_editor = false;
			if ( textarea.is( ':visible' ) && ! textarea.parents( 'body' ).hasClass( 'rtl' ) && ! add_editor ) { /* excluding .rtl pages because codeMirror doesn`t work properly in case textarea is inside table td */
				pdfprnt_add_editor();
				add_editor = true;
			}
			$( 'input[name="pdfprnt_use_custom_styles"]' ).click( function() {
				if ( $( this ).is( ':checked' ) && ! $( this ).parents( 'body' ).hasClass( 'rtl' ) ) { /* excluding .rtl pages because codeMirror doesn`t work properly in case textarea is inside table td */
					textarea.show();
					if ( ! add_editor ) {
						pdfprnt_add_editor();
						add_editor = true;
					}
				} else if ( $( this ).is( ':checked' ) && $( this ).parents( 'body' ).hasClass( 'rtl' ) ) { /* excluding .rtl pages because codeMirror doesn`t work properly in case textarea is inside table td */
					textarea.show();
				} else {
					textarea.hide();
				}
			} );
		}
	} );
} )( jQuery );

/**
 * Add labels to 'position of buttons'-table on settings page
 */
function pdfprnt_add_labels() {
	( function( $ ) {
		var labels = [],
			i = 0;
		if ( $( window ).width() <= 785 ) {
			if ( ! $( '.pdfprnt_label' ).length ) {
				/* get text of column headers */
				$( '.pdfprnt_table_head' ).children().each( function() {
				 	labels[i] = $( this ).text();
				 	i ++;
				} );
				/* add labels */
				for ( i = 1; i < 5; i ++ ) {
					html = '<label class="pdfprnt_label">' + labels[ i - 1 ] +'</label>';
					$( '.pdfprnt_pdf_button td:nth-child(' + i + '), .pdfprnt_print_button td:nth-child(' + i + ')' ).append( html );
					$( '.pdfprnt_position_button td:nth-child(' + i + '), .pdfprnt_layout td:nth-child(' + i + ')' ).prepend( html );
				}
			}
		}
	} )( jQuery );
}

/**
 * Initialize CSS highlighter
 */
function pdfprnt_add_editor() {
	var editor = CodeMirror.fromTextArea(
		document.getElementById( "pdfprnt_custom_styles" ), {
			mode:            "css",
			theme:           "default",
			styleActiveLine: true,
			matchBrackets:   true,
			lineNumbers:     true
		}
	);
}