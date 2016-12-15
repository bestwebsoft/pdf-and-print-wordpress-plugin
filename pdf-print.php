<?php
/*
Plugin Name: PDF & Print by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/wordpress/plugins/pdf-print/
Description: Generate PDF files and print WordPress posts/pages. Customize document header/footer styles and appearance.
Author: BestWebSoft
Text Domain: pdf-print
Domain Path: /languages
Version: 1.9.3
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*  © Copyright 2016  BestWebSoft  ( http://support.bestwebsoft.com )

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( dirname( __FILE__ ) . '/includes/deprecated.php' );

/* Add our own menu */
if ( ! function_exists( 'pdfprnt_admin_menu' ) ) {
	function pdfprnt_admin_menu() {
		bws_general_menu();
		$settings = add_submenu_page( 'bws_panel', __( 'PDF & Print Settings', 'pdf-print' ), 'PDF & Print', 'manage_options', 'pdf-print.php', 'pdfprnt_settings_page' );
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
			'id'      => 'pdfprnt',
			'section' => '200538669'
		);
		bws_help_tab( get_current_screen(), $args );
	}
}

if ( ! function_exists( 'pdfprnt_plugins_loaded' ) ) {
	function pdfprnt_plugins_loaded() {
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'pdf-print', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Init plugin */
if ( ! function_exists ( 'pdfprnt_init' ) ) {
	function pdfprnt_init() {
		global $pdfprnt_plugin_info;
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
		bws_wp_min_version_check( $plugin_basename, $pdfprnt_plugin_info, '3.8' );

		/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && 'pdf-print.php' == $_GET['page'] ) )
			pdfprnt_settings();
	}
}

if ( ! function_exists( 'pdfprnt_admin_init' ) ) {
	function pdfprnt_admin_init() {
		global $bws_plugin_info, $pdfprnt_plugin_info, $bws_shortcode_list;

		if ( empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '101', 'version' => $pdfprnt_plugin_info["Version"] );
		/* add PDF&Print to global $bws_shortcode_list ##*/
		$bws_shortcode_list['pdfprnt'] = array( 'name' => 'PDF&Print', 'js_function' => 'pdfprnt_shortcode_init' );
	}
}

