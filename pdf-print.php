<?php
/*
Plugin Name: PDF & Print by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/pdf-print/
Description: Generate PDF files and print WordPress posts/pages. Customize document header/footer styles and appearance.
Author: BestWebSoft
Text Domain: pdf-print
Domain Path: /languages
Version: 2.2.1
Author URI: https://bestwebsoft.com/
License: GPLv2 or later
*/

/* © Copyright 2020 BestWebSoft ( https://support.bestwebsoft.com )

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

require_once( dirname( __FILE__ ) . '/includes/deprecated.php' );

/**
 * Add our own menu
 */
if ( ! function_exists( 'pdfprnt_add_admin_menu' ) ) {
	function pdfprnt_add_admin_menu() {
		global $submenu, $pdfprnt_plugin_info, $wp_version, $pdfprnt_options;

		$settings = add_menu_page( __( 'PDF & Print Settings', 'pdf-print' ), 'PDF & Print', 'manage_options', 'pdf-print.php', 'pdfprnt_settings_page' );
		add_submenu_page( 'pdf-print.php', __( 'PDF & Print Settings', 'pdf-print' ), __( 'Settings', 'pdf-print' ), 'manage_options', 'pdf-print.php', 'pdfprnt_settings_page' );
		add_submenu_page( 'pdf-print.php', __( 'Headers & Footers', 'pdf-print' ), __( 'Headers & Footers', 'pdf-print' ), 'manage_options', 'pdf-print-templates.php', 'pdfprnt_templates' );
		add_submenu_page( 'pdf-print.php', 'BWS Panel', 'BWS Panel', 'manage_options', 'pdfprnt-bws-panel', 'bws_add_menu_render' );

		if ( isset( $submenu['pdf-print.php'] ) ) {
			$submenu['pdf-print.php'][] = array(
				'<span style="color:#d86463"> ' . __( 'Upgrade to Pro', 'pdf-print' ) . '</span>',
				'manage_options',
				'https://bestwebsoft.com/products/wordpress/plugins/pdf-print/?k=d9da7c9c2046bed8dfa38d005d4bffdb&pn=101&v=' . $pdfprnt_plugin_info["Version"] . '&wp_v=' . $wp_version );
		}

		add_action( "load-{$settings}", 'pdfprnt_add_tabs' );
	}
}

/**
 * Add help tab on settings page
 * @return void
 */
if ( ! function_exists( 'pdfprnt_add_tabs' ) ) {
	function pdfprnt_add_tabs() {
		$args = array(
			'id'		=> 'pdfprnt',
			'section'	=> '200538669'
		);
		bws_help_tab( get_current_screen(), $args );
	}
}

/**
 * Localization
 */
