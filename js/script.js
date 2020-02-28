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

		/* Display/hide default Button Image on radio switch */
		$( 'input[name="pdfprnt_button_image[pdf]"], input[name="pdfprnt_button_image[print]"]' ).on( 'change', function() {
			if ( $( this ).is( ':checked' ) ) {
				var $input = $( this ),
					button = $input.attr( 'data-button' );

				switch( $input.val() ) {
					case 'none':
						$( '.pdfprnt-button-image-default-' + button ).hide();
						break;
					case 'default':
						$( '.pdfprnt-button-image-default-' + button ).show();
						break;
				}
			}
		} ).trigger( 'change' );

		/**
			* Add All select *
		*/
		$( '.pdfprnt_role' ).on( 'change', function() {
			var checkboxes = $( '.pdfprnt_role' );
			if ( checkboxes.filter( ':checked' ).length == checkboxes.length ) {
				$( '.pdfprnt_select_all' ).prop( 'checked', true );
			} else {
				$( '.pdfprnt_select_all' ).prop( 'checked', false );
			}
		} ).trigger( 'change' );

		$( '.pdfprnt_select_all' ).on( 'change', function() {
			if ( $( this ).is( ':checked' ) ) {
				$( '.pdfprnt_role' ).prop( 'checked', true );
			} else {
				$( '.pdfprnt_role' ).prop( 'checked', false );
			}
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
						$( '<div class="updated fade"><p><strong>' + pdfprnt_var['loading_fonts'] + '</strong></p></div>' ).insertAfter( ".pdfprnt-title" );
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
							$( '<div class="error"><p><strong>' + result + pdfprnt_var['need_reload'] + '</strong></p></div>' ).insertAfter( ".pdfprnt-title" );
							input.attr( 'disabled', false );
							return false;
						}
						if ( message['done'] ) {
							$( '<div class="updated fade"><p><strong>' + message['done'] + '</strong></p></div>' ).insertAfter( ".pdfprnt-title" );
							$( '#pdfprnt_load_fonts_button' ).hide();
						}
						if ( message['error'] ) {
							$( '<div class="error"><p><strong>' + message['error'] + pdfprnt_var['need_reload'] + '</strong></p></div>' ).insertAfter( ".pdfprnt-title" );
							input.attr( 'disabled', false );
						}
					}
				} );
			return false;
		} );

		/* Ajax request for upgrading of mPDF library */
		var button = $( 'input[name="pdfprnt_upgrade_library"]' );
		button.click( function() {
			button.attr( 'disabled', true );
			$.ajax( {
				url: ajaxurl,
				type: "POST",
				data: { action: 'pdfprnt_upgrade_library', pdfprnt_ajax_nonce: pdfprnt_var['ajax_nonce'], is_network_admin: $( 'body' ).hasClass( 'network-admin' ) },
				beforeSend: function() {
					$( '#pdfprnt_library_loader' ).css( 'display', 'inline-block' );
					$( '.updated, .error' ).hide();
					$( '<div class="updated fade"><p><strong>' + pdfprnt_var['loading_mpdf'] + '</strong></p></div>' ).insertAfter( ".pdfprnt-title" );
				},
				success: function( result ) {
					$( '#pdfprnt_library_loader, .updated, .error' ).hide();
					try {
						var message = $.parseJSON( result );
					} catch ( e ) {
						$( '<div class="error"><p><strong>' + result + '</strong></p></div>' ).insertAfter( ".pdfprnt-title" );
						input.attr( 'disabled', false );
						return false;
					}
					if ( message['done'] ) {
						$( '<div class="updated fade"><p><strong>' + message['done'] + '</strong></p></div>' ).insertAfter( ".pdfprnt-title" );
					}
					if ( message['error'] ) {
						$( '<div class="error"><p><strong>' + message['error'] + '</strong></p></div>' ).insertAfter( ".pdfprnt-title" );
						button.attr( 'disabled', false );
					}
				}
			} );
			return false;
		} );

		if ( $( 'input[name="pdfprnt_use_custom_css"]' ).length ) {
			var textarea = $( '#pdfprnt_custom_css_code_wrap' ),
				add_editor = false;
			if ( textarea.is( ':visible' ) && ! textarea.parents( 'body' ).hasClass( 'rtl' ) && ! add_editor ) { /* excluding .rtl pages because codeMirror doesn`t work properly in case textarea is inside table td */
				pdfprnt_add_editor();
				add_editor = true;
			}

			$( 'input[name="pdfprnt_use_custom_css"]' ).click( function() {
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

		/* Featured Image Size */
		$( 'input[name="pdfprnt_show_featured_image"]' ).on('change', function() {
			if ( $( this ).is( ':checked' ) ) {
				$( "#pdfprnt_featured_image_size_wrap" ).show();
			} else {
				$( "#pdfprnt_featured_image_size_wrap" ).hide();
			}
		}).trigger( 'change' );

		/* Watermark Opacity */
		$( '#pdfprnt_watermark_opacity_slider' ).slider( {
			value : 0.3,
			min   : 0.1,
			max   : 1,
			step  : 0.05,
			create : function( event, ui ) {
				$( '#pdfprnt_watermark_opacity_value' ).text( '[' + $( this ).slider( 'value' ) + ']' );
				$( '#pdfprnt-watermark-opacity' ).hide();
			},
			disabled: true
		} );

		$( '.pdfprnt-custom-accordion' ).accordion( {
			heightStyle: 'content'
		} );
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
	if ( 'function' == typeof wp.CodeMirror || 'function' == typeof CodeMirror ) {
        var CodeMirrorFunc = (
            typeof wp.CodeMirror != 'undefined'
        ) ? wp.CodeMirror : CodeMirror;
        var editor = CodeMirrorFunc.fromTextArea(
            document.getElementById( "pdfprnt_custom_css_code" ), {
                mode: "css",
                theme: "default",
                styleActiveLine: true,
                matchBrackets: true,
                lineNumbers: true
            }
        );
    }
}