/* Register settings for plugin */
if ( ! function_exists( 'pdfprnt_settings' ) ) {
	function pdfprnt_settings() {
		global $pdfprnt_options, $pdfprnt_plugin_info, $pdfprnt_options_defaults;

		if ( ! $pdfprnt_plugin_info ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$pdfprnt_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Variable to verify performance number of once function. */
		$pdfprnt_default_post_types		=	array();
		/* Default post types of WordPress. */
		foreach ( get_post_types( array( 'public' => 1, 'show_ui' => 1,	'_builtin' => true ), 'names' ) as $value )
			$pdfprnt_default_post_types[]	=	$value;

		$pdfprnt_options_defaults		=	array(
			'plugin_option_version' 		=> $pdfprnt_plugin_info["Version"],
			'position'						=>	'top-right',
			'position_search_archive'		=>	'top-right',
			'position_custom'				=>	'right',
			'show_btn_print'				=>	1,
			'show_btn_pdf'					=>	1,
			'show_btn_print_search_archive'	=>	1,
			'show_btn_pdf_search_archive'	=>	1,
			'use_theme_stylesheet'			=>	0,
			'use_custom_styles'				=>	0,
			'custom_styles'					=>	"",
			'tmpl_shorcode'					=>	1,
			'use_types_posts'				=>	$pdfprnt_default_post_types,
			'show_print_window'				=>	0,
			'additional_fonts'				=>	0,
			'show_title'					=>	1,
			'show_featured_image'			=>	0,
			'pdf_button_image'				=>	'default', /* 'default', 'none' */
			'print_button_image'			=>	'default', /* 'default', 'none' */
			'pdf_button_title'				=>	'',
			'print_button_title'			=>	'',
			'pdf_margins'					=>	array(
													'left'		=> '15',
													'right'		=> '15',
													'top'		=> '16',
													'bottom'	=> '16'
												),
			'first_install'					=>	strtotime( "now" ),
			'suggest_feature_banner'		=>	1
		);

		pdfprnt_check_old_options();

		if ( ! get_option( 'pdfprnt_options' ) )
			add_option( 'pdfprnt_options', $pdfprnt_options_defaults );

		$pdfprnt_options	= get_option( 'pdfprnt_options' );

		if ( ! isset( $pdfprnt_options['plugin_option_version'] ) || $pdfprnt_options['plugin_option_version'] != $pdfprnt_plugin_info["Version"] ) {
			if ( in_array( $pdfprnt_options['position_search_archive'], array( 'pdfprnt-right', 'pdfprnt-left' ) ) )
				$pdfprnt_options['position_search_archive'] = 'pdfprnt-left' == $pdfprnt_options['position_search_archive'] ? 'top-left' : 'top-right';
			if ( in_array( $pdfprnt_options['position_custom'], array( 'pdfprnt-right', 'pdfprnt-left' ) ) )
				$pdfprnt_options['position_custom'] = 'pdfprnt-left' == $pdfprnt_options['position_custom'] ? 'left' : 'right';
			$pdfprnt_options = array_merge( $pdfprnt_options_defaults, $pdfprnt_options );
			$pdfprnt_options['plugin_option_version'] = $pdfprnt_plugin_info["Version"];
			$pdfprnt_options['hide_premium_options'] = array();

			update_option( 'pdfprnt_options', $pdfprnt_options );

			if ( is_multisite() ) {
				switch_to_blog( 1 );
				register_uninstall_hook( __FILE__, 'pdfprnt_uninstall' );
				restore_current_blog();
			} else {
				register_uninstall_hook( __FILE__, 'pdfprnt_uninstall' );
			}
		}
	}
}

/**
 * Display <input type="radio">  on settings page
 **/
if ( ! function_exists( 'pdfprnt_display_radio' ) ) {
	function pdfprnt_display_radio( $name, $selected ) {
		$positions_values	=	array(
			'top-left'		=>	__( 'Top Left', 'pdf-print' ),
			'top-right'		=>	__( 'Top Right', 'pdf-print' ),
			'bottom-left'	=>	__( 'Bottom Left', 'pdf-print' ),
			'bottom-right'	=>	__( 'Bottom Right', 'pdf-print' ),
			'top-bottom-left'	=>	__( 'Top & Bottom Left', 'pdf-print' ),
			'top-bottom-right'	=>	__( 'Top & Bottom Right', 'pdf-print' )
		);
		foreach ( $positions_values as $key => $value ) { ?>
			<label><input type="radio" name="<?php echo $name; ?>" value="<?php echo $key ?>"<?php echo $key == $selected ? ' checked="checked"' : ''; ?> />&nbsp;<?php echo $value; ?></label><br/>
		<?php }
	}
}
/* Add admin page */
if ( ! function_exists ( 'pdfprnt_settings_page' ) ) {
	function pdfprnt_settings_page () {
		global $pdfprnt_options, $pdfprnt_plugin_info, $pdfprnt_options_defaults;
		$message = $error  = "";
		$plugin_basename   = plugin_basename( __FILE__ );
		if ( is_multisite() ) {
			switch_to_blog( 1 );
			$upload_dir = wp_upload_dir();
			restore_current_blog();
		} else {
			$upload_dir = wp_upload_dir();
		}
		$need_fonts_reload = false;
		$fonts_path        = $upload_dir['basedir'] .'/pdf-print-fonts';
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		require_once( dirname( __FILE__ ) . '/includes/pro_banners.php' );
		$all_plugins = get_plugins();
		if ( isset( $_REQUEST['pdfprnt_form_submit'] ) && check_admin_referer( $plugin_basename, 'pdfprnt_nonce_name' ) ) {
			if ( isset( $_POST['bws_hide_premium_options'] ) ) {
				$hide_result = bws_hide_premium_options( $pdfprnt_options );
				$pdfprnt_options = $hide_result['options'];
			}

			$pdfprnt_options['position']						=	isset( $_REQUEST['pdfprnt_position'] ) ? $_REQUEST['pdfprnt_position'] : 'top-right';
			$pdfprnt_options['position_search_archive']			=	isset( $_REQUEST['pdfprnt_position_search_archive'] ) ? $_REQUEST['pdfprnt_position_search_archive'] : 'top-right';
			$pdfprnt_options['position_custom']					=	isset( $_REQUEST['pdfprnt_position_custom'] ) ? $_REQUEST['pdfprnt_position_custom'] : 'right';
			$pdfprnt_options['use_theme_stylesheet']			=	isset( $_REQUEST['pdfprnt_use_theme_stylesheet'] ) ? $_REQUEST['pdfprnt_use_theme_stylesheet'] : 0;
			$pdfprnt_options['show_btn_pdf']					=	isset( $_REQUEST['pdfprnt_show_btn_pdf'] ) ? 1 : 0;
			$pdfprnt_options['show_btn_print']					=	isset( $_REQUEST['pdfprnt_show_btn_print'] ) ? 1 : 0;
			$pdfprnt_options['show_btn_pdf_search_archive']		=	isset( $_REQUEST['pdfprnt_show_btn_pdf_search_archive'] ) ? 1 : 0;
			$pdfprnt_options['show_btn_print_search_archive']	=	isset( $_REQUEST['pdfprnt_show_btn_print_search_archive'] ) ? 1 : 0;
			$pdfprnt_options['tmpl_shorcode']					=	isset( $_REQUEST['pdfprnt_tmpl_shorcode'] ) ? 1 : 0;
			$pdfprnt_options['show_print_window']				=	isset( $_REQUEST['pdfprnt_show_print_window'] ) ? 1 : 0;
			$pdfprnt_options['use_types_posts']					=	isset( $_REQUEST['pdfprnt_use_types_posts'] ) ? $_REQUEST['pdfprnt_use_types_posts'] : array();
			$pdfprnt_options['use_custom_styles']				=	isset( $_REQUEST['pdfprnt_use_custom_styles'] ) ? 1 : 0;
			$pdfprnt_options['show_title']						=	isset( $_REQUEST['pdfprnt_show_title'] ) ? 1 : 0;
			$pdfprnt_options['show_featured_image']				=	isset( $_REQUEST['pdfprnt_show_featured_image'] ) ? 1 : 0;

			$pdfprnt_options['pdf_button_title']				=	isset( $_REQUEST['pdfprnt_pdf_button_title'] ) ? stripslashes( sanitize_text_field( $_REQUEST['pdfprnt_pdf_button_title'] ) ) : '';
			$pdfprnt_options['print_button_title']				=	isset( $_REQUEST['pdfprnt_print_button_title'] ) ? stripslashes( sanitize_text_field( $_REQUEST['pdfprnt_print_button_title'] ) ) : '';
			$pdfprnt_options['pdf_button_image']				= (
				isset( $_REQUEST['pdfprnt_pdf_button_image'] ) &&
				'none' == $_REQUEST['pdfprnt_pdf_button_image'] &&
				! empty( $pdfprnt_options['pdf_button_title'] )
			) ?
				'none'
			:
				'default';

			$pdfprnt_options['print_button_image']				= (
				isset( $_REQUEST['pdfprnt_print_button_image'] ) &&
				'none' == $_REQUEST['pdfprnt_print_button_image'] &&
				! empty( $pdfprnt_options['print_button_title'] )
			) ?
				'none'
			:
				'default';

			foreach ( $pdfprnt_options['pdf_margins'] as $margin => $value ) {
				$pdfprnt_options['pdf_margins'][ $margin ] =
					isset( $_REQUEST[ 'pdfprnt_pdf_margin_' . $margin ] )
				?
					intval( $_REQUEST[ 'pdfprnt_pdf_margin_' . $margin ] )
				:
					$pdfprnt_options['pdf_margins'][ $margin ];
			}

			if ( isset( $_REQUEST['pdfprnt_custom_styles'] ) ) {
				$custom_styles = trim( strip_tags( stripslashes( $_REQUEST['pdfprnt_custom_styles'] ) ) );
				if ( 10000 < strlen( $custom_styles ) )
					$error .= __( 'You have entered too much text in the "edit styles" field.', 'pdf-print' );
				else
					$pdfprnt_options['custom_styles'] = $custom_styles;
			}
			update_option( 'pdfprnt_options', $pdfprnt_options );
			$message	=	__( 'Settings saved.', 'pdf-print' );
		}
		if ( ! is_dir( $fonts_path ) && $pdfprnt_options['additional_fonts'] != 0 ) { /* if "pdf-print-fonts" folder was removed somehow */
			$error = __( 'The folder "uploads/pdf-print-fonts" was removed', 'pdf-print' );
			$need_fonts_reload = true;
		} elseif (
			is_dir( $fonts_path ) &&
			$pdfprnt_options['additional_fonts'] != count( scandir( $fonts_path ) ) &&
			0 < $pdfprnt_options['additional_fonts']
		) { /* if some fonts was removed somehow from "pdf-print-fonts" folder */
			$error = __( 'Some fonts were removed from the folder "uploads/pdf-print-fonts"', 'pdf-print' );
			$need_fonts_reload = true;
		}
		if ( $need_fonts_reload ) {
			$error .= '.&nbsp;' . sprintf(
				__( 'You may need to reload fonts. For more info see %s', 'pdf-print' ),
				'<a href="http://support.bestwebsoft.com/hc/en-us/sections/200538669" target="_blank">' . __( 'FAQ', 'pdf-print' ) . '</a>'
			);
			pdfprnt_update_option( -1, true );
		}
		if ( isset( $_REQUEST['pdfprnt_load_fonts'] ) ) {
			/* load additional fonts if javascript is disabled */
			$result = pdfprnt_load_fonts();
			if ( isset( $result['error'] ) ) {
				$error .= '&nbsp;' . $result['error'] . '.&nbsp;' .
					sprintf(
						__( 'You may need to reload fonts. For more info see %s', 'pdf-print' ),
						'<a href="http://support.bestwebsoft.com/hc/en-us/sections/200538669" target="_blank">' . __( 'FAQ', 'pdf-print' ) . '</a>'
					);
				$need_fonts_reload = true;
			}
			if ( isset( $result['done'] ) )
				$message .= '&nbsp;' . $result['done'];
		}
		if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
			$pdfprnt_options = $pdfprnt_options_defaults;
			update_option( 'pdfprnt_options', $pdfprnt_options );
			$message = __( 'All plugin settings were restored.', 'pdf-print' );
		}

		/* GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
			$go_pro_result = bws_go_pro_tab_check( $plugin_basename, 'pdfprnt_options' );
			if ( ! empty( $go_pro_result['error'] ) )
				$error = $go_pro_result['error'];
			elseif ( ! empty( $go_pro_result['message'] ) )
				$message = $go_pro_result['message'];
		} ?>
		<div class="wrap">
			<h1>PDF &amp; Print<?php if ( isset( $_GET['action'] ) && 'templates' == $_GET['action'] ) { ?> <a href="admin.php?page=pdf-print.php&amp;action=templates&amp;pdfprnt_tab_action=new" class="add-new-h2 pdfprnt_add_new_button"><?php _e( 'Add New Running Titles', 'pdf-print' ); ?></a><?php } ?></h1>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=pdf-print.php"><?php _e( 'Settings', 'pdf-print' ); ?></a>
				<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'extra' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=pdf-print.php&amp;action=extra"><?php _e( 'Extra settings', 'pdf-print' ); ?></a>
				<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'templates' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=pdf-print.php&amp;action=templates"><?php _e( 'Running Titles', 'pdf-print' ); ?></a>
				<a class="nav-tab <?php if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=pdf-print.php&amp;action=custom_code"><?php _e( 'Custom code', 'pdf-print' ); ?></a>
				<a class="nav-tab bws_go_pro_tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=pdf-print.php&amp;action=go_pro"><?php _e( 'Go PRO', 'pdf-print' ); ?></a>
			</h2>
			<div class="updated fade below-h2" <?php if ( empty( $message ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="error below-h2" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php if ( ! empty( $hide_result['message'] ) ) { ?>
				<div class="updated fade below-h2"><p><strong><?php echo $hide_result['message']; ?></strong></p></div>
			<?php }
			$ttfontdata = plugin_dir_path( __FILE__ ) . 'mpdf/ttfontdata';
			if ( ! is_writable( $ttfontdata ) ) {
				if ( ! @chmod( $ttfontdata, 0755 ) ) { ?>
					<div class="error below-h2">
						<p>
							<strong>
								<?php _e( "Warning: Not enough rights for folder", 'pdf-print' ); ?>&nbsp;<i><?php echo $ttfontdata; ?></i>.<br />
								<?php _e( 'Please check and change permissions for your plugins` folder ( for folders - 755, for files - 644 ). For more info see', 'pdf-print' ); ?>&nbsp;
								<a href="https://codex.wordpress.org/Changing_File_Permissions" target="_blank"><?php _e( 'Changing File Permissions', 'pdf-print' ); ?></a>
								&nbsp;<?php _e( 'and', 'pdf-print' ); ?>&nbsp;
								<a href="http://support.bestwebsoft.com/hc/en-us/sections/200538669" target="_blank"><?php _e( 'FAQ', 'pdf-print' ); ?></a>.
							</strong>
						</p>
					</div>
				<?php }
			}
			if ( ! isset( $_GET['action'] ) ) {
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( $plugin_basename );
				} else {
					bws_show_settings_notice(); ?>
					<form method="post" action="admin.php?page=pdf-print.php" id="pdfprnt_settings_form" class="bws_form">
						<table class="form-table pdfprnt_settings_table">
							<tr>
								<th scope="row"><?php _e( 'Types of posts that will be used in the plugin', 'pdf-print' ); ?></th>
								<td>
									<select name="pdfprnt_use_types_posts[]" multiple="multiple">
										<?php foreach ( get_post_types( array( 'public' => 1, 'show_ui' => 1 ), 'objects' ) as $key => $value  ) {
											if ( 'attachment' != $key ) { ?>
												<option value="<?php echo $key; ?>" <?php if ( in_array( $key, $pdfprnt_options['use_types_posts'] ) ) echo 'selected="selected"'; ?>><?php echo $value->label; ?></option>
											<?php }
										} ?>
									</select>
								</td>
							</tr>
						</table>
						<table class="form-table pdfprnt_settings_table pdfprnt_buttons">
							<tr class="pdfprnt_table_head">
								<th scope="row"></th>
								<th><?php _e( 'Posts and pages', 'pdf-print' ); ?></th>
								<th><?php _e( 'Search and archive pages', 'pdf-print' ); ?></th>
							</tr>
							<tr class="pdfprnt_pdf_button">
								<th scope="row"><?php _e( 'Show PDF button', 'pdf-print' ); ?></th>
								<td>
									<input type="checkbox" name="pdfprnt_show_btn_pdf" <?php if ( 1 == $pdfprnt_options['show_btn_pdf'] ) echo 'checked="checked"'; ?> />
								</td>
								<td>
									<input type="checkbox" name="pdfprnt_show_btn_pdf_search_archive" <?php if ( 1 == $pdfprnt_options['show_btn_pdf_search_archive'] ) echo 'checked="checked"'; ?> />
								</td>
							</tr>
							<tr class="pdfprnt_print_button">
								<th scope="row"><?php _e( 'Show Print button', 'pdf-print' ); ?></th>
								<td>
									<input type="checkbox" name="pdfprnt_show_btn_print" <?php if ( 1 == $pdfprnt_options['show_btn_print'] ) echo 'checked="checked"'; ?> />
								</td>
								<td>
									<input type="checkbox" name="pdfprnt_show_btn_print_search_archive" <?php if ( 1 == $pdfprnt_options['show_btn_print_search_archive'] ) echo 'checked="checked"'; ?> />
								</td>
							</tr>
							<tr class="pdfprnt_position_button">
								<th scope="row"><?php _e( 'Position of buttons in the content', 'pdf-print' ); ?></th>
								<td>
									<fieldset><?php pdfprnt_display_radio( 'pdfprnt_position', $pdfprnt_options['position'] ); ?></fieldset>
								</td>
								<td>
									<fieldset><?php pdfprnt_display_radio( 'pdfprnt_position_search_archive', $pdfprnt_options['position_search_archive'] ); ?></fieldset>
								</td>
							</tr>
							<tr class="pdfprnt_button_layout pdfprnt_pdf_button_layout">
								<th scope="row"><?php _e( 'PDF button', 'pdf-print' ); ?></th>
								<td colspan="2">
									<fieldset>
										<span><strong><?php _e( 'Image', 'pdf-print' ); ?></strong></span>
										<select name="pdfprnt_pdf_button_image">
											<option value="default"<?php if ( 'none' != $pdfprnt_options['pdf_button_image'] ) echo ' selected="selected"'; ?>>
												<?php _e( 'Default', 'pdf-print' ); ?>
											</option>
											<option value="none"<?php if ( 'none' == $pdfprnt_options['pdf_button_image'] ) echo ' selected="selected"'; ?>>
												<?php _e( 'None', 'pdf-print' ); ?>
											</option>
											<option disabled="disabled">
												<?php _e( 'Custom (available in PRO)', 'pdf-print' ); ?>
											</option>
										</select>
										<?php pdfprnt_pro_block( 'pdfprnt_pdf_image_block' ); ?>
										<span><strong><?php _e( 'Title', 'pdf-print' ); ?></strong></span>
										<label>
											<input id="pdfprnt_pdf_button_title" type="text" value="<?php echo $pdfprnt_options['pdf_button_title']; ?>" name="pdfprnt_pdf_button_title">
										</label>
									</fieldset>
								</td>
							</tr>
							<tr class="pdfprnt_button_layout pdfprnt_print_button_layout">
								<th scope="row"><?php _e( 'Print button', 'pdf-print' ); ?></th>
								<td colspan="2">
									<fieldset>
										<span><strong><?php _e( 'Image', 'pdf-print' ); ?></strong></span>
										<select name="pdfprnt_print_button_image">
											<option value="default"<?php if ( 'none' != $pdfprnt_options['print_button_image'] ) echo ' selected="selected"'; ?>>
												<?php _e( 'Default', 'pdf-print' ); ?>
											</option>
											<option value="none"<?php if ( 'none' == $pdfprnt_options['print_button_image'] ) echo ' selected="selected"'; ?>>
												<?php _e( 'None', 'pdf-print' ); ?>
											</option>
											<option disabled="disabled">
												<?php _e( 'Custom (available in PRO)', 'pdf-print' ); ?>
											</option>
										</select>
										<?php pdfprnt_pro_block( 'pdfprnt_print_image_block' ); ?>
										<span><strong><?php _e( 'Title', 'pdf-print' ); ?></strong></span>
										<label>
											<input id="pdfprnt_print_button_title" type="text" value="<?php echo $pdfprnt_options['print_button_title']; ?>" name="pdfprnt_print_button_title">
										</label>
									</fieldset>
								</td>
							</tr>
						</table>
						<div>
							<p>
								<?php _e( 'In order to use PDF and Print buttons in the custom post or page template, see', 'pdf-print' ); ?>&nbsp;<a href="http://support.bestwebsoft.com/hc/en-us/sections/200538669" target="_blank"><?php _e( 'FAQ', 'pdf-print' ); ?></a>
							</p>
						</div>
						<?php pdfprnt_pro_block( 'pdfprnt_layout_block' ); ?>
						<table class="form-table pdfprnt_settings_table">
							<tr id="pdfprnt_load_fonts_button">
								<th scope="row"><?php _e( 'Load additional fonts', 'pdf-print' ); ?></th>
								<td style="position: relative;">
									<?php if ( class_exists( 'ZipArchive' ) ) {
										$fonts_button_title =
												0 < $pdfprnt_options['additional_fonts'] || /* loading not called yet */
												$need_fonts_reload /* loading occurred with errors or neccessary files lacks */
											?
												__( 'Reload Fonts', 'pdf-print' )
											:
												__( 'Load Fonts', 'pdf-print' ); ?>
										<input type="submit" class="button bws_no_bind_notice" value="<?php echo $fonts_button_title; ?>" name="pdfprnt_load_fonts" />&nbsp;<span id="pdfprnt_font_loader" class="pdfprnt_loader"><img src="<?php echo plugins_url( 'images/ajax-loader.gif', __FILE__ ); ?>" alt="loader" /></span><br />
										<input type="hidden" name="pdfprnt_action" value="pdfprnt_load_fonts" />
										<input type="hidden" name="pdfprnt_ajax_nonce" value="<?php echo wp_create_nonce( 'pdfprnt_ajax_nonce' ); ?>" />
										<?php if ( 0 < $pdfprnt_options['additional_fonts'] ) { ?>
											<span><?php _e( 'Additional fonts were loaded successfully', 'pdf-print' ); ?>.</span>
										<?php } else {
											if ( -1 == $pdfprnt_options['additional_fonts'] ) { ?>
												<span><?php _e( 'If you have some problems with your internet connection, please, try to load additional fonts manually. For more info see', 'pdf-print' ); ?>&nbsp;<a href="http://support.bestwebsoft.com/hc/en-us/sections/200538669" target="_blank"><?php _e( 'FAQ', 'pdf-print' ); ?></a>.</span><br />
											<?php } ?>
											<span class="bws_info"><?php _e( 'You can load additional fonts, needed for the PDF creation. When creating the PDF-doc, this will allow automatic selection of fonts necessary for text, according to languages used in the content.', 'pdf-print' ); ?></span>
										<?php }
									} else { ?>
										<span style="color: red"><strong><?php _e( 'WARNING', 'pdf-print' ); ?>:&nbsp;</strong><?php _e( 'Class ZipArchive is not installed on your server. It is impossible to load additional fonts.', 'pdf-print' ); ?></span>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Use the theme stylesheet or plugin default style', 'pdf-print' ); ?></th>
								<td>
									<select name="pdfprnt_use_theme_stylesheet">
										<option value="0" <?php if ( 0 == $pdfprnt_options['use_theme_stylesheet'] ) echo 'selected="selected"'; ?>><?php echo __( 'Default stylesheet', 'pdf-print' ); ?></option>
										<option value="1" <?php if ( 1 == $pdfprnt_options['use_theme_stylesheet'] ) echo 'selected="selected"'; ?>><?php echo __( 'Current theme stylesheet', 'pdf-print' ); ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Add custom styles', 'pdf-print' ); ?></th>
								<td>
									<input value="1" type="checkbox" name="pdfprnt_use_custom_styles" <?php if ( 1 == $pdfprnt_options['use_custom_styles'] ) echo 'checked="checked"'; ?> />
									<div class="bws_help_box dashicons dashicons-editor-help">
										<div class="bws_hidden_help_text" style="min-width: 200px;">
											<p>
												<?php _e( 'Additional CSS-styles will be applied to your PDF/Print document', 'pdf-print' ); ?>.<br/>
												<?php _e( 'Learn more about', 'pdf-print' ); ?>&nbsp;<a href="http://www.w3schools.com/css/" target="_blank">CSS</a>.
											</p>
										</div>
									</div>
								</td>
							</tr>
							<tr class="pdfprnt_custom_styles"<?php if ( 1 != $pdfprnt_options['use_custom_styles'] ) echo ' style="display: none;"'; ?>>
								<th scope="row"></th>
								<td>
									<textarea id="pdfprnt_custom_styles" name="pdfprnt_custom_styles" maxlength="10000" cols="50" rows="5"><?php echo $pdfprnt_options['custom_styles']; ?></textarea><br />
									<span class="bws_info"><?php _e( 'You can enter up to 10,000 characters', 'pdf-print' ); ?>.</span>

								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Display additional elements', 'pdf-print' ); ?></th>
								<td><fieldset>
									<label><input type="checkbox" name="pdfprnt_show_title" <?php if ( 1 == $pdfprnt_options['show_title'] ) echo 'checked="checked"'; ?> />&nbsp;<?php _e( 'Title', 'pdf-print' ); ?></label><br/>
									<label><input type="checkbox" name="pdfprnt_show_featured_image"  <?php if ( 1 == $pdfprnt_options['show_featured_image'] ) echo 'checked="checked"'; ?> />&nbsp;<?php _e( 'Featured image', 'pdf-print' ); ?></label>
								</fieldset></td>
							</tr>
							<tr>
								<th scope="row">
									<?php _e( 'Settings for shortcodes', 'pdf-print' ); ?>
								</th>
								<td>
									<label><input type="checkbox" name="pdfprnt_tmpl_shorcode"  <?php if ( 1 == $pdfprnt_options['tmpl_shorcode'] ) echo 'checked="checked"'; ?> /> <span><?php _e( 'Do!', 'pdf-print' ); ?></span></label>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php _e( 'PDF margins', 'pdf-print' ); ?>
									<div class="bws_help_box dashicons dashicons-editor-help">
										<div class="bws_hidden_help_text">
											<img src="<?php echo plugins_url( 'images/margins-sample.png', __FILE__ ); ?>">
										</div>
									</div>
								</th>
								<td class="pdfprnt_pdf_margin_settings"><table>
									<?php $pdf_margins = array(
										'left' 		=> __( 'Left', 'pdf-print' ),
										'right' 	=> __( 'Right', 'pdf-print' ),
										'top' 		=> __( 'Top', 'pdf-print' ),
										'bottom' 	=> __( 'Bottom', 'pdf-print' )
									);
									foreach ( $pdf_margins as $margin => $margin_name ) { ?>
										<tr>
											<th><?php echo $margin_name; ?></th>
											<td><input type="number" name="pdfprnt_pdf_margin_<?php echo $margin; ?>" min="0" max="297" step="1" value="<?php echo $pdfprnt_options['pdf_margins'][ $margin ]; ?>"></td>
										</tr>
									<?php } ?></table>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Show the print window', 'pdf-print' ); ?></th>
								<td>
									<input type="checkbox" name="pdfprnt_show_print_window" <?php if ( 1 == $pdfprnt_options['show_print_window'] ) echo 'checked="checked"'; ?> />
								</td>
							</tr>
						</table>
						<?php pdfprnt_pro_block( 'pdfprnt_woocommerce_block' ); ?>
						<input type="hidden" name="pdfprnt_form_submit" value="1" />
						<p class="submit">
							<input type="submit" id="bws-submit-button" class="button-primary" value="<?php _e( 'Save Changes', 'pdf-print' ); ?>" />
						</p>
						<?php wp_nonce_field( $plugin_basename, 'pdfprnt_nonce_name' ); ?>
					</form>
					<?php bws_form_restore_default_settings( $plugin_basename );
				}
			} else {
				switch ( $_GET['action'] ) {
					case 'extra':
					case 'templates':
						$new = isset( $_GET['pdfprnt_tab_action'] ) && 'new' == $_GET['pdfprnt_tab_action'] ? '_new' : '';
						pdfprnt_pro_block( "pdfprnt_{$_GET['action']}{$new}_block", false );
						break;
					case 'custom_code':
						bws_custom_code_tab();
						break;
					case 'go_pro':
						$show = bws_hide_premium_options_check( $pdfprnt_options ) ? true : false;
						bws_go_pro_tab_show(
							$show,
							$pdfprnt_plugin_info,
							$plugin_basename,
							'pdf-print.php',
							'pdf-print-pro.php',
							'pdf-print-pro/pdf-print-pro.php',
							'pdf-print',
							'd9da7c9c2046bed8dfa38d005d4bffdb',
							'101',
							isset( $go_pro_result['pro_plugin_is_activated'] )
						);
						break;
					default:
						break;
				}
			}
			bws_plugin_reviews_block( $pdfprnt_plugin_info['Name'], 'pdf-print' ); ?>
		</div>
	<?php }
}