if ( ! function_exists( 'pdfprnt_plugins_loaded' ) ) {
	function pdfprnt_plugins_loaded() {
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'pdf-print', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/**
 * Init plugin
 */
if ( ! function_exists ( 'pdfprnt_init' ) ) {
	function pdfprnt_init() {
		global $pdfprnt_plugin_info, $pdfprnt_is_old_php;
		$plugin_basename = plugin_basename( __FILE__ );

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( $plugin_basename );

		if ( empty( $pdfprnt_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$pdfprnt_plugin_info = get_plugin_data( __FILE__ );
		}

		/* check WordPress version */
		bws_wp_min_version_check( $plugin_basename, $pdfprnt_plugin_info, '4.5' );

		/* check PHP version */
		$pdfprnt_is_old_php = version_compare( PHP_VERSION, '5.4.0', '<' );

		/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && 'pdf-print.php' == $_GET['page'] ) ) {
			pdfprnt_settings();
		}
	}
}

/**
 * Admin init
 */
if ( ! function_exists( 'pdfprnt_admin_init' ) ) {
	function pdfprnt_admin_init() {
		global $bws_plugin_info, $pdfprnt_plugin_info, $bws_shortcode_list, $pagenow, $pdfprnt_options;

		if ( empty( $bws_plugin_info ) ) {
			$bws_plugin_info = array( 'id' => '101', 'version' => $pdfprnt_plugin_info["Version"] );
		}
		/* add PDF&Print to global $bws_shortcode_list */
		$bws_shortcode_list['pdfprnt'] = array( 'name' => 'PDF&Print', 'js_function' => 'pdfprnt_shortcode_init' );

		if ( 'plugins.php' == $pagenow ) {
			/* Install the option defaults */
			if ( function_exists( 'bws_plugin_banner_go_pro' ) ) {
				pdfprnt_settings();
				bws_plugin_banner_go_pro( $pdfprnt_options, $pdfprnt_plugin_info, 'pdfprnt', 'pdf-print', 'e2f2549f4d70bc4cb9b48071169d264e', '101', 'pdf-print' );
			}
		}
	}
}

/**
 * Function for activation
 */
if ( ! function_exists( 'pdfprnt_plugin_activate' ) ) {
	function pdfprnt_plugin_activate() {
		if ( is_multisite() ) {
			switch_to_blog( 1 );
			register_uninstall_hook( __FILE__, 'pdfprnt_uninstall' );
			restore_current_blog();
		} else {
			register_uninstall_hook( __FILE__, 'pdfprnt_uninstall' );
		}
	}
}

/**
 * Register settings for plugin
 */
if ( ! function_exists( 'pdfprnt_settings' ) ) {
	function pdfprnt_settings() {
		global $pdfprnt_options, $pdfprnt_plugin_info;
		$options_default = pdfprnt_get_options_default();
		if ( ! get_option( 'pdfprnt_options' ) ) {
			add_option( 'pdfprnt_options', $options_default );
		}

		$pdfprnt_options = get_option( 'pdfprnt_options' );
		if ( ! isset( $pdfprnt_options['plugin_option_version'] ) || $pdfprnt_options['plugin_option_version'] != $pdfprnt_plugin_info["Version"] ) {
			if ( isset( $pdfprnt_options['button_post_types'] ) ) {
				unset( $options_default['button_post_types'] );
			}
			if ( function_exists( 'array_replace_recursive' ) ) {
				$pdfprnt_options = array_replace_recursive( $options_default, $pdfprnt_options );
			} else {
				foreach ( $options_default as $key => $value ) {
					if (
						! isset( $pdfprnt_options[ $key ] ) ||
						( isset( $pdfprnt_options[ $key ] ) && is_array( $options_default[ $key ] ) && ! is_array( $pdfprnt_options[ $key ] ) )
					) {
						$pdfprnt_options[ $key ] = $options_default[ $key ];
					} else {
						if ( is_array( $options_default[ $key ] ) ) {
							foreach ( $options_default[ $key ] as $key2 => $value2 ) {
								if ( ! isset( $pdfprnt_options[ $key ][ $key2 ] ) )
									$pdfprnt_options[ $key ][ $key2 ] = $options_default[ $key ][ $key2 ];
							}
						}
					}
				}
			}
			$is_old_pro = strpos( "pro-", $pdfprnt_options['plugin_option_version'] === false ) ? false : true;
			if ( $is_old_pro ) {
				foreach ( array( 'pdf', 'print' ) as $button ) {
					if ( $pdfprnt_options['button_image'][ $button ]['type'] != 'none' ) {
						$pdfprnt_options['button_image'][ $button ]['type'] = 'default';
						$pdfprnt_options['button_image'][ $button ]['image_src'] = plugins_url( "images/{$button}.png", __FILE__ );
					}
				}
			}

			$pdfprnt_options['plugin_option_version'] = $pdfprnt_plugin_info["Version"];
			$pdfprnt_options['hide_premium_options'] = array();

			/**
			 * @deprecated 2.2.1
			 * @todo Remove after 20.08.2020
			 */
			if ( version_compare( $pdfprnt_plugin_info["Version"], '2.2.1' ) <= 0 ) {
				$pdfprnt_options['use_default_css'] = 'theme' === $pdfprnt_options['use_default_css'] || 1 === $pdfprnt_options['use_default_css'] ? 1 : 0;
			}
			/* end todo */

			update_option( 'pdfprnt_options', $pdfprnt_options );

			pdfprnt_plugin_activate();
		}
	}
}

/**
 * Get plugin default settings
 */
if ( ! function_exists( 'pdfprnt_get_options_default' ) ) {
	function pdfprnt_get_options_default() {
		global $pdfprnt_plugin_info, $wp_roles;

		/* Variable to verify performance number of once function. */
		$default_post_types = array();
		/* Default post types of WordPress. */
		foreach ( get_post_types( array( 'public' => 1, 'show_ui' => 1, '_builtin' => true ), 'names' ) as $value ) {
			$default_post_types[] = $value;
		}
		$default_post_types[] = 'pdfprnt_search';
		$default_post_types[] = 'pdfprnt_archives';

		$enabled_roles = array();
		$roles = array_keys( $wp_roles->roles );
		foreach ( $roles as $key => $role ) {
			$enabled_roles[ $role ] = 1;
		}
		$enabled_roles['unauthorized'] = 1;

		$options_default = array(
			'plugin_option_version'			=> $pdfprnt_plugin_info["Version"],
			'button_post_types'				=> array(
												'pdf'	=> $default_post_types,
												'print'	=> $default_post_types
											),
			'buttons_position'				=> 'top-right',
			'use_default_css'				=> 0,
			'use_custom_css'				=> 0,
			'custom_css_code'				=> '',
			'do_shorcodes'					=> 1,
			'disable_links'					=> 1,
			'show_print_window'				=> 0,
			'additional_fonts'				=> 0,
			'show_title'					=> 1,
			'show_featured_image'			=> 0,
			'featured_image_size'			=> 'thumbnail',
			'button_image'					=> array(
													'pdf' => array(
														'type'			=> 'default',
														'image_src'		=> plugins_url( "images/pdf.png", __FILE__ )
													),
													'print' => array(
														'type'			=> 'default',
														'image_src'		=> plugins_url( "images/print.png", __FILE__ )
													)
												),
			'button_title'					=> array(
													'pdf'		=> '',
													'print'	=> ''
												),
			'pdf_page_size'					=> 'A4',
            'image_to_pdf'                  => 0,
			'pdf_margins'					=> array(
													'left'		=> 15,
													'right'		=> 15,
													'top'			=> 16,
													'bottom'	=> 16
												),
			'first_install'						=> strtotime( "now" ),
			'suggest_feature_banner'			=> 1,
			'file_action' 						=> 'open',
			'enabled_roles'						=> $enabled_roles
								);

		return $options_default;
	}
}

/**
 * Settings page
 */
if ( ! function_exists( 'pdfprnt_settings_page' ) ) {
	function pdfprnt_settings_page() {
		require_once( dirname( __FILE__ ) . '/includes/pro_banners.php' );
		if ( ! class_exists( 'Bws_Settings_Tabs' ) )
			require_once( dirname( __FILE__ ) . '/bws_menu/class-bws-settings.php' );
		require_once( dirname( __FILE__ ) . '/includes/class-pdfprnt-settings.php' );
		$page = new Pdfprnt_Settings_Tabs( plugin_basename( __FILE__ ) ); ?>
		<div class="wrap">
			<h1 class="pdfprnt-title"><?php _e( 'PDF & Print Settings', 'pdf-print' ); ?></h1>
			<?php $page->display_content(); ?>
		</div>
	<?php }
}

/**
 * Headers & Footers page
 */
if ( ! function_exists( 'pdfprnt_templates' ) ) {
	function pdfprnt_templates() {
		global $wp_version, $pdfprnt_plugin_info, $pdfprnt_options, $pdfprnt_links;
		require_once( dirname( __FILE__ ) . '/includes/pro_banners.php' );

		pdfprnt_settings();
		$bws_hide_premium = bws_hide_premium_options_check( $pdfprnt_options );

		$tab_action = ( isset( $_GET['pdfprnt_tab_action'] ) && 'new' == $_GET['pdfprnt_tab_action'] );
		if ( $tab_action ) {
			$page_title = __( 'Add New', 'pdf-print' );
			$page_name = __( 'Header & Footer Template', 'pdf-print' );
		} else {
			$page_title = '';
			$page_name = __( 'Headers & Footers', 'pdf-print' );
		}
		$display_title = ( ! empty( $page_title ) ) ? "$page_title $page_name": "$page_name";
		if ( ! isset( $_GET['pdfprnt_tab_action'] ) && ! $bws_hide_premium ) {
			$display_title .= sprintf(
				' <a class="add-new-h2 pdfprnt_add_new_button" href="%s">%s</a>',
				admin_url( 'admin.php?page=pdf-print-templates.php&pdfprnt_tab_action=new' ),
				__( 'Add New', 'pdf-print' )
			);
		} ?>
		<div class="wrap">
			<h1 class="pdfprnt-title"><?php echo $display_title; ?></h1>
			<br>
			<?php if ( $bws_hide_premium ) { ?>
			    <p>
                    <?php _e( 'This tab contains Pro options only.', 'pdf-print' );
                    echo ' ' . sprintf(
                        __( '%sChange the settings%s to view the Pro options.', 'pdf-print' ),
                        '<a href="admin.php?page=pdf-print.php&bws_active_tab=misc">',
                        '</a>'
                    ); ?>
                </p>
			<?php } else { ?>
				<div class="bws_pro_version_bloc pdfprnt-pro-feature pdfprnt-pro-feature-templates">
					<div class="bws_pro_version_table_bloc">
						<div class="bws_table_bg" style="z-index: 1098;"></div>
						<div class="bws_pro_version">
							<?php if ( ! $tab_action ) {
								$date_format = get_option( 'date_format' );
								pdfprnt_headers_footers_list_block( $date_format );
							} else {
								pdfprnt_headers_footers_editor_block();
							} ?>
						</div>
					</div>
					<div class="bws_pro_version_tooltip" style="z-index: 1099;">
						<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/pdf-print/?k=d9da7c9c2046bed8dfa38d005d4bffdb&pn=101&v=<?php echo $pdfprnt_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="PDF & Print Pro Plugin"><?php _e( 'Upgrade to Pro', 'pdf-print' ); ?></a>
						<div class="clear"></div>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php }
}

/* PDF&Print shortcode */
/* [bws_pdfprint] */
if ( ! function_exists( 'pdfprnt_shortcode' ) ) {
	function pdfprnt_shortcode( $attr ) {
		global $pdfprnt_options, $pdfprnt_is_old_php;

		if ( isset( $_REQUEST['print'] ) || $pdfprnt_is_old_php || ! pdfprnt_is_user_role_enabled() ) {
			return;
		}

		$buttons = '';
		if ( is_home() ) {
			global $post;
			$permalink = get_permalink( $post );
		} else {
			$permalink = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		$shortcode_atts = shortcode_atts( array( 'display' => 'pdf' ), $attr );
		$shortcode_atts = str_word_count( $shortcode_atts['display'], 1 );

		foreach ( $shortcode_atts as $value ) {
			if ( 'pdf' == $value ) {
				$buttons .= pdfprnt_get_button( 'pdf', $permalink );
			}
			if ( 'print' == $value ) {
				$buttons .= pdfprnt_get_button( 'print', $permalink );
			}
		}
		if ( ! empty( $buttons ) ) {
			$buttons = sprintf(
				'<div class="pdfprnt-buttons">%s</div>',
				$buttons
			);
		}
		return $buttons;
	}
}

/**
 * Add shortcode content
 */
if ( ! function_exists( 'pdfprnt_shortcode_button_content' ) ) {
	function pdfprnt_shortcode_button_content( $content ) { ?>
		<div id="pdfprnt" style="display:none;">
			<fieldset>
				<?php _e( 'Add PDF & Print Buttons to your page or post', 'pdf-print' ); ?>
				<br />
				<label>
					<input type="checkbox" name="pdfprnt_selected_pdf" value="pdf" checked="checked" />
					<?php _e( 'PDF', 'pdf-print' ); ?>
				</label>
				<br />
				<label>
					<input type="checkbox" name="pdfprnt_selected_print" value="print" />
					<?php _e( 'Print', 'pdf-print' ); ?>
				</label>
				<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_pdfprint]" />
				<div class="clear"></div>
			</fieldset>
		</div>
		<?php $script = "function pdfprnt_shortcode_init() {
            ( function( $ ) {
                $( '.mce-reset input[name^=\"pdfprnt_selected\"]' ).change( function() {
                    var result = '';
                    $( '.mce-reset input[name^=\"pdfprnt_selected\"]' ).each( function() {
                        if ( $( this ).is( ':checked' ) ) {
                            result += $( this ).val() + ',';
                        }
                    } );

                    if ( '' == result ) {
                        $( '.mce-reset #bws_shortcode_display' ).text( '' );
                    } else {
                        result = result.slice( 0, - 1 );
                        $( '.mce-reset #bws_shortcode_display' ).text( '[bws_pdfprint display=\"' + result + '\"]' );
                    }
                } );
            } ) ( jQuery );
        }";

        wp_register_script( 'pdfprnt_bws_shortcode_button', '' );
        wp_enqueue_script( 'pdfprnt_bws_shortcode_button' );
        wp_add_inline_script( 'pdfprnt_bws_shortcode_button', sprintf( $script ) );
	}
}

/**
 * Forming button
 * @param 		string		$button					button name: 'pdf' or 'print'
 * @param 		string		$url					page url or permalink
 * @param 		string		$custom_query_arg		custom query arg to add to query
 * @return 		string		$link					formed link with image and button title
 */
if ( ! function_exists( 'pdfprnt_get_button' ) ) {
	function pdfprnt_get_button( $button = 'pdf', $url = '', $custom_query_arg = '' ) {
		global $pdfprnt_options;

		if ( empty( $url ) ) {
			$url = home_url( '/' );
		}
		$button = ( 'print' == $button ) ? 'print' : 'pdf';

		if ( 'default' == $pdfprnt_options['button_image'][ $button ]['type'] && ! empty( $pdfprnt_options['button_image'][ $button ]['image_src'] ) ) {
			$image = sprintf( '<img src="%s" alt="image_%s" title="%s" />',
				esc_attr( $pdfprnt_options['button_image'][ $button ]['image_src'] ),
				$button,
				( ( 'print' == $button ) ? __( 'Print Content', 'pdf-print' ) : __( 'View PDF', 'pdf-print' ) )
			);
		} else {
			$image = '';
		}

		$custom_query_arg = ( ! empty( $custom_query_arg ) ) ? $custom_query_arg : $button;
		$url = esc_url( $url );
		$url = add_query_arg( 'print' , $custom_query_arg , $url );
        $target='_blank';

		$title = ( ! empty( $pdfprnt_options['button_title'][ $button ] ) ) ?
			sprintf(
				'<span class="pdfprnt-button-title pdfprnt-button-%s-title">%s</span>',
				$button,
				$pdfprnt_options['button_title'][ $button ]
			)
		:
			'';
        if ($pdfprnt_options['image_to_pdf'] && $custom_query_arg == 'pdf') {
            $url="javascript: imageToPdf()";
            $target='_self';
        }
        $link = sprintf(
            '<a href="%s" class="pdfprnt-button pdfprnt-button-%s" target="%s">%s%s</a>',
            $url,
            $button,
            $target,
            $image,
            $title
        );
		return $link;
	}
}

if ( ! function_exists( 'pdfprnt_is_user_role_enabled' ) ) {
	function pdfprnt_is_user_role_enabled() {
		global $pdfprnt_options, $current_user;
		if ( ! is_user_logged_in() ) {
			return ! empty( $pdfprnt_options['enabled_roles']['unauthorized'] );
		} else {
			$role = $current_user->roles[0];
			return ! empty( $pdfprnt_options['enabled_roles'][ $role ] );
		}
	}
}

/**
 * Positioning buttons in content of standart posts and pages
 */
if ( ! function_exists( 'pdfprnt_content' ) ) {
	function pdfprnt_content( $content ) {
		global $pdfprnt_options, $post, $pdfprnt_is_old_php;

		if ( $pdfprnt_is_old_php || is_admin() || is_feed() || is_search() || is_category() || is_tax() || is_tag() || is_author() || ! pdfprnt_is_user_role_enabled() || post_password_required( $post->ID ) ) {
			return $content;
		}

		$show_button_pdf = ! empty( $pdfprnt_options['button_post_types']['pdf'] ) && in_array( $post->post_type, $pdfprnt_options['button_post_types']['pdf'] );
		$show_button_print = ! empty( $pdfprnt_options['button_post_types']['print'] ) && in_array( $post->post_type, $pdfprnt_options['button_post_types']['print'] );

		if ( $show_button_pdf || $show_button_print ) {

			$str = '<div class="pdfprnt-buttons pdfprnt-buttons-' . $post->post_type . ' pdfprnt-' . $pdfprnt_options['buttons_position'] . '">';
			if ( is_home() ) {
				$permalink = get_permalink( $post );
			} else {
				$permalink = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			}

			if ( $show_button_pdf ) {
				$str .= pdfprnt_get_button( 'pdf', $permalink );
			}
			if ( $show_button_print ) {
				$str .= pdfprnt_get_button( 'print', $permalink );
			}
			$str .= '</div>';

			if ( 'top-left' == $pdfprnt_options['buttons_position'] || 'top-right' == $pdfprnt_options['buttons_position'] ) {
				$content = $str . $content;
			} elseif ( 'bottom-left' == $pdfprnt_options['buttons_position'] || 'bottom-right' == $pdfprnt_options['buttons_position'] ) {
				$content = $content . $str;
			} else {
				$content = $str . $content . $str;
			}
		}
		return $content;
	}
}

/**
 * Output buttons for search or archive pages
 */
if ( ! function_exists( 'pdfprnt_show_buttons_search_archive' ) ) {
	function pdfprnt_show_buttons_search_archive( $content ) {
		global $wp_query;

		/* make sure that we display pdf/print buttons only with main loop */
		if ( is_main_query() && $content === $wp_query ) {
			global $pdfprnt_options, $wp, $posts, $pdfprnt_show_archive_start, $pdfprnt_show_archive_end;

			$is_search = ( is_search() );
			$is_archive = ( is_archive() || is_category() || is_tax() || is_tag() || is_author() );

			if ( $is_search ) {
				$show_button_pdf = ( in_array( 'pdfprnt_search', $pdfprnt_options['button_post_types']['pdf'] ) );
				$show_button_print = ( in_array( 'pdfprnt_search', $pdfprnt_options['button_post_types']['print'] ) );
			} else if( $is_archive ) {
				$show_button_pdf = ( in_array( 'pdfprnt_archives', $pdfprnt_options['button_post_types']['pdf'] ) );
				$show_button_print = ( in_array( 'pdfprnt_archives', $pdfprnt_options['button_post_types']['print'] ) );
			} else {
				$show_button_pdf = false;
				$show_button_print = false;
			}

			$loop_position = current_filter();
			if ( ! ( $show_button_pdf || $show_button_print ) ) {
				return;
			}

			if ( ( 'loop_start' == $loop_position && $pdfprnt_show_archive_start == 1 ) || ( 'loop_end' == $loop_position && $pdfprnt_show_archive_end == 1 ) ) {
				return;
			}

			global $pdfprnt_is_search_archive;
			$current_url = set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$pdfprnt_is_search_archive = true;
			$str = '<div class="pdfprnt-buttons pdfprnt-buttons-' . ( ( $is_search ) ? 'search' : 'archive' ) .' pdfprnt-' . $pdfprnt_options['buttons_position'] . '">';
			if ( $show_button_pdf ) {
				$str .= pdfprnt_get_button( 'pdf', $current_url, 'pdf-search' );
			}

			if ( $show_button_print ) {
				$str .= pdfprnt_get_button( 'print', $current_url, 'print-search' );
			}
			$str .= '</div>';
			echo $str;
			if ( 'loop_start' == $loop_position ) {
				$pdfprnt_show_archive_start++;
			} elseif ( 'loop_end' == $loop_position ) {
				$pdfprnt_show_archive_end++;
			}
		}
	}
}

/**
 * Add buttons before or after the loop
 */
if ( ! function_exists( 'pdfprnt_auto_show_buttons_search_archive' ) ) {
	function pdfprnt_auto_show_buttons_search_archive() {
		global $pdfprnt_options, $pdfprnt_is_old_php;

		if ( $pdfprnt_is_old_php ) {
			return;
		}

		$display_in_search = ( is_search() && ( in_array( 'pdfprnt_search', $pdfprnt_options['button_post_types']['pdf'] ) || in_array( 'pdfprnt_search', $pdfprnt_options['button_post_types']['print'] ) ) );
		$display_in_archive = ( ( is_archive() || is_category() || is_tax() || is_tag() || is_author() ) && ( in_array( 'pdfprnt_archives', $pdfprnt_options['button_post_types']['pdf'] ) || in_array( 'pdfprnt_archives', $pdfprnt_options['button_post_types']['print'] ) ) );

		if ( $display_in_search || $display_in_archive ) {
			global $pdfprnt_show_archive_start, $pdfprnt_show_archive_end;
			$pdfprnt_show_archive_start = $pdfprnt_show_archive_end = 0;
			if ( in_array( $pdfprnt_options['buttons_position'], array( 'top-left', 'top-right' ) ) ) {
				add_action( 'loop_start', 'pdfprnt_show_buttons_search_archive' );
			} elseif ( in_array( $pdfprnt_options['buttons_position'], array( 'bottom-left', 'bottom-right' ) ) ) {
				add_action( 'loop_end', 'pdfprnt_show_buttons_search_archive' );
			} else {
				add_action( 'loop_start', 'pdfprnt_show_buttons_search_archive' );
				add_action( 'loop_end', 'pdfprnt_show_buttons_search_archive' );
			}
		}
	}
}

/**
 * Display plugin buttons via action call
 * @param     string       $where         where to display
 * @param     mixed        $user_query    WP_Query parameters
 */
if ( ! function_exists( 'pdfprnt_display_plugin_buttons' ) ) {
	function pdfprnt_display_plugin_buttons( $where = 'top', $user_query = '' ) {
		global $pdfprnt_options, $pdfprnt_is_old_php;

		if ( $pdfprnt_is_old_php ) {
			return;
		}

		if ( preg_match( "|" . $where . "|", $pdfprnt_options['buttons_position'] ) || empty( $where ) ) {
			echo pdfprnt_show_buttons_for_custom_post_type( $user_query );
		}
	}
}

/**
 * Output buttons of page for custom post type
 */
if ( ! function_exists( 'pdfprnt_show_buttons_for_custom_post_type' ) ) {
	function pdfprnt_show_buttons_for_custom_post_type( $user_query = '' ) {
		global $pdfprnt_options, $post, $posts;

		$show_button_pdf = in_array( $post->post_type, $pdfprnt_options['button_post_types']['pdf'] );
		$show_button_print = in_array( $post->post_type, $pdfprnt_options['button_post_types']['print'] );

		if (
			/* not display anything if displaying buttons for post and pages is disabled */
			( ! ( $show_button_pdf || $show_button_print ) ) ||
			/* not display anything if we have wrong $user_query */
			( ! ( is_array( $user_query ) || is_string( $user_query ) ) )
		) {
			return;
		}
		$current_url = set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = esc_url( $current_url );
		$is_return = true;

		if ( empty( $user_query ) ) { /* set necessary values of parameters for pdf/print buttons */
			$nothing_else = false;
			if ( is_search() || is_archive() || is_category() || is_tax() || is_tag() || is_author() || ! pdfprnt_is_user_role_enabled() ) { /* search, cattegories, archives */
				foreach ( $posts as $value ) {
					if ( in_array( $value->post_type, $pdfprnt_options['button_post_types']['pdf'] ) || in_array( 'page', $pdfprnt_options['button_post_types']['print'] ) ) {
						$is_return = false;
						break;
					}
				}
				if ( $is_return ) {
					return;
				}
				$pdf_query_parameter = 'pdf-search';
				$print_query_parameter = 'print-search';
			} elseif ( is_page() ) { /* pages */
				if ( in_array( 'page', $pdfprnt_options['button_post_types']['pdf'] ) || in_array( 'page', $pdfprnt_options['button_post_types']['print'] ) ) {
					$nothing_else = true;
				} else {
					return;
				}
			} elseif ( is_single() ) { /* posts */
				$post_type = get_post_type( $post->ID );
				if ( in_array( $post_type, $pdfprnt_options['button_post_types']['pdf'] ) || in_array( $post_type, $pdfprnt_options['button_post_types']['print'] ) ) {
					$nothing_else = true;
				} else {
					return;
				}
			} else {
				$nothing_else = true;
			}
			if ( $nothing_else ) {
				$pdf_query_parameter = 'pdf';
				$print_query_parameter = 'print';
			}
		} else {
			$custom_query = new WP_Query( $user_query );
			$current_url = add_query_arg( $custom_query->query, '', $current_url );
			/* Check for existence the type of posts. */
			if ( ! empty( $custom_query->posts ) ) {
				foreach ( $custom_query->posts as $post ) {
					if ( in_array( get_post_type( $post ), $pdfprnt_options['button_post_types']['pdf'] ) || in_array( get_post_type( $post ), $pdfprnt_options['button_post_types']['print'] ) ) {
						$is_return = false;
						break;
					}
				}
			}
			if ( $is_return ) {
				return;
			}
			global $pdfprnt_is_custom_post_type;
			$pdfprnt_is_custom_post_type = true;
			$pdf_query_parameter = 'pdf-custom';
			$print_query_parameter = 'print-custom';
		}

		$str = '<div class="pdfprnt-buttons pdfprnt-buttons-custom pdfprnt-buttons-' . $post->post_type . ' pdfprnt-' . $pdfprnt_options['buttons_position'] . '">';
		if ( $show_button_pdf ) {
			$str .= pdfprnt_get_button( 'pdf', $current_url, $pdf_query_parameter );
		}
		if ( $show_button_print ) {
			$str .= pdfprnt_get_button( 'print', $current_url, $print_query_parameter );
		}
		$str .= '</div>';
		return $str;
	}
}

/**
 * Add action links
 */
if ( ! function_exists( 'pdfprnt_action_links' ) ) {
	function pdfprnt_action_links( $links, $file ) {
		if ( ! is_network_admin() && is_plugin_inactive( 'pdf-print-pro/pdf-print-pro.php' ) ) {
			$base = plugin_basename( __FILE__ );
			if ( $file == $base ) {
				$settings_link = '<a href="admin.php?page=pdf-print.php">' . __( 'Settings', 'pdf-print' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

/**
 * Add links
 */
if ( ! function_exists( 'pdfprnt_links' ) ) {
	function pdfprnt_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() && is_plugin_inactive( 'pdf-print-pro/pdf-print-pro.php' ) ) {
				$links[] = '<a href="admin.php?page=pdf-print.php">' . __( 'Settings', 'pdf-print' ) . '</a>';
			}
			$links[] = '<a href="https://support.bestwebsoft.com/hc/en-us/sections/200538669" target="_blank">' . __( 'FAQ', 'pdf-print' ) . '</a>';
			$links[] = '<a href="https://support.bestwebsoft.com">' . __( 'Support', 'pdf-print' ) . '</a>';
		}
		return $links;
	}
}

/**
 * Add stylesheets
 */
if ( ! function_exists ( 'pdfprnt_admin_head' ) ) {
	function pdfprnt_admin_head() {
		global $pdfprnt_plugin_info, $pdfprnt_options, $post;

		if ( is_admin() ) {
			wp_enqueue_style( 'pdfprnt_general', plugins_url( 'css/style-general.css', __FILE__ ), false, $pdfprnt_plugin_info['Version'] );

			if ( isset( $_GET['page'] ) && "pdf-print.php" == $_GET['page'] ) {
				wp_enqueue_style( 'pdfprnt_stylesheet', plugins_url( 'css/style.css', __FILE__ ), false, $pdfprnt_plugin_info['Version'] );
				bws_enqueue_settings_scripts();
				bws_plugins_include_codemirror();
				wp_enqueue_script( 'pdfprnt_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery', 'jquery-ui-accordion', 'jquery-ui-slider' ), $pdfprnt_plugin_info['Version'] );
				wp_localize_script( 'pdfprnt_script', 'pdfprnt_var', array(
						'loading_fonts'	=> __( 'Loading of fonts. It might take a several minutes.', 'pdf-print' ),
						'loading_mpdf' => __( 'Loading the mPDF library. It will take few minutes.', 'pdf-print' ),
						'ajax_nonce'	=> wp_create_nonce( 'pdfprnt_ajax_nonce' ),
						'need_reload'	=> '&nbsp;' . sprintf(
							__( 'It is necessary to reload fonts. For more info, please see %s.', 'pdf-print' ),
							'<a href="https://support.bestwebsoft.com/hc/en-us/articles/206693223" target="_blank">' . __( 'FAQ', 'pdf-print' ) . '</a>'
						)
					)
				);
			}
			if ( isset( $_GET['page'] ) && "pdf-print-templates.php" == $_GET['page'] ) {
				wp_enqueue_style( 'pdfprnt_stylesheet', plugins_url( 'css/templates.css', __FILE__ ), false, $pdfprnt_plugin_info['Version'] );
			}
		} elseif ( isset( $post->post_title ) ) {
			wp_enqueue_style( 'pdfprnt_frontend', plugins_url( 'css/frontend.css', __FILE__ ), false, $pdfprnt_plugin_info['Version'] );

            /* Sending data for front js */
            $file_name =  $post->post_title;
            wp_enqueue_script('html2canvas.js', plugins_url('js/html2canvas.js', __FILE__));
            wp_enqueue_script('jspdf.js', plugins_url('js/jspdf.js', __FILE__));
            wp_enqueue_script( 'pdfprnt_front_script', plugins_url( 'js/front-script.js', __FILE__ ), array( 'html2canvas.js', 'jspdf.js') );
            wp_localize_script( 'pdfprnt_front_script', 'pdfprnt_file_settings', array(
                    'margin_left'   => $pdfprnt_options['pdf_margins']['left'],
                    'margin_right'  => $pdfprnt_options['pdf_margins']['right'],
                    'margin_top'    => $pdfprnt_options['pdf_margins']['top'],
                    'margin_bottom' => $pdfprnt_options['pdf_margins']['bottom'],
                    'page_size'     => $pdfprnt_options['pdf_page_size'],
                    'file_action'   => $pdfprnt_options['file_action'],
                    'file_name'     => $file_name )
            );
		}
	}
}

/**
 * Remove inline 'font-family' and 'font' styles from content
 */
if ( ! function_exists( 'pdfprnt_preg_replace' ) ) {
	function pdfprnt_preg_replace( $patterns, $content ) {
		foreach( $patterns as $pattern ) {
			$content = preg_replace( "/" . $pattern . "(.*?);/", "", $content );
			preg_match_all( "~style=(\"\'?)~", $content, $quotes );/* get array with quotes */
			if ( isset( $quotes[1] ) && ! empty( $quotes[1] ) ) {
				foreach ( $quotes[1] as $quote ) {
					preg_match_all( "~style=" . $quote . "(.*?)" . $quote . "~", $content, $styles );
					if ( ! empty( $styles[1] ) ) {
						foreach ( $styles[1] as $style ) {
							if ( preg_match( "/" . $pattern . "/", $style ) ) {
								$content = preg_replace( "/" . $style . "/", "", $content );
							}
						}

					}
				}
			}
		}
		return $content;
	}
}

/**
 * Generate templates for pdf file or print
 */
if ( ! function_exists( 'pdfprnt_generate_template' ) ) {
	function pdfprnt_generate_template( $content, $is_print = false ) {
		global $pdfprnt_options, $wp_locale, $post;
		$html =
		'<html>
			<head>';
				if ( $is_print && $post instanceof WP_Post ) {
					if ( is_archive() || is_category() || is_tax() || is_tag() || is_author() || ! pdfprnt_is_user_role_enabled() ) {
						$title = ( function_exists( 'get_the_archive_title' ) ) ? get_the_archive_title() : wp_title( '', false );
					} elseif ( is_search() ) {
						$title = sprintf( __( 'Search Results for: %s', 'pdf-print' ), get_search_query() );
					} else {
						$title = get_the_title();
					}
					$html .= sprintf(
						'<title>%s - %s</title>',
						strip_tags( $title ),
						get_bloginfo( 'name' )
					);
				}
				if ( ! empty( $pdfprnt_options['use_default_css'] ) && 1 == $pdfprnt_options['use_default_css'] ) {
					/* remove 'font-family' and 'font' styles from theme css-file if additional fonts not loaded */
					if ( 0 == $pdfprnt_options['additional_fonts'] ) {
						$css = wp_remote_get( get_bloginfo( 'stylesheet_url' ) );
						if ( is_array( $css ) && ! empty( $css['body'] ) ) {
							$html .= '<style type="text/css">' . preg_replace( "/(font:(.*?);)|(font-family(.*?);)/", "", $css['body'] ) . '</style>';
						}
					} else {
						$html .= '<link type="text/css" rel="stylesheet" href="' . get_bloginfo( 'stylesheet_url' ) . '" media="all" />';
					}
				} else {
					$html .= '<link type="text/css" rel="stylesheet" href="' . plugins_url( 'css/default.css', __FILE__ ) . '" media="all" />';
				}
				$html .= pdfprnt_additional_styles( $is_print );
				if ( 1 == $pdfprnt_options['use_custom_css'] && ! empty( $pdfprnt_options['custom_css_code'] ) ) {
					$html .= '<style type="text/css">' . $pdfprnt_options['custom_css_code'] . '</style>';
				}
				if ( $is_print && 1 == $pdfprnt_options['show_print_window'] ) {
					$html .= '<script>window.onload = function(){ window.print(); };</script>';
				}
			$html .=
			'</head>
				<body class="' . ( $is_print ? 'pdfprnt_print ' : '' ) . $wp_locale->text_direction . '">';
				/* Remove inline 'font-family' and 'font' styles from content */
				if ( 0 == $pdfprnt_options['additional_fonts'] ) {
					$content = pdfprnt_preg_replace( array( "font-family", "font:" ), $content );
				}
				$html .= $content .
			'</body>
		</html>';
		return $html;
	}
}

if ( ! function_exists( 'pdfprnt_additional_styles' ) ) {
	function pdfprnt_additional_styles( $is_print ) {
		$styles = apply_filters( 'bwsplgns_add_pdf_print_styles', array() );
		$html = '';
		if ( ! empty( $styles ) && is_array( $styles ) ) {
			$url = get_bloginfo( 'url' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			$path = get_home_path();
			foreach ( $styles as $style ) {
				if ( ! empty( $style[0] ) && file_exists( $path . $style[0] ) ) {
					/* if "get print" */
					if ( $is_print ) {
						if( ( isset( $style[1] ) && 'print' == $style[1] ) || ! isset( $style[1] ) ) {
							$html .= '<link type="text/css" rel="stylesheet" href="' . $url . '/' . $style[0] . '" media="all" />';
						}
					/* if "get pdf" */
					} else {
						if ( ( isset( $style[1] ) && 'pdf' == $style[1] ) || ! isset( $style[1] ) ) {
							$html .= '<link type="text/css" rel="stylesheet" href="' . $url . '/' . $style[0] . '" media="all" />';
						}
					}
				}
			}
		}
		return $html;
	}
}

/**
 * Output print page or pdf document and include plugin script
 */
if ( ! function_exists( 'pdfprnt_print' ) ) {
	function pdfprnt_print( $query ) {
		global $pdfprnt_options, $posts, $post, $pdfprnt_is_old_php, $pdfprnt_links;
		$print = get_query_var( 'print' );

		if ( $pdfprnt_is_old_php || empty( $print ) || ! pdfprnt_is_user_role_enabled() ) {
			return;
		}

		if ( apply_filters( 'bwsplgns_remove_content_filters', true ) ) {
			remove_all_filters( 'the_content' );
			add_filter( 'the_content', 'capital_P_dangit', 11 );
			add_filter( 'the_content', 'wptexturize' );
			add_filter( 'the_content', 'convert_smilies' );
			add_filter( 'the_content', 'convert_chars' );
			add_filter( 'the_content', 'wpautop' );

			if ( ! class_exists( 'WP_Embed' ) ) {
				require_once( ABSPATH . WPINC . '/class-wp-embed.php' );
			}
			new WP_Embed();
		}
		do_action( 'bwsplgns_pdf_print_the_content' );
		if ( 1 == $pdfprnt_options['do_shorcodes'] ) {
			add_filter( 'the_content', 'do_shortcode' );
			$pattern = false;
		} else {
			$pattern = get_shortcode_regex();
		}

		$doc_type = explode( '-', $print );

		if ( ! is_array( $doc_type ) ) {
			return;
		}

		/* for search or archives */
		if ( isset( $doc_type[1] ) ) {
			switch ( $doc_type[1] ) {
				case 'custom':
					$url_data = parse_url( $_SERVER['REQUEST_URI'] );
					parse_str( $url_data['query'], $args );
					unset( $args['print'] );
					if ( ! empty( $args ) )
						$posts = query_posts( $args );
					break;
				case 'search':
				default:
					break;
			}
		/* for single posts or pages */
		} elseif (
			( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] == get_home_url() . '/?print=' . $doc_type[0]
			&&
			'page' == get_option( 'show_on_front' )
		) {
			$post_id = get_option( 'page_on_front' );
			$posts = query_posts( array( 'page_id' => $post_id ) );
		} else {
			if ( ! is_home() ) {
				$posts = array( $post );
			}
		}

		switch( $doc_type[0] ) {
			case 'pdf':

				/* set path to directory with fonts */
				if ( ! ( 0 == $pdfprnt_options['additional_fonts'] && defined( "_MPDF_SYSTEM_TTFONTS" ) ) ) {
					if ( is_multisite() ) {
						switch_to_blog( 1 );
						$upload_dir = wp_upload_dir();
						restore_current_blog();
					} else {
						$upload_dir = wp_upload_dir();
					}
					define( '_MPDF_SYSTEM_TTFONTS', $upload_dir['basedir'] .'/pdf-print-fonts/' );
				}

				/* prepare data */
				$titles = $authors = array();
				$default_font = 0 == $pdfprnt_options['additional_fonts'] ? 'dejavusansmono' : '';
				$last = count( $posts );
				$i = 1;
				$html = '<div id="content">';
				foreach ( $posts as $p ) {

					$title_filter = apply_filters( 'bwsplgns_get_pdf_print_title', apply_filters( 'the_title', $p->post_title, $p->ID ), $p );
					$titles[]= $title_filter;
					$user_info = get_userdata( $p->post_author );
					$authors[] = $user_info->display_name;
					$title = 1 == $pdfprnt_options['show_title'] ? '<div class="entry-header"><h1 class="entry-title"><a href="' . get_permalink( $p ) . '">' . $title_filter . '</a></h1></div>' : '';
					$image = 1 == $pdfprnt_options['show_featured_image'] && has_post_thumbnail( $p->ID ) ? '<div class="entry-thumbnail">' . get_the_post_thumbnail( $p->ID , $pdfprnt_options['featured_image_size']) . '</div>' : '';
					/* replacing shortcodes to an empty string which were added to the content */
					if ( $pattern && preg_match_all( '/'. $pattern .'/s', $p->post_content, $matches ) ) {
						foreach ( array_unique( $matches[0] ) as $value )
							$p->post_content = str_replace( $value, "", $p->post_content );
					}

					$post_content = apply_filters( 'bwsplgns_get_pdf_print_content', $p->post_content, $p );
                    $shortcodes = implode( '|', apply_filters( 'bwsplgns_pdf_print_remove_shortcodes', array( 'vc_', 'az_' , 'multilanguage_switcher') ) );
                    $post_content = preg_replace( "/\[\/?({$shortcodes})[^\]]*?\]/", '', $post_content );
					$post_content = apply_filters( 'the_content', $post_content );

					$html .=
						'<div class="post">' .
							$image .
							$title .
							'<div class="entry-content">' . $post_content . '</div>
						</div>';
					if ( $i != $last ) {
						$html .= '<br/><hr/><br/>';
						$i ++;
					}
				}
				$html .= '</div>';
				$titles = array_unique( $titles );
				$authors = array_unique( $authors );

				$pdfprnt_links = $pdfprnt_options['disable_links'];

				/* generate PDF-document */
				$path = plugin_dir_path( __FILE__ ) . 'vendor';
				$is_installed = file_exists( $path );
                if ( $is_installed ) {
	                /* Implement mPDF v7.1.5 */
	                include ( __DIR__ . '/vendor/autoload.php' );
	                $mpdf_config = array(
		                'mode' => '+aCJK',
		                'format' => $pdfprnt_options['pdf_page_size'],
		                'default_font_size' => 0,
		                'default_font' => $default_font,
		                'margin_left' => $pdfprnt_options['pdf_margins']['left'],
		                'margin_right' => $pdfprnt_options['pdf_margins']['right'],
		                'margin_top' => $pdfprnt_options['pdf_margins']['top'],
		                'margin_bottom' => $pdfprnt_options['pdf_margins']['bottom'],
	                );
	                $mpdf = new \Mpdf\Mpdf( $mpdf_config );

                } else {
	                include ( 'mpdf/mpdf.php' );
	                $mpdf = new mPDF(
	                        '+aCJK',
                            $pdfprnt_options['pdf_page_size'],
                            0,
                            $default_font,
                            $pdfprnt_options['pdf_margins']['left'],
                            $pdfprnt_options['pdf_margins']['right'],
                            $pdfprnt_options['pdf_margins']['top'],
                            $pdfprnt_options['pdf_margins']['bottom']
                    );
                }
				$mpdf->allow_charset_conversion = true;
				$mpdf->charset_in = get_bloginfo( 'charset' );
				if ( 0 != $pdfprnt_options['additional_fonts'] ) {
					$mpdf->autoScriptToLang = true;
					$mpdf->autoLangToFont = true;
					$mpdf->baseScript = 1;
					$mpdf->autoVietnamese = true;
					$mpdf->autoArabic = true;
				}
				if ( is_rtl() ) {
					$mpdf->SetDirectionality( 'rtl' );
				}
				$mpdf->SetTitle( htmlspecialchars_decode( implode( ',', $titles ) ) );
				$mpdf->SetAuthor( implode( ',', $authors ) );
				$mpdf->SetSubject( get_bloginfo( 'blogdescription' ) );
				$mpdf->WriteHTML( pdfprnt_generate_template( $html ) );
				do_action_ref_array( 'bwsplgns_mpdf', array( &$mpdf ) );
				if ( 'download' == $pdfprnt_options['file_action'] ){
					$mpdf->Output( '', 'D' );
				} else {
					$mpdf->Output();
				}
				die();
				break;
				case 'print':

				$last = count( $posts );
				$i = 1;
				$html = '<div id="content">';
				foreach ( $posts as $p ) {

					$title = 1 == $pdfprnt_options['show_title'] ? '<div class="entry-header"><h1 class="entry-title">' . apply_filters( 'bwsplgns_get_pdf_print_title', apply_filters( 'the_title', $p->post_title, $p->ID ), $p ) . '</h1></div>' : '';
					$image = 1 == $pdfprnt_options['show_featured_image'] && has_post_thumbnail( $p->ID ) ? '<div class="entry-thumbnail">' . get_the_post_thumbnail( $p->ID , $pdfprnt_options['featured_image_size'] ) . '</div>' : '';
					/* replacing shortcodes to an empty string which were added to the content */
					if ( $pattern && preg_match_all( '/'. $pattern .'/s', $p->post_content, $matches ) ) {
						foreach ( array_unique( $matches[0] ) as $value )
							$p->post_content = str_replace( $value, "", $p->post_content );
					}

					$post_content = apply_filters( 'bwsplgns_get_pdf_print_content', $p->post_content, $p );
                    $shortcodes = implode( '|', apply_filters( 'bwsplgns_pdf_print_remove_shortcodes', array( 'vc_', 'az_', 'multilanguage_switcher') ) );
                    $post_content = preg_replace( "/\[\/?({$shortcodes})[^\]]*?\]/", '', $post_content );
					$post_content = apply_filters( 'the_content', $post_content );

					ob_start(); ?>
						<div class="post">
							<?php echo $image .
							$title; ?>
							<div class="entry-content"><?php echo $post_content; ?></div>
						</div>
					<?php $html .= ob_get_clean();
					if ( $i != $last ) {
						$html .= '<br/><hr/><br/>';
						$i ++;
					}
				}
				$html .= '</div>';
				echo pdfprnt_generate_template( $html, true );
				die();
			default:
				break;
		}
	}
}

/* Add query vars */
if ( ! function_exists( 'print_vars_callback' ) ) {
	function print_vars_callback( $query_vars ) {
		$query_vars[] = 'print';
		return $query_vars;
	}
}

/**
 * Class Pdfprnt_ZipArchive for extracting
 * necessary folder from zip-archive
 */
if ( class_exists( 'ZipArchive' ) && ! class_exists( 'Pdfprnt_ZipArchive' ) ) {
	class Pdfprnt_ZipArchive extends ZipArchive {
		/**
		 * constructor of class
		 */
		public function extractSubdirTo( $destination, $subdir ) {
			$errors = array();
			$charset = get_bloginfo( 'charset' );
			// Prepare dirs
			$destination = str_replace( array( "/", "\\" ), DIRECTORY_SEPARATOR, $destination );
			$subdir = str_replace( array( "/", "\\" ), "/", $subdir);

			if ( substr( $destination, mb_strlen( DIRECTORY_SEPARATOR, $charset ) * -1 ) != DIRECTORY_SEPARATOR ) {
				$destination .= DIRECTORY_SEPARATOR;
			}

			if ( substr( $subdir, -1 ) != "/" ) {
				$subdir .= "/";
			}
			// Extract files
			for ( $i = 0; $i < $this->numFiles; $i++ ) {
				$filename = $this->getNameIndex( $i );

				if ( substr( $filename, 0, mb_strlen( $subdir, $charset ) ) == $subdir ) {
					$relativePath = substr( $filename, mb_strlen( $subdir, $charset ) );
					$relativePath = str_replace( array( "/", "\\" ), DIRECTORY_SEPARATOR, $relativePath );

					if ( mb_strlen( $relativePath, $charset ) > 0 ) {
						if ( substr( $filename, -1 ) == "/" ) {
							if ( ! is_dir( $destination . $relativePath ) ) {
								if ( ! @mkdir( $destination . $relativePath, 0755, true ) ) {
									$errors[ $i ] = $filename;
								}
							}
						} else {
							if ( dirname( $relativePath) != "." ) {
								if ( ! is_dir( $destination . dirname( $relativePath ) ) ) {
									// New dir (for file)
									@mkdir( $destination . dirname( $relativePath ), 0755, true );
								}
							}
							// New file
							if ( @file_put_contents( $destination . $relativePath, $this->getFromIndex( $i ) ) === false ) {
								$errors[ $i ] = $filename;
							}
						}
					}
				}
			}
			return $errors;
		}
	}
}

/**
 * Download Zip
*/
if ( ! function_exists( 'pdfprnt_download_zip' ) ) {
	function pdfprnt_download_zip( $zip_file, $upload_dir, $url ) {
		/* check permissions */
		if ( is_writable( $upload_dir ) ) {
			/* load ZIP-archive */
			$result = array();
			$fp = fopen( $zip_file, 'w+' );
			$curl = curl_init();
			$curl_parametres = array(
				CURLOPT_URL			=> $url,
				CURLOPT_FILE		=> $fp,
				CURLOPT_USERAGENT	=> ( isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0" )
			);
			curl_setopt_array( $curl, $curl_parametres );
			if ( curl_exec( $curl ) ) {
				$result['done'] = 'ok';
			} else {
				$result['error'] = curl_error( $curl ) . '<br />' . __( 'Check your internet connection', 'pdf-print' );
			}
			curl_close( $curl );
			fclose( $fp );
		} else {
			$result['error'] = __( 'Cannot load files in to "uploads" folder. Check your permissions', 'pdf-print' );
		}
		return $result;
	}
}

/**
 * Extract additional fonts to uploads/pdf-print-fonts
 * @param     string   $zip_file     path to zip archive
 */
if ( ! function_exists( 'pdfprnt_copy_fonts' ) ) {
	function pdfprnt_copy_fonts( $zip_file, $upload_dir ) {
		global $pdfprnt_options;
		if ( empty( $pdfprnt_options ) ) {
			$pdfprnt_options = get_option( 'pdfprnt_options' );
		}
		$destination = $upload_dir .'/pdf-print-fonts';
		if ( ! file_exists( $destination ) ) {
			mkdir( $destination, 0755, true );
		}
		$result = array();
		/* check permissions */
		if ( is_writable( $destination ) ) {
			$zip = new Pdfprnt_ZipArchive();
			/* open zip-archive */
			if ( true === $zip->open( $zip_file ) ) {
				/* extract folder with fonts */
				$errors = $zip->extractSubdirTo( $destination, "mpdf-master/ttfonts" );
				$zip->close();
				if ( empty( $errors ) ) {
					$result['done'] = __( 'Additional fonts were loaded successfully.', 'pdf-print' );
					unlink( $zip_file );
				} else {
					$result['error'] = __( 'Some errors occurred during loading files.', 'pdf-print' );
				}
			} else {
				$result['error'] = __( 'Can not extract files from zip-archive.', 'pdf-print' );
				unlink( $zip_file );
			}
		} else {
			$result['error'] = sprintf( __( 'Cannot create %s folder. Check your permissions.', 'pdf-print' ), '"uploads/pdf-print-fonts"' );
		}
		$files = scandir( $destination );
		$value = isset( $result['error'] ) ? -1 : count( $files );
		$opt = 'additional_fonts';
		pdfprnt_update_option( $opt, $value, true );
		return $result;
	}
}

/**
 * Extract mPDF library ( verson 7.1.5 ) from zip-archive
 * @param     string    $zip_file          path to zip-archive
 * @param     string    $upload_dir        path to "Upload" folder
 * @param     boolean   $is_network_admin
 * @return    string    $result            result message
 */
if ( ! function_exists( 'pdfprnt_extract_mpdf_library' ) ) {
	function pdfprnt_extract_mpdf_library( $zip_file, $upload_dir ) {
		global $wpdb;

		$result = array();
		/* check permissions */
		if ( is_writable( $upload_dir ) ) {
			$zip = new Pdfprnt_ZipArchive();
			/* open zip-archive */
			if ( true === $zip->open( $zip_file ) ) {
				/* extract folder with fonts */
				$errors = $zip->extractSubdirTo( $upload_dir, "mpdf7" );
				$zip->close();
				if ( empty( $errors ) ) {
					$value = '7.1.5';
					$result['done'] = __( 'The mPDF library V7.1.5 has been loaded successfully.', 'pdf-print' );
					unlink( $zip_file );
				} else {
					$result['error'] = __( 'Some errors occurred during loading files.', 'pdf-print' );
				}
			} else {
				$result['error'] = __( 'Can not extract files from zip-archive.', 'pdf-print' );
				unlink( $zip_file );
			}
		} else {
			$result['error'] = sprintf( __( 'Cannot extract files into %s folder. Check your permissions.', 'pdf-print' ), $upload_dir );
		}
		if ( ! isset( $value ) ) {
			$value = '6.1';
		}

		$opt = 'mpdf_library_version';
		pdfprnt_update_option( $opt, $value, true );
		return $result;
	}
}

/**
 * Download ZIP-archive with MPDF library,
 * copy fonts to folder 'pdf-print-fons'
 */
if ( ! function_exists( 'pdfprnt_load_and_copy' ) ) {
	function pdfprnt_load_and_copy( $zip_file, $upload_dir, $url ) {
		$result = file_exists( $zip_file ) ? array( 'done' => 'ok' ) : pdfprnt_download_zip( $zip_file, $upload_dir, $url );

		if ( plugin_dir_path(__FILE__) == $upload_dir && isset( $result['done'] ) ) {
			$result = pdfprnt_extract_mpdf_library( $zip_file, $upload_dir );
		} elseif ( isset( $result['done'] ) ) {
			$result = pdfprnt_copy_fonts( $zip_file, $upload_dir );
		}
		return $result;
	}
}

/**
 * Function to load fonts for MPDF library
 */
if ( ! function_exists( 'pdfprnt_load_fonts' ) ) {
	function pdfprnt_load_fonts() {
		global $pdfprnt_options;
		if ( empty( $pdfprnt_options ) ) {
			$pdfprnt_options = get_option( 'pdfprnt_options' );
		}
		$ajax_request = isset( $_REQUEST['action'] ) && 'pdfprnt_load_fonts' == $_REQUEST['action'] ? true : false;
		$php_request = isset( $_REQUEST['pdfprnt_action'] ) && 'pdfprnt_load_fonts' == $_REQUEST['pdfprnt_action'] ? true : false;
		$verified = isset( $_REQUEST['pdfprnt_ajax_nonce'] ) && wp_verify_nonce( $_REQUEST['pdfprnt_ajax_nonce'], 'pdfprnt_ajax_nonce' ) ? true : false;
		/* Sourse of the fonts */
		$url = "https://bestwebsoft.com/wp-content/plugins/paid-products/plugins/fontdownloads/?action=loading_fonts";
		if ( ( $ajax_request || $php_request ) && $verified ) {
			$result = array();
			$flag = false;
			/* get path to directory for ZIP-archive uploading */
			if ( is_multisite() ) {
				switch_to_blog( 1 );
				$upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$upload_dir = wp_upload_dir();
			}
			$zip_file = $upload_dir['basedir'] . '/mpdf-master.zip';
			$destination = $upload_dir['basedir'] . '/pdf-print-fonts';
			if ( file_exists( $destination ) ) { /* if folder with fonts already exists */
				if ( is_multisite() ) {
					$network_options = get_site_option( 'pdfprnt_options' ); /* get network options */
					$files = scandir( $destination );
					if ( $network_options['additional_fonts'] == count( $files ) ) { /* if all fonts was loaded successfully */
						$result['done'] = __( 'Additional fonts were loaded successfully.', 'pdf-print' );
						$opt = 'additional_fonts';
						pdfprnt_update_option( $opt, $network_options['additional_fonts'], true );
					} else { /* if something wrong */
						$result = pdfprnt_load_and_copy( $zip_file, $upload_dir['basedir'], $url ); /* load fonts */
					}
				} else {
					$result = pdfprnt_load_and_copy( $zip_file, $upload_dir['basedir'], $url ); /* load fonts */
				}
			} else {
				mkdir( $destination, 0755, true );
				$result = pdfprnt_load_and_copy( $zip_file, $upload_dir['basedir'], $url );
			}
			if ( $ajax_request ) {
				echo json_encode( $result );
			} else {
				return $result;
			}
		}
		if ( $ajax_request ) {
			die();
		}
	}
}

/**
 * Function to upgrade the MPDF library from version 6.1 to verson 7.1.5
 * @param     boolean   $is_network_admin
 * @return    string    $result            result message
 */
if ( ! function_exists( 'pdfprnt_upgrade_library' ) ) {
	function pdfprnt_upgrade_library() {
		global $pdfprnt_options, $wpdb;

		$ajax_request = isset( $_REQUEST['action'] ) && 'pdfprnt_upgrade_library' == $_REQUEST['action'] ? true : false;

		$php_request = isset( $_REQUEST['pdfprnt_action'] ) && 'pdfprnt_upgrade_library' == $_REQUEST['pdfprnt_action'] ? true : false;
		$verified = isset( $_REQUEST['pdfprnt_ajax_nonce'] ) && wp_verify_nonce( $_REQUEST['pdfprnt_ajax_nonce'], 'pdfprnt_ajax_nonce' ) ? true : false;
		$old_dir = plugin_dir_path(__FILE__) . 'mpdf';
		$result = array();
		if ( ( $ajax_request || $php_request ) && true === $verified ) {
			/* get path to directory for ZIP-archive uploading */
			if ( is_multisite() ) {
				switch_to_blog( 1 );
				$upload_dir = plugin_dir_path(__FILE__);
				restore_current_blog();
			} else {
				$upload_dir = plugin_dir_path(__FILE__);
			}
			$zip_file = $upload_dir . 'mpdf7.zip';
			/* Sourse of the MPDF library */
			$url = "https://bestwebsoft.com/wp-content/plugins/paid-products/plugins/pdf-print-mpdf7/?action=loading_library";
			$new_dir = plugin_dir_path(__FILE__) . 'vendor';

			if ( ! file_exists( $new_dir ) ) {
				$result = pdfprnt_load_and_copy( $zip_file, $upload_dir, $url ); /* load library */
			}
			if ( ! isset( $result['error'] ) ) {
				pdfprnt_delete_old_library( $old_dir );
			}
		}
		if ( true === $ajax_request ) {
			echo json_encode( $result );
			wp_die();
		} else {
			return $result;
		}
	}
}

if ( ! function_exists( 'pdfprnt_delete_old_library' ) ) {
	function pdfprnt_delete_old_library( $dir ) {
		$files = scandir( $dir );
		$files = array_diff( $files, array( '.','..' ) );
		foreach ( $files as $file ) {
			( is_dir ("$dir/$file" ) ) ? pdfprnt_delete_old_library( "$dir/$file" ) : unlink( "$dir/$file" );
		}
		return rmdir( $dir );
	}
}

if ( ! function_exists( 'pdfprnt_update_option' ) ) {
	function pdfprnt_update_option( $opt, $value, $update_network = false ) {
		global $pdfprnt_options;
		if ( empty( $pdfprnt_options ) ) {
			$pdfprnt_options = get_option( 'pdfprnt_options' );
		}
		$pdfprnt_options[ $opt ] = $value;
		update_option( 'pdfprnt_options', $pdfprnt_options );
		if ( $update_network ) {
			$network_options = get_site_option( 'pdfprnt_options' );
			$network_options[ $opt ] = $value;
			update_site_option( 'pdfprnt_options', $network_options );
		}
	}
}

if ( ! function_exists( 'pdfprnt_get_pdf_page_sizes' ) ) {
	function pdfprnt_get_pdf_page_sizes() {
		$page_sizes = array( 'A0', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7','A8', 'A9', 'A10', 'B0', 'B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7','B8', 'B9', 'B10', 'C0', 'C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7','C8', 'C9', 'C10', '4A0', '2A0', 'RA0', 'RA1', 'RA2', 'RA3', 'RA4', 'SRA0', 'SRA1', 'SRA2', 'SRA3', 'SRA4', 'Letter', 'Legal', 'Executive', 'Folio', 'Demy', 'Royal','A', 'B'
		);
		$page_sizes = apply_filters( 'pdfprnt_get_pdf_page_sizes', $page_sizes );
		return $page_sizes;
	}
}

if ( ! function_exists ( 'pdfprnt_plugin_banner' ) ) {
	function pdfprnt_plugin_banner() {
		global $hook_suffix, $pdfprnt_plugin_info;
		if ( 'plugins.php' == $hook_suffix ) {
			bws_plugin_banner_to_settings( $pdfprnt_plugin_info, 'pdfprnt_options', 'pdf-print', 'admin.php?page=pdf-print.php' );
		}

		if ( isset( $_REQUEST['page'] ) && 'pdf-print.php' == $_REQUEST['page'] ) {
			bws_plugin_suggest_feature_banner( $pdfprnt_plugin_info, 'pdfprnt_options', 'pdf-print' );
		}
	}
}

/**
 * Buttons Widget
 */
if ( ! class_exists( 'Pdfprnt_Buttons_Widget' ) ) {
	class Pdfprnt_Buttons_Widget extends WP_Widget {
		function __construct() {
			parent::__construct( 'pdfprnt_buttons', __( 'PDF & Print Buttons', 'pdf-print' ), array( 'description' => __( 'Show PDF & Print Buttons on your site', 'pdf-print' ) ) );
		}

		function widget( $args, $instance ) {
			global $pdfprnt_options, $pdfprnt_is_search_archive, $pdfprnt_is_custom_post_type;
			$url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url = esc_url( $url );
			$buttons = '';

			foreach ( array( 'pdf', 'print' ) as $button ) {
				$instance[ $button . '_button_show' ] = ! empty( $instance[ $button . '_button_show' ] ) ? 1 : 0;
				$instance[ $button . '_button_title' ] = strip_tags( $pdfprnt_options['button_title'][ $button ] );
				if (
					isset( $pdfprnt_options['button_image'][ $button ]['type'] ) &&
					in_array( $pdfprnt_options['button_image'][ $button ]['type'], array( 'none', 'default' ) )
				) {
					$instance[ $button . '_button_image' ] = $pdfprnt_options['button_image'][ $button ]['type'];
				} else {
					$instance[ $button . '_button_image' ] = 'default';
				}
			}

			if ( ! empty( $pdfprnt_is_search_archive ) ) {
				$custom_query = '-search';
			} elseif ( ! empty( $pdfprnt_is_custom_post_type ) ) {
				$custom_query = '-custom';
			} else {
				$custom_query = '';
			}

			if ( 1 == $instance['pdf_button_show'] ) {
				$pdf_url = add_query_arg( 'print' , 'pdf' . $custom_query, $url );
				$button_title = ! empty( $instance['pdf_button_title'] ) ? '<span class="pdfprnt-button-title pdfprnt-button-pdf-title">' . $instance['pdf_button_title'] . '</span>' : '';
				$button_image = 'none' != $instance['pdf_button_image'] ? '<img src="' . plugins_url( 'images/pdf.png', __FILE__ ) . '" alt="image_pdf" title="' . __( 'View PDF', 'pdf-print' ) . '" />' : '';
				$buttons .= sprintf(
					'<a href="%s" class="pdfprnt-button pdfprnt-button-pdf" target="_blank">%s%s</a>',
					$pdf_url,
					$button_image,
					$button_title
				);
			}
			if ( 1 == $instance['print_button_show'] ) {
				$print_url = add_query_arg( 'print' , 'print' . $custom_query, $url );
				$button_title = ! empty( $instance['print_button_title'] ) ? '<span class="pdfprnt-button-title pdfprnt-button-pdf-title">' . $instance['print_button_title'] . '</span>' : '';
				$button_image = 'none' != $instance['print_button_image'] ? '<img src="' . plugins_url( 'images/print.png', __FILE__ ) . '" alt="image_print" title="' . __( 'Print Content', 'pdf-print' ) . '" />' : '';
				$buttons .= sprintf(
					'<a href="%s" class="pdfprnt-button pdfprnt-button-print" target="_blank">%s%s</a>',
					$print_url,
					$button_image,
					$button_title
				);
			}

			if ( ! empty( $buttons ) ) {
				$title = ( ! empty( $instance['title'] ) ) ? apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) : '';
				$buttons = sprintf(
					'<div class="pdfprnt-buttons pdfprnt-widget">%s</div>',
					$buttons
				);
				echo $args['before_widget'];
				if ( ! empty( $title ) ) {
					echo $args['before_title'] . $title . $args['after_title'];
				}
				echo $buttons;
				echo $args['after_widget'];
			}
		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['pdf_button_show'] = ! empty( $new_instance['pdf_button_show'] ) ? 1 : 0;
			$instance['print_button_show'] = ! empty( $new_instance['print_button_show'] ) ? 1 : 0;
			return $instance;
		}

		function form( $instance ) {
			global $pdfprnt_options;
			if ( empty( $pdfprnt_options ) ) {
				$pdfprnt_options = get_option( 'pdfprnt_options' );
			}

			if ( ! empty( $instance ) ) {
				$title = ! empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
				$pdf_button_show = ! empty( $instance['pdf_button_show'] ) ? 1 : 0;
				$print_button_show = ! empty( $instance['print_button_show'] ) ? 1 : 0;
			} else {
				$title = '';
				$pdf_button_show = 1;
				$print_button_show = 1;
			} ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'pdf-print' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<input class="widefat pdfprnt-show-button-cb" id="<?php echo $this->get_field_id( 'pdf_button_show' ); ?>" name="<?php echo $this->get_field_name( 'pdf_button_show' ); ?>" type="checkbox" value="1" <?php checked( 1, $pdf_button_show ); ?> />
				<label for="<?php echo $this->get_field_id( 'pdf_button_show' ); ?>"><?php _e( 'Display PDF Button', 'pdf-print' ); ?></label>
			</p>
			<p>
				<input class="widefat pdfprnt-show-button-cb" id="<?php echo $this->get_field_id( 'print_button_show' ); ?>" name="<?php echo $this->get_field_name( 'print_button_show' ); ?>" type="checkbox" value="1" <?php checked( 1, $print_button_show ); ?> />
				<label for="<?php echo $this->get_field_id( 'print_button_show' ); ?>"><?php _e( 'Display Print Button', 'pdf-print' ); ?></label>
			</p>
		<?php }
	}
}

/**
 * Register buttons Widget
 */
if ( ! function_exists( 'pdfprnt_register_buttons_widget' ) ) {
	function pdfprnt_register_buttons_widget() {
		global $pdfprnt_is_old_php;
		if ( ! $pdfprnt_is_old_php ) {
			register_widget( 'Pdfprnt_Buttons_Widget' );
		}
	}
}

/**
 * Deleting plugin options on uninstalling
 */
if ( ! function_exists( 'pdfprnt_uninstall' ) ) {
	function pdfprnt_uninstall() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$all_plugins = get_plugins();

		if ( ! array_key_exists( 'pdf-print-pro/pdf-print-pro.php', $all_plugins ) ) {
			global $wpdb;
			if ( is_multisite() ) {
				/* Get all blog ids */
				$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
				$tables = '';
				$old_blog = $wpdb->blogid;
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					delete_option( 'pdfprnt_options' );
					delete_option( 'widget_pdfprint_buttons' );
				}
				switch_to_blog( $old_blog );
				delete_site_option( 'pdfprnt_options' );
				delete_option( 'widget_pdfprnt_buttons' );
			} else {
				delete_option( 'pdfprnt_options' );
				delete_option( 'widget_pdfprnt_buttons' );
			}
		}

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

/* Adding function to output PDF document or pirnt page */
register_activation_hook( __FILE__, 'pdfprnt_plugin_activate' );
add_action( 'wp', 'pdfprnt_print' );
add_action( 'wp_head', 'pdfprnt_auto_show_buttons_search_archive' );
add_action( 'plugins_loaded', 'pdfprnt_plugins_loaded' );
/* Initialization */
add_action( 'init', 'pdfprnt_init' );
add_action( 'admin_init', 'pdfprnt_admin_init' );
/* Adding stylesheets */
add_action( 'admin_enqueue_scripts', 'pdfprnt_admin_head' );
add_action( 'wp_enqueue_scripts', 'pdfprnt_admin_head' );
/* Adding 'BWS Plugins' admin menu */
add_action( 'admin_menu', 'pdfprnt_add_admin_menu' );
/* Add query vars */
add_filter( 'query_vars', 'print_vars_callback' );

add_shortcode( 'bws_pdfprint', 'pdfprnt_shortcode' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'pdfprnt_shortcode_button_content' );

/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'pdfprnt_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'pdfprnt_links', 10, 2 );
/* Adding buttons plugin to content */
add_filter( 'the_content', 'pdfprnt_content' );
/* load additional fonts */
add_action( 'wp_ajax_pdfprnt_load_fonts', 'pdfprnt_load_fonts' );
/* load mPDF library */
add_action( 'wp_ajax_pdfprnt_upgrade_library', 'pdfprnt_upgrade_library' );
/* Adding banner */
add_action( 'admin_notices', 'pdfprnt_plugin_banner' );

/* Register widget */
add_action( 'widgets_init', 'pdfprnt_register_buttons_widget' );

add_action( 'bwsplgns_display_pdf_print_buttons', 'pdfprnt_display_plugin_buttons', 10, 2 );
