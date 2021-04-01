( function( $ ) {
	$( document ).ready( function() {

		/* Apply network settings */
		$( 'input[name="pdfprnt_network_apply"]' ).on( 'change', function() {
			if ( $( this ).is( ':checked' ) ) {
				var $input = $( this );

				switch( $input.val() ) {
					case 'off':
						$( '.bws_network_apply_all, .pdfprnt-table-settings, #bws_settings_tabs li:not(.bws-tab-settings):not(.bws-tab-license)' ).hide();
						break;
					case 'default':
						$( '.bws_network_apply_all' ).hide();
						$( '.pdfprnt-table-settings, .pdfprnt-table-settings, #bws_settings_tabs li:not(.bws-tab-settings):not(.bws-tab-license)' ).show();
						break;
					case 'all':
						$( '.bws_network_apply_all, .pdfprnt-table-settings, #bws_settings_tabs li:not(.bws-tab-settings):not(.bws-tab-license)' ).show();
						break;
				}
			}
		} ).trigger( 'change' );

		/* Button Image */
		$( 'input[name="pdfprnt_button_image[pdf]"], input[name="pdfprnt_button_image[print]"]' ).on( 'change', function() {
			if ( $( this ).is( ':checked' ) ) {
				var $input = $( this ),
					button = $input.attr( 'data-button' ),
					image_block = $( '.pdfprnt-image-' + button ),
					image = image_block.find( 'img' ),
					file_input = $( '#pdfprnt-button-image-file-' + button );

				switch( $input.val() ) {
					case 'none':
						image_block.hide();
						file_input.hide();
						break;
					case 'default':
						file_input.hide();
						image.attr( 'src', image.attr( 'data-default-src' ) );
						image_block.show();
						break;
					case 'custom':
						file_input.show();
						image.attr( 'src', image.attr( 'data-custom-src' ) );
						image_block.show();
						break;
				}

				if ( $( 'input[name^="pdfprnt_button_image"][value="custom"]:checked' ).length > 0 ) {
					$( '#pdfprnt-image-info' ).show();
				} else {
					$( '#pdfprnt-image-info' ).hide();
				}
			}
		} ).trigger( 'change' );

		/* Default PDF File Name */
		$( 'input[name="pdfprnt_select_file_name"]' ).on('change', function() {
			if ( $( this ).filter( ':checked' ).val() == 0 ) {
				$( "#pdfprnt-file-name-wrap" ).show();
			} else {
				$( "#pdfprnt-file-name-wrap" ).hide();
			}
		}).trigger('change');

		/* Featured Image Size */
		$( 'input[name="pdfprnt_show_featured_image"]' ).on('change', function() {
			if ( $( this ).is( ':checked' ) ) {
				$( "#pdfprnt_featured_image_size_wrap" ).show();
			} else {
				$( "#pdfprnt_featured_image_size_wrap" ).hide();
			}
		}).trigger('change');

		/* Watermark Protection */
		$( 'input[name="pdfprnt_select_watermark"]' ).on('change', function() {
			var watermark = $( this ).filter( ':checked' ).val();

			switch( watermark ) {
				case 'none':
					$( '#pdfprnt-watermark-text-wrap, #pdfprnt-watermark-file-wrap, #pdfprnt_watermark_opacity' ).hide();
					break;
				case 'text':
					$( '#pdfprnt-watermark-text-wrap, #pdfprnt_watermark_opacity' ).show();
					$( '#pdfprnt-watermark-file-wrap' ).hide();
					break;
				case 'image':
					$( '#pdfprnt-watermark-text-wrap' ).hide();
					$( '#pdfprnt-watermark-file-wrap, #pdfprnt_watermark_opacity' ).show();
					break;
			}
		}).trigger('change');

		/* Watermark Opacity */
		$( '#pdfprnt_watermark_opacity_slider' ).slider({
			value  : $( '#pdfprnt-watermark-opacity' ).val(),
			min    : 0.1,
			max    : 1,
			step   : 0.05,
			create : function( event, ui ) {
				$( '#pdfprnt_watermark_opacity_value' ).text( '[' + $( this ).slider( 'value' ) + ']' );
				$( '#pdfprnt-watermark-opacity' ).hide();
			},
			slide : function( event, ui ) {
				$( '#gglmps_zoom_value' ).text( '[' + ui.value + ']' );
			},
			change: function( event, ui ) {
				$( '#pdfprnt-watermark-opacity' ).val( ui.value );
				$( '#pdfprnt_watermark_opacity_value' ).text( '[' + ui.value + ']' );
			}
		});

		/* Additional Elements */
		$( 'input[name="pdfprnt_image_to_pdf"]' ).on('change', function() {
			if ( $( this ).is( ':checked' ) ) {
				$( "#pdfprnt_additional_elements_wrap, #pdfprnt_woocommerce_product_details_wrap, #pdfprnt_watermark_protection_wrap, #pdfprnt_watermark_opacity, #pdfprnt_prevent_copying_wrap, #pdfprnt_additional_fonts_wrap, #pdfprnt_default_css_wrap, #pdfprnt_print_shortcodes_wrap, #pdfprnt_remove_links_wrap, #pdfprnt-disable-links, #pdfprnt_custom_css_wrap" ).hide();
			} else {
				$( "#pdfprnt_additional_elements_wrap, #pdfprnt_woocommerce_product_details_wrap, #pdfprnt_watermark_protection_wrap, #pdfprnt_prevent_copying_wrap, #pdfprnt_additional_fonts_wrap, #pdfprnt_default_css_wrap, #pdfprnt_print_shortcodes_wrap, #pdfprnt_remove_links_wrap, #pdfprnt-disable-links, #pdfprnt_custom_css_wrap" ).show();
			}
		}).trigger('change');


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

		/* Ajax request for loading of additional fonts */
		var input = $( 'input[name="pdfprnt_load_fonts"]' );
		input.click( function() {
			input.attr( 'disabled', true );
			$.ajax( {
				url: ajaxurl,
				type: "POST",
				data: { action: 'pdfprnt_load_fonts', pdfprnt_ajax_nonce: pdfprnt_var['ajax_nonce'], is_network_admin: $( 'body' ).hasClass( 'network-admin' ) },
				beforeSend: function() {
					$( '#pdfprnt_font_loader' ).css( 'display', 'inline-block' );
					$( '.updated, .error' ).hide();
					$( '<div class="updated fade"><p><strong>' + pdfprnt_var['loading_fonts'] + '</strong></p></div>' ).insertAfter( ".pdfprnt-title" );
					/* display 'warning'-window while fonts loading */
					window.onbeforeunload = function( e ) {
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

		/* add css highlighter*/
		if ( $( 'input[name="pdfprnt_use_custom_css"]' ).length ) {
			var textarea   = $( '#pdfprnt_custom_css_code_wrap' ),
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

		/* Custom Fields tab accordion */
		$( function() {
			$( "#pdfprnt-accordion" ).accordion({
				heightStyle: "content",
				collapsible: true
			});
		} );
	} );
} )( jQuery );

/* Initialize CSS highlighter */
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
                lineNumbers: true,
				lineWrapping: true
            }
        );
    }
}