/* PDF&Print shortcode */
/* [bws_pdfprint] */
if ( ! function_exists( 'pdfprnt_shortcode' ) ) {
	function pdfprnt_shortcode( $attr ) {

		if ( isset( $_REQUEST['print'] ) )
			return;

		global $pdfprnt_options;
		$buttons = '';
		if ( is_home() ) {
			global $post;
			$permalink = get_permalink( $post->ID );
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
		if ( ! empty( $buttons ) )
			$buttons = sprintf(
				'<div class="pdfprnt-buttons">%s</div>',
				$buttons
			);
		return $buttons;
	}
}

/* add shortcode content  */
if ( ! function_exists( 'pdfprnt_shortcode_button_content' ) ) {
	function pdfprnt_shortcode_button_content( $content ) {
		global $wp_version; ?>
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
		<script type="text/javascript">
			function pdfprnt_shortcode_init() {
				( function( $ ) {
					var current_object = '<?php echo ( $wp_version < 3.9 ) ? "#TB_ajaxContent" : ".mce-reset"; ?>';
					$( current_object + ' input[name^="pdfprnt_selected"]' ).change( function() {
						var result = '';
						$( current_object + ' input[name^="pdfprnt_selected"]' ).each( function() {
							if ( $( this ).is( ':checked' ) ) {
								result += $( this ).val() + ',';
							}
						} );

						if ( '' == result ) {
							$( current_object + ' #bws_shortcode_display' ).text( '' );
						} else {
							result = result.slice( 0, - 1 );
							$( current_object + ' #bws_shortcode_display' ).text( '[bws_pdfprint display="' + result + '"]' );
						}
					} );
				} ) ( jQuery );
			}
		</script>
	<?php }
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
		if ( empty( $url ) )
			$url = home_url( '/' );
		$button = ( 'print' == $button ) ? 'print' : 'pdf';

		if ( ! empty( $pdfprnt_options[ $button . '_button_image' ] ) && 'none' != $pdfprnt_options[ $button . '_button_image' ] ) {
			$image = ( 'pdf' == $button ) ?
				'<img src="' . plugins_url( 'images/pdf.png', __FILE__ ) . '" alt="image_pdf" title="' . __( 'View PDF', 'pdf-print' ) . '" />'
			:
				'<img src="' . plugins_url( 'images/print.gif', __FILE__ ) . '" alt="image_print" title="' . __( 'Print Content', 'pdf-print' ) . '" />';
		} else {
			$image = '';
		}

		$custom_query_arg = ( ! empty( $custom_query_arg ) ) ? $custom_query_arg : $button;
		$url = add_query_arg( 'print' , $custom_query_arg , $url );

		$title = ( ! empty( $pdfprnt_options[ $button . '_button_title'] ) ) ?
			sprintf(
				'<span class="pdfprnt-button-title pdfprnt-button-%s-title">%s</span>',
				$button,
				$pdfprnt_options[ $button . '_button_title' ]
			)
		:
			'';
		$link = sprintf(
			'<a href="%s" class="pdfprnt-button pdfprnt-button-%s" target="_blank">%s%s</a>',
			$url,
			$button,
			$image,
			$title
		);
		return $link;
	}
}

/* Positioning buttons in the page */
if ( ! function_exists( 'pdfprnt_content' ) ) {
	function pdfprnt_content( $content ) {
		global $pdfprnt_options, $post;

		if ( is_admin() || is_feed() || is_search() || is_archive() || is_category() || is_tax() || is_tag() || is_author() || ! in_array( $post->post_type, $pdfprnt_options['use_types_posts'] ) )
			return $content;
		if ( 1 == $pdfprnt_options['show_btn_pdf'] || 1 == $pdfprnt_options['show_btn_print'] ) {
			$str = '<div class="pdfprnt-' . $pdfprnt_options['position'] . '">';
			if ( is_home() ) {
				$permalink = get_permalink( $post->ID );
			} else {
				$permalink = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			}

			if ( 1 == $pdfprnt_options['show_btn_pdf'] ) {
				$str .= pdfprnt_get_button( 'pdf', $permalink );
			}
			if ( 1 == $pdfprnt_options['show_btn_print'] ) {
				$str .= pdfprnt_get_button( 'print', $permalink );
			}
			$str .= '</div>';

			if ( 'top-left' == $pdfprnt_options['position'] || 'top-right' == $pdfprnt_options['position'] )
				$content = $str . $content;
			elseif ( 'bottom-left' == $pdfprnt_options['position'] || 'bottom-right' == $pdfprnt_options['position'] )
				$content = $content . $str;
			else
				$content = $str . $content . $str;
		}
		return $content;
	}
}

/* Output buttons for search or archive pages */
if ( ! function_exists( 'pdfprnt_show_buttons_search_archive' ) ) {
	function pdfprnt_show_buttons_search_archive( $content ) {
		global $wp_query;
		/* make sure that we display pdf/print buttons only with main loop */
		if ( is_main_query() && $content === $wp_query ) {
			global $pdfprnt_options, $wp, $posts, $pdfprnt_show_archive_start, $pdfprnt_show_archive_end;
			$loop_position = current_filter();
			if ( ! ( 1 == $pdfprnt_options['show_btn_pdf_search_archive'] || 1 == $pdfprnt_options['show_btn_print_search_archive'] ) )
				return;
			/* Check for existence the type of posts. */
			$is_return = true;
			foreach ( $posts as $post ) {
				if ( in_array( $post->post_type, $pdfprnt_options['use_types_posts'] ) ) {
					$is_return = false;
					break;
				}
			}
			if ( $is_return )
				return;

			if ( ( 'loop_start' == $loop_position && $pdfprnt_show_archive_start == 1 ) || ( 'loop_end' == $loop_position && $pdfprnt_show_archive_end == 1 ) )
				return;

			global $pdfprnt_is_search_archive;
			$current_url = set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$pdfprnt_is_search_archive = true;
			$str = '<div class="pdfprnt-' . $pdfprnt_options['position_search_archive'] . '">';
			if ( 1 == $pdfprnt_options['show_btn_pdf_search_archive'] ) {
				$str .= pdfprnt_get_button( 'pdf', $current_url, 'pdf-search' );
			}

			if ( 1 == $pdfprnt_options['show_btn_print_search_archive'] ) {
				$str .= pdfprnt_get_button( 'print', $current_url, 'print-search' );
			}
			$str .= '</div>';
			echo $str;
			if ( 'loop_start' == $loop_position )
				$pdfprnt_show_archive_start++;
			elseif ( 'loop_end' == $loop_position )
				$pdfprnt_show_archive_end++;
		}
	}
}

/**
 * Add buttons before or after the loop
 */
if ( ! function_exists( 'pdfprnt_auto_show_buttons_search_archive' ) ) {
	function pdfprnt_auto_show_buttons_search_archive() {
		if ( is_search() || is_archive() || is_category() || is_tax() || is_tag() || is_author() ) {
			global $pdfprnt_options, $pdfprnt_show_archive_start, $pdfprnt_show_archive_end;
			$pdfprnt_show_archive_start = $pdfprnt_show_archive_end = 0;
			if ( in_array( $pdfprnt_options['position_search_archive'], array( 'top-left', 'top-right' ) ) ) {
				add_action( 'loop_start', 'pdfprnt_show_buttons_search_archive' );
			} elseif ( in_array( $pdfprnt_options['position_search_archive'], array( 'bottom-left', 'bottom-right' ) ) ) {
				add_action( 'loop_end', 'pdfprnt_show_buttons_search_archive' );
			} else {
				add_action( 'loop_start', 'pdfprnt_show_buttons_search_archive' );
				add_action( 'loop_end', 'pdfprnt_show_buttons_search_archive' );
			}
		}
	}
}

/**
 * Output buttons of page for BWS Portfolio plugin
 * @deprecated since 1.8.4
 */
if( ! function_exists( 'pdfprnt_show_buttons_for_bws_portfolio' ) ) {
	function pdfprnt_show_buttons_for_bws_portfolio() {
		return pdfprnt_show_buttons_for_custom_post_type();
	}
}

/**
 * Output buttons of post for BWS Portfolio plugin
 * @deprecated since 1.8.4
 */
if ( ! function_exists( 'pdfprnt_show_buttons_for_bws_portfolio_post' ) ) {
	function pdfprnt_show_buttons_for_bws_portfolio_post() {
		return pdfprnt_show_buttons_for_custom_post_type();
	}
}


/**
 * Display plugin buttons via action call
 * @param     string       $where         where to display
 * @param     mixed        $user_query    WP_Query parameters
 */
if ( ! function_exists( 'pdfprnt_display_plugin_buttons' ) ) {
	function pdfprnt_display_plugin_buttons( $where = 'top', $user_query = '' ) {
		global $pdfprnt_options;
		if ( preg_match( "|" . $where . "|", $pdfprnt_options['position'] ) || empty( $where ) )
			echo pdfprnt_show_buttons_for_custom_post_type( $user_query );
	}
}

/* Output buttons of page for custom post type */
if ( ! function_exists( 'pdfprnt_show_buttons_for_custom_post_type' ) ) {
	function pdfprnt_show_buttons_for_custom_post_type( $user_query = '' ) {
		global $pdfprnt_options, $post, $wp, $posts;
		if (
			/* not display anything if displaying buttons for post and pages is disabled */
			( ! ( 1 == $pdfprnt_options['show_btn_pdf'] || 1 == $pdfprnt_options['show_btn_print'] ) ) ||
			/* not display anything if we have wrong $user_query */
			( ! ( is_array( $user_query ) || is_string( $user_query ) ) )
		)
			return;

		$current_url = set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$is_return   = true;

		if ( empty( $user_query ) ) { /* set necessary values of parameters for pdf/print buttons */
			$nothing_else = false;
			if ( is_search() || is_archive() || is_category() || is_tax() || is_tag() || is_author() ) { /* search, cattegories, archives */
				foreach ( $posts as $value ) {
					if ( in_array( $value->post_type, $pdfprnt_options['use_types_posts'] ) ) {
						$is_return = false;
						break;
					}
				}
				if ( $is_return )
					return;
				$pdf_query_parameter   = 'pdf-search';
				$print_query_parameter = 'print-search';
			} elseif ( is_page() ) { /* pages */
				if ( in_array( 'page', $pdfprnt_options['use_types_posts'] ) ) {
					$nothing_else = true;
				} else {
					return;
				}
			} elseif ( is_single() ) { /* posts */
				$post_type = get_post_type( $post->ID );
				if ( in_array( $post_type, $pdfprnt_options['use_types_posts'] ) ) {
					$nothing_else = true;
				} else {
					return;
				}
			} else {
				$nothing_else = true;
			}
			if ( $nothing_else ) {
				$pdf_query_parameter   = 'pdf';
				$print_query_parameter = 'print';
			}
		} else {
			$custom_query = new WP_Query( $user_query );
			$current_url  = add_query_arg( $custom_query->query, '', $current_url );
			/* Check for existence the type of posts. */
			if ( ! empty( $custom_query->posts) ) {
				foreach ( $custom_query->posts as $post ) {
					if ( in_array( get_post_type( $post ), $pdfprnt_options['use_types_posts'] ) ) {
						$is_return = false;
						break;
					}
				}
			}
			if ( $is_return )
				return;
			global $pdfprnt_is_custom_post_type;
			$pdfprnt_is_custom_post_type = true;
			$pdf_query_parameter   = 'pdf-custom';
			$print_query_parameter = 'print-custom';
		}

		$str = '<div class="pdfprnt-' . $pdfprnt_options['position'] . '">';
		if ( 1 == $pdfprnt_options['show_btn_pdf'] ) {
			$str .= pdfprnt_get_button( 'pdf', $current_url, $pdf_query_parameter );
		}
		if ( 1 == $pdfprnt_options['show_btn_print'] ) {
			$str .= pdfprnt_get_button( 'print', $current_url, $print_query_parameter );
		}
		$str .= '</div>';
		return $str;
	}
}

/* Add links */
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

/* Add links */
if ( ! function_exists( 'pdfprnt_links' ) ) {
	function pdfprnt_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() && is_plugin_inactive( 'pdf-print-pro/pdf-print-pro.php' ) )
				$links[]	=	'<a href="admin.php?page=pdf-print.php">' . __( 'Settings', 'pdf-print' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com/hc/en-us/sections/200538669" target="_blank">' . __( 'FAQ', 'pdf-print' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'pdf-print' ) . '</a>';
		}
		return $links;
	}
}

/* Add stylesheets */
if ( ! function_exists ( 'pdfprnt_admin_head' ) ) {
	function pdfprnt_admin_head() {
		$is_plugin_options = isset( $_GET['page'] ) && $_GET['page'] == "pdf-print.php";

		if ( ! is_admin() || $is_plugin_options )
			wp_enqueue_style( 'pdfprnt_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );

		if ( $is_plugin_options ) {
			bws_plugins_include_codemirror();
			wp_enqueue_script( 'pdfprnt_script', plugins_url( 'js/script.js', __FILE__ ) );
			wp_localize_script( 'pdfprnt_script', 'pdfprnt_var', array(
					'loading_fonts' => __( 'Loading of fonts. It might take a several minutes', 'pdf-print' ),
					'ajax_nonce'    => wp_create_nonce( 'pdfprnt_ajax_nonce' ),
					'need_reload'   => '.&nbsp;' . sprintf(
						__( 'You may need to reload fonts. For more info see %s', 'pdf-print' ),
						'<a href="http://bestwebsoft.com/products/wordpress/plugins/pdf-print/faq" target="_blank">' . __( 'FAQ', 'pdf-print' ) . '</a>'
					)
				)
			);
		}
	}
}

/* Remove inline 'font-family' and 'font' styles from content */
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
							if ( preg_match( "/" . $pattern . "/", $style ) )
								$content = preg_replace( "/" . $style . "/", "", $content );
						}

					}
				}
			}
		}
		return $content;
	}
}

/* Generate templates for pdf file or print */
if ( ! function_exists( 'pdfprnt_generate_template' ) ) {
	function pdfprnt_generate_template( $content, $isprint = false ) {
		global $pdfprnt_options, $wp_locale;
		$html =
		'<html>
			<head>';
				if ( 1 == $pdfprnt_options['use_theme_stylesheet'] ) {
					/* remove 'font-family' and 'font' styles from theme css-file if additional fonts not loaded */
					if ( 0 == $pdfprnt_options['additional_fonts'] ) {
						$css = wp_remote_get( get_bloginfo( 'stylesheet_url' ) );
						if ( ! empty( $css['body'] ) )
							$html .= '<style type="text/css">' . preg_replace( "/(font:(.*?);)|(font-family(.*?);)/", "", $css['body'] ) . '</style>';
					} else {
						$html .= '<link type="text/css" rel="stylesheet" href="' . get_bloginfo( 'stylesheet_url' ) . '" media="all" />';
					}
				} else {
					$html .= '<link type="text/css" rel="stylesheet" href="' . plugins_url( 'css/default.css', __FILE__ ) . '" media="all" />';
				}
				$html .= pdfprnt_additional_styles( $isprint );
				if ( 1 == $pdfprnt_options['use_custom_styles'] && ! empty( $pdfprnt_options['custom_styles'] ) )
					$html .= '<style type="text/css">' . $pdfprnt_options['custom_styles'] . '</style>';
				if ( $isprint && 1 == $pdfprnt_options['show_print_window'] )
					$html .= '<script>window.onload = function(){ window.print(); };</script>';
			$html .=
			'</head>
			<body' . ( $isprint ? ' class="pdfprnt_print ' . $wp_locale->text_direction . '"' : 'class="' . $wp_locale->text_direction . '"' ) . '>';
				/* Remove inline 'font-family' and 'font' styles from content */
				if ( 0 == $pdfprnt_options['additional_fonts'] )
					$content = pdfprnt_preg_replace( array( "font-family", "font:" ), $content );
				$html .= $content .
			'</body>
		</html>';
		return $html;
	}
}

if ( ! function_exists( 'pdfprnt_additional_styles' ) ) {
	function pdfprnt_additional_styles( $isprint ) {
		$styles = apply_filters( 'bwsplgns_add_pdf_print_styles', array() );
		$html = '';
		if ( ! empty( $styles ) && is_array( $styles ) ) {
			$url  = get_bloginfo( 'url' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			$path = get_home_path();
			foreach ( $styles as $style ) {
				if ( ! empty( $style[0] ) && file_exists( $path . $style[0] ) ) {
					/* if "get print" */
					if ( $isprint ) {
						if( ( isset( $style[1] ) && 'print' ==  $style[1] ) || ! isset( $style[1] ) )
							$html .= '<link type="text/css" rel="stylesheet" href="' . $url . '/' . $style[0] . '" media="all" />';
					/* if "get pdf" */
					} else {
						if ( ( isset( $style[1] ) && 'pdf' ==  $style[1] ) || ! isset( $style[1] ) )
							$html .= '<link type="text/css" rel="stylesheet" href="' . $url . '/' . $style[0] . '" media="all" />';
					}
				}
			}
		}
		return $html;
	}
}

/* Output print page or pdf document and include plugin script */
if ( ! function_exists( 'pdfprnt_print' ) ) {
	function pdfprnt_print( $query ) {
		global $pdfprnt_options, $posts, $post;

		if ( ! $print = get_query_var( 'print' ) )
			return;

		remove_all_filters( 'the_content' );
		add_filter( 'the_content', 'capital_P_dangit', 11 );
		add_filter( 'the_content', 'wptexturize' );
		add_filter( 'the_content', 'convert_smilies' );
		add_filter( 'the_content', 'convert_chars' );
		add_filter( 'the_content', 'wpautop' );
		do_action( 'bwsplgns_pdf_print_the_content' );
		if ( ! 0 == $pdfprnt_options['tmpl_shorcode'] ) {
			add_filter( 'the_content', 'do_shortcode' );
			$pattern = false;
		} else {
			$pattern = get_shortcode_regex();
		}

		$doc_type = explode( '-', $print );

		if ( ! is_array( $doc_type ) )
			return;

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
			if ( ! is_home() )
				$posts = array( $post );
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
				$titles       = $authors = array();
				$default_font = 0 == $pdfprnt_options['additional_fonts'] ? 'dejavusansmono' : '';
				$last = count( $posts );
				$i    = 1;
				$html = '<div id="content">';
				foreach ( $posts as $p ) {

					if ( ! in_array( get_post_type( $p ), $pdfprnt_options['use_types_posts'] ) )
						continue;
					$title_filter = apply_filters( 'bwsplgns_get_pdf_print_title', apply_filters( 'the_title', $p->post_title, $p->ID ), $p );
					$titles[]  = $title_filter;
					$user_info = get_userdata( $p->post_author );
					$authors[] = $user_info->display_name;
					$title     = 1 == $pdfprnt_options['show_title'] ? '<div class="entry-header"><h1 class="entry-title"><a href="' . get_permalink( $p->ID ) . '">' . $title_filter . '</a></h1></div>' : '';
					$image     = 1 == $pdfprnt_options['show_featured_image'] && has_post_thumbnail( $p->ID ) ? '<div class="entry-thumbnail">' . get_the_post_thumbnail( $p->ID ) . '</div>' : '';
					/* replacing shortcodes to an empty string which were added to the content */
					if ( $pattern && preg_match_all( '/'. $pattern .'/s', $p->post_content, $matches ) ) {
						foreach ( array_unique( $matches[0] ) as $value )
							$p->post_content = str_replace( $value, "", $p->post_content );
					}

					$post_content = apply_filters( 'the_content', $p->post_content );
					if ( defined( 'WPB_VC_VERSION' ) ) {
						$post_content = preg_replace( "/\[((\/){1}|())(vc_|az_)(.*?)\]/", '', $post_content );
					}
					$post_content = apply_filters( 'bwsplgns_get_pdf_print_content', $post_content, $p );

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
				$html   .= '</div>';
				$titles  = array_unique( $titles );
				$authors = array_unique( $authors );

				/* generate PDF-document */
				include ( 'mpdf/mpdf.php' );
				$mpdf = new mPDF( '+aCJK', 'A4', 0, $default_font, $pdfprnt_options['pdf_margins']['left'], $pdfprnt_options['pdf_margins']['right'], $pdfprnt_options['pdf_margins']['top'], $pdfprnt_options['pdf_margins']['bottom'] );
				$mpdf->allow_charset_conversion = true;
				$mpdf->charset_in               = get_bloginfo( 'charset' );
				if ( 0 != $pdfprnt_options['additional_fonts'] ) {
					$mpdf->autoScriptToLang = true;
					$mpdf->autoLangToFont   = true;
					$mpdf->baseScript       = 1;
					$mpdf->autoVietnamese   = true;
					$mpdf->autoArabic       = true;
				}
				if ( is_rtl() )
					$mpdf->SetDirectionality( 'rtl' );
				$mpdf->SetTitle( implode( ',', $titles ) );
				$mpdf->SetAuthor( implode( ',', $authors ) );
				$mpdf->SetSubject( get_bloginfo( 'blogdescription' ) );
				$mpdf->WriteHTML( pdfprnt_generate_template( $html ) );
				$mpdf->Output();
				break;
			case 'print':

				$last = count( $posts );
				$i    = 1;
				$html = '<div id="content">';
				foreach ( $posts as $p ) {

					if ( ! in_array( get_post_type( $p ), $pdfprnt_options['use_types_posts'] ) )
						continue;

					$title = 1 == $pdfprnt_options['show_title'] ? '<div class="entry-header"><h1 class="entry-title">' . apply_filters( 'bwsplgns_get_pdf_print_title', apply_filters( 'the_title', $p->post_title, $p->ID ), $p ) . '</h1></div>' : '';
					$image = 1 == $pdfprnt_options['show_featured_image'] && has_post_thumbnail( $p->ID ) ? '<div class="entry-thumbnail">' . get_the_post_thumbnail( $p->ID ) . '</div>' : '';
					/* replacing shortcodes to an empty string which were added to the content */
					if ( $pattern && preg_match_all( '/'. $pattern .'/s', $p->post_content, $matches ) ) {
						foreach ( array_unique( $matches[0] ) as $value )
							$p->post_content = str_replace( $value, "", $p->post_content );
					}

					$post_content = apply_filters( 'the_content', $p->post_content );
					if ( defined( 'WPB_VC_VERSION' ) ) {
						$post_content = preg_replace( "/\[((\/){1}|())(vc_|az_)(.*?)\]/", '', $post_content );
					}
					$post_content = apply_filters( 'bwsplgns_get_pdf_print_content', $post_content, $p );

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

			if ( substr( $destination, mb_strlen( DIRECTORY_SEPARATOR, $charset ) * -1 ) != DIRECTORY_SEPARATOR )
				$destination .= DIRECTORY_SEPARATOR;

			if ( substr( $subdir, -1 ) != "/" )
				$subdir .= "/";
			// Extract files
			for ( $i = 0; $i < $this->numFiles; $i++ ) {
				$filename = $this->getNameIndex( $i );

				if ( substr( $filename, 0, mb_strlen( $subdir, $charset ) ) == $subdir ) {
					$relativePath = substr( $filename, mb_strlen( $subdir, $charset ) );
					$relativePath = str_replace( array( "/", "\\" ), DIRECTORY_SEPARATOR, $relativePath );

					if ( mb_strlen( $relativePath, $charset ) > 0 ) {
						if ( substr( $filename, -1 ) == "/" ) {
							if ( ! is_dir( $destination . $relativePath ) )
								if ( ! @mkdir( $destination . $relativePath, 0755, true ) )
									$errors[$i] = $filename;
						} else {
							if ( dirname( $relativePath) != "." ) {
								if ( ! is_dir( $destination . dirname( $relativePath ) ) ) {
									// New dir (for file)
									@mkdir( $destination . dirname( $relativePath ), 0755, true );
								}
							}
							// New file
							if ( @file_put_contents( $destination . $relativePath, $this->getFromIndex( $i ) ) === false )
								$errors[$i] = $filename;
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
	function pdfprnt_download_zip( $zip_file, $upload_dir ) {
		/* check permissions */
		if ( is_writable( $upload_dir ) ) {
			/* load ZIP-archive */
			$result = array();
			$fp     = fopen( $zip_file, 'w+' );
			$curl   = curl_init();
			$curl_parametres = array(
				CURLOPT_URL       => 'http://www.mpdfonline.com/repos/MPDF_6_0.zip',
				CURLOPT_FILE      => $fp,
				CURLOPT_USERAGENT => ( isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0" )
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
 * Copy neccesary fonts to mpdf/ttfonts
 * @param     string   $zip_file     path to zip archive
 */
if ( ! function_exists( 'pdfprnt_copy_fonts' ) ) {
	function pdfprnt_copy_fonts( $zip_file, $upload_dir ) {
		global $pdfprnt_options;
		if ( empty( $pdfprnt_options ) )
			$pdfprnt_options = get_option( 'pdfprnt_options' );
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
				$errors = $zip->extractSubdirTo( $destination, "mpdf60/ttfonts" );
				$zip->close();
				if ( empty( $errors ) ) {
					$result['done'] = __( 'Additional fonts were successfully loaded', 'pdf-print' );
					unlink( $zip_file );
				} else {
					$result['error'] = __( 'Some errors occur during loading files', 'pdf-print' );
				}
			} else {
				$result['error'] = __( 'Can not extract files from zip-archive', 'pdf-print' );
				unlink( $zip_file );
			}
		} else {
			$result['error'] = __( 'Cannot create "uploads/pdf-print-fonts" folder. Check your permissions', 'pdf-print' );
		}
		$value = isset( $result['error'] ) ? -1 : count( scandir( $destination ) );
		pdfprnt_update_option( $value, true );
		return $result;
	}
}

/**
 * Download ZIP-archive with MPDF library,
 * copy fonts to folder 'pdf-print-fons'
 */
if ( ! function_exists( 'pdfprnt_load_and_copy' ) ) {
	function pdfprnt_load_and_copy( $zip_file, $upload_dir ) {
		$result = file_exists( $zip_file ) ? array( 'done' => 'ok' ) : pdfprnt_download_zip( $zip_file, $upload_dir );
		if ( isset( $result['done'] ) )
			$result = pdfprnt_copy_fonts( $zip_file, $upload_dir );
		return $result;
	}
}
/**
 * Function to load fonts for MPDF library
 */
if ( ! function_exists( 'pdfprnt_load_fonts' ) ) {
	function pdfprnt_load_fonts() {
		global $pdfprnt_options;
		if ( empty( $pdfprnt_options ) )
			$pdfprnt_options = get_option( 'pdfprnt_options' );
		$ajax_request = isset( $_REQUEST['action'] ) && 'pdfprnt_load_fonts' == $_REQUEST['action'] ? true : false;
		$php_request  = isset( $_REQUEST['pdfprnt_action'] ) && 'pdfprnt_load_fonts' == $_REQUEST['pdfprnt_action'] ? true : false;
		$verified     = isset( $_REQUEST['pdfprnt_ajax_nonce'] ) && wp_verify_nonce( $_REQUEST['pdfprnt_ajax_nonce'], 'pdfprnt_ajax_nonce' ) ? true : false;
		if ( ( $ajax_request || $php_request ) && $verified ) {
			$result = array();
			$flag   = false;
			/* get path to directory for ZIP-archive uploading */
			if ( is_multisite() ) {
				switch_to_blog( 1 );
				$upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$upload_dir = wp_upload_dir();
			}
			$zip_file    = $upload_dir['basedir'] . '/MPDF_6_0.zip';
			$destination = $upload_dir['basedir'] .'/pdf-print-fonts';
			if ( file_exists( $destination ) ) { /* if folder with fonts already exists */
				if ( is_multisite() ) {
					$network_options = get_site_option( 'pdfprnt_options' ); /* get network options */
					if ( $network_options['additional_fonts'] == count( scandir( $destination ) ) ) { /* if all fonts was loaded successfully */
						$result['done'] = __( 'Additional fonts was successfully loaded', 'pdf-print' );
						pdfprnt_update_option( $network_options['additional_fonts'] );
					} else { /* if something wrong */
						$result = pdfprnt_load_and_copy( $zip_file, $upload_dir['basedir'] ); /* load fonts */
					}
				} else {
					$result = pdfprnt_load_and_copy( $zip_file, $upload_dir['basedir'] ); /* load fonts */
				}
			} else {
				mkdir( $destination, 0755, true );
				$result = pdfprnt_load_and_copy( $zip_file, $upload_dir['basedir'] );
			}
			if ( $ajax_request )
				echo json_encode( $result );
			else
				return $result;
		}
		if ( $ajax_request )
			die();
	}
}

if ( ! function_exists( 'pdfprnt_update_option' ) ) {
	function pdfprnt_update_option( $value, $update_network = false ) {
		global $pdfprnt_options;
		if ( empty( $pdfprnt_options ) )
			$pdfprnt_options = get_option( 'pdfprnt_options' );
		$pdfprnt_options['additional_fonts'] = $value;
		update_option( 'pdfprnt_options', $pdfprnt_options );
		if ( $update_network ) {
			$network_options = get_site_option( 'pdfprnt_options' );
			$network_options['additional_fonts'] = $value;
			update_site_option( 'pdfprnt_options', $network_options );
		}
	}
}

if ( ! function_exists ( 'pdfprnt_plugin_banner' ) ) {
	function pdfprnt_plugin_banner() {
		global $hook_suffix, $pdfprnt_plugin_info, $pdfprnt_options;
		if ( empty( $pdfprnt_options ) )
			$pdfprnt_options = get_option( 'pdfprnt_options' );
		if ( 'plugins.php' == $hook_suffix ) {
			if ( isset( $pdfprnt_options['first_install'] ) && strtotime( '-1 week' ) > $pdfprnt_options['first_install'] )
				bws_plugin_banner( $pdfprnt_plugin_info, 'pdfprnt', 'pdf-print', 'e2f2549f4d70bc4cb9b48071169d264e', '101', '//ps.w.org/pdf-print/assets/icon-128x128.png' );
			bws_plugin_banner_to_settings( $pdfprnt_plugin_info, 'pdfprnt_options', 'pdf-print', 'admin.php?page=pdf-print.php' );
		}

		if ( isset( $_REQUEST['page'] ) && 'pdf-print.php' == $_REQUEST['page'] ) {
			bws_plugin_suggest_feature_banner( $pdfprnt_plugin_info, 'pdfprnt_options', 'pdf-print' );
		}
	}
}

/* Buttons Widget */
if ( ! class_exists( 'Pdfprnt_Buttons_Widget' ) ) {
	class Pdfprnt_Buttons_Widget extends WP_Widget {
		function __construct() {
			parent::__construct( 'pdfprnt_buttons', __( 'PDF & Print Buttons', 'pdf-print' ), array( 'description' => __( 'Show PDF & Print Buttons on your site', 'pdf-print' ) ) );
		}

		function widget( $args, $instance ) {
			global $pdfprnt_options, $pdfprnt_is_search_archive, $pdfprnt_is_custom_post_type;
			$url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$buttons = '';

			$instance['pdf_button_show']		= ! empty( $instance['pdf_button_show'] ) ? 1 : 0;
			$instance['print_button_show']		= ! empty( $instance['print_button_show'] ) ? 1 : 0;
			$instance['pdf_button_title']		= strip_tags( $pdfprnt_options['pdf_button_title'] );
			$instance['print_button_title']		= strip_tags( $pdfprnt_options['print_button_title'] );
			$instance['pdf_button_image']		= ( isset( $pdfprnt_options['pdf_button_image'] ) && 'none' == $pdfprnt_options['pdf_button_image'] ) ? 'none' : 'default';
			$instance['print_button_image']		= ( isset( $pdfprnt_options['print_button_image'] ) && 'none' == $pdfprnt_options['print_button_image'] ) ? 'none' : 'default';
			if ( ! empty( $pdfprnt_is_search_archive ) )
				$custom_query = '-search';
			elseif ( ! empty( $pdfprnt_is_custom_post_type ) )
				$custom_query = '-custom';
			else
				$custom_query = '';
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
				$button_image = 'none' != $instance['print_button_image'] ? '<img src="' . plugins_url( 'images/print.gif', __FILE__ ) . '" alt="image_print" title="' . __( 'Print Content', 'pdf-print' ) . '" />' : '';
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
				if ( ! empty( $title ) )
					echo $args['before_title'] . $title . $args['after_title'];
				echo $buttons;
				echo $args['after_widget'];
			}
		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title']					= strip_tags( $new_instance['title'] );
			$instance['pdf_button_show']		= ! empty( $new_instance['pdf_button_show'] ) ? 1 : 0;
			$instance['print_button_show']		= ! empty( $new_instance['print_button_show'] ) ? 1 : 0;
			return $instance;
		}

		function form( $instance ) {
			global $pdfprnt_options;
			if ( empty( $pdfprnt_options ) ) {
				$pdfprnt_options = get_option( 'pdfprnt_options' );
			}

			if ( ! empty( $instance ) ) {
				$title					= ! empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
				$pdf_button_show		= ! empty( $instance['pdf_button_show'] ) ? 1 : 0;
				$print_button_show		= ! empty( $instance['print_button_show'] ) ? 1 : 0;
			} else {
				$title					= '';
				$pdf_button_show 		= 1;
				$print_button_show 		= 1;
			} ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'pdf-print' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<input class="widefat pdfprnt-show-button-cb" id="<?php echo $this->get_field_id( 'pdf_button_show' ); ?>" name="<?php echo $this->get_field_name( 'pdf_button_show' ); ?>" type="checkbox" value="1" <?php if ( 1 == $pdf_button_show ) { echo 'checked="checked"'; } ?> />
				<label for="<?php echo $this->get_field_id( 'pdf_button_show' ); ?>"><?php _e( 'Display PDF Button', 'pdf-print' ); ?></label>
			</p>
			<p>
				<input class="widefat pdfprnt-show-button-cb" id="<?php echo $this->get_field_id( 'print_button_show' ); ?>" name="<?php echo $this->get_field_name( 'print_button_show' ); ?>" type="checkbox" <?php if ( 1 == $print_button_show ) echo 'checked="checked"'; ?> value="1" />
				<label for="<?php echo $this->get_field_id( 'print_button_show' ); ?>"><?php _e( 'Display Print Button', 'pdf-print' ); ?></label>
			</p>
		<?php }
	}
}

if ( ! function_exists( 'pdfprnt_register_buttons_widget' ) ) {
	function pdfprnt_register_buttons_widget() {
		register_widget( 'Pdfprnt_Buttons_Widget' );
	}
}

/* Deleting plugin options on uninstalling */
if ( ! function_exists( 'pdfprnt_uninstall' ) ) {
	function pdfprnt_uninstall() {
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$all_plugins = get_plugins();

		if ( ! array_key_exists( 'pdf-print-pro/pdf-print-pro.php', $all_plugins ) ) {
			global $wpdb;
			if ( is_multisite() ) {
				/* Get all blog ids */
				$blogids  = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
				$tables   = '';
				$old_blog = $wpdb->blogid;
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					delete_option( 'pdfprnt_options' );
				}
				switch_to_blog( $old_blog );
				delete_site_option( 'pdfprnt_options' );
			} else {
				delete_option( 'pdfprnt_options' );
			}
		}

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

/* Adding function to output PDF document or pirnt page */
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
add_action( 'admin_menu', 'pdfprnt_admin_menu' );
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
/* Adding banner */
add_action( 'admin_notices', 'pdfprnt_plugin_banner' );

/* Register widget */
add_action( 'widgets_init', 'pdfprnt_register_buttons_widget' );

add_action( 'bwsplgns_display_pdf_print_buttons', 'pdfprnt_display_plugin_buttons', 10, 2 );