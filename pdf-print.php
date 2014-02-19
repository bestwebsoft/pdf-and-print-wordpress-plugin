<?php
/*
Plugin Name: PDF & Print
Plugin URI:  http://bestwebsoft.com/plugin/
Description: Plugin adds PDF creation and Print button on your site.
Author: BestWebSoft
Version: 1.6
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*  © Copyright 2014  BestWebSoft  ( http://support.bestwebsoft.com )

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

require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );

/* Add our own menu */
if ( ! function_exists( 'pdfprnt_add_pages' ) ) {
	function pdfprnt_add_pages() {
		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', WP_CONTENT_URL . "/plugins/google-plus-one/images/px.png", 1001 );
		add_submenu_page( 'bws_plugins', __( 'PDF & Print Settings', 'pdf-print' ), __( 'PDF & Print', 'pdf-print' ), 'manage_options', "pdf-print.php", 'pdfprnt_settings_page' );
	}
}

/* Register settings for plugin */
if ( ! function_exists( 'pdfprnt_settings' ) ) {
	function pdfprnt_settings() {
		global $pdfprnt_options_array, $pdfprnt_output_count_buttons, $wpmu;
		$pdfprnt_output_count_buttons	=	0;		/* Variable to verify performance number of once function. */
		$pdfprnt_default_post_types		=	array();
		foreach ( get_post_types( array( 'public' => 1, 'show_ui' => 1,	'_builtin' => true ), 'names' ) as $value )
			$pdfprnt_default_post_types[]	=	$value;		/* Default post types of WordPress. */
		$pdfprnt_options_array_defaults		=	array(
			'position'						=>	'top-right',
			'position_search_archive'		=>	'left',
			'position_custom'				=>	'left',
			'show_btn_print'				=>	1,
			'show_btn_pdf'					=>	1,
			'show_btn_print_search_archive'	=>	1,
			'show_btn_pdf_search_archive'	=>	1,
			'show_btn_print_custom'			=>	1,
			'show_btn_pdf_custom'			=>	1,
			'use_theme_stylesheet'			=>	0,
			'tmpl_post'						=>	1,
			'tmpl_search'					=>	1,
			'tmpl_custom'					=>	1,
			'tmpl_shorcode'					=>	1,
			'use_types_posts'				=>	$pdfprnt_default_post_types
		);
		if ( 1 == $wpmu ) {
			if ( ! get_site_option( 'pdfprnt_options_array' ) ) {
				add_site_option( 'pdfprnt_options_array', $cptch_option_defaults, '', 'yes' );
			}
		} else {
			if ( ! get_option( 'pdfprnt_options_array' ) )
				add_option( 'pdfprnt_options_array', $pdfprnt_options_array_defaults, '', 'yes' );
		}
		if ( 1 == $wpmu )
			$pdfprnt_options_array	= get_site_option( 'pdfprnt_options_array' );
		else 
			$pdfprnt_options_array	= get_option( 'pdfprnt_options_array' );
		$pdfprnt_options_array	= array_merge( $pdfprnt_options_array_defaults, $pdfprnt_options_array );
	}
}

/* Function check if plugin is compatible with current WP version  */
if ( ! function_exists ( 'pdfprnt_version_check' ) ) {
	function pdfprnt_version_check() {
		global $wp_version, $bws_plugin_info;

		if (  function_exists( 'get_plugin_data' ) && ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) ) ) {
			$plugin_info = get_plugin_data( __FILE__ );	
			$bws_plugin_info = array( 'id' => '101', 'version' => $plugin_info["Version"] );
		}

		$plugin_data	=	get_plugin_data( __FILE__, false );
		$require_wp		=	"3.0"; /* Wordpress at least requires version */
		$plugin			=	plugin_basename( __FILE__ );
	 	if ( version_compare( $wp_version, $require_wp, "<" ) ) {
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
				wp_die( "<strong>" . $plugin_data['Name'] . " </strong> " . __( 'requires', 'pdf-print' ) . " <strong>WordPress" . $require_wp . " </strong> " . __( 'or higher, that is why it has been deactivated! Please upgrade WordPress and try again.', 'pdf-print') . "<br /><br />" . __( 'Back to the WordPress', 'pdf-print') . " <a href='" . get_admin_url( null, 'plugins.php' ) . "'>" . __( 'Plugins page', 'pdf-print') . "</a>." );
			}
		}
	}
}

/* Add admin page */
if ( ! function_exists ( 'pdfprnt_settings_page' ) ) {
	function pdfprnt_settings_page () {
		global $pdfprnt_options_array;
		$pdfprnt_positions_values	=	array(
			'top-left'		=>	__( 'Top Left', 'pdf-print' ),
			'top-right'		=>	__( 'Top Right', 'pdf-print' ),
			'bottom-left'	=>	__( 'Bottom Left', 'pdf-print' ),
			'bottom-right'	=>	__( 'Bottom Right', 'pdf-print' )
		);
		if ( isset( $_REQUEST['pdfprnt_position'] ) &&
			isset( $_REQUEST['pdfprnt_position_search_archive'] ) &&
			isset( $_REQUEST['pdfprnt_position_custom'] ) &&
			isset( $_REQUEST['pdfprnt_use_theme_stylesheet'] ) &&
			isset( $_REQUEST['pdfprnt_tmpl_post'] ) &&
			isset( $_REQUEST['pdfprnt_tmpl_custom'] ) &&
			isset( $_REQUEST['pdfprnt_tmpl_search'] ) &&
			check_admin_referer( plugin_basename( __FILE__ ), 'pdfprnt_nonce_name' ) ) {
			$pdfprnt_options_array['position']						=	$_REQUEST['pdfprnt_position'];
			$pdfprnt_options_array['position_search_archive']		=	$_REQUEST['pdfprnt_position_search_archive'];
			$pdfprnt_options_array['position_custom']				=	$_REQUEST['pdfprnt_position_custom'];
			$pdfprnt_options_array['tmpl_post']						=	$_REQUEST['pdfprnt_tmpl_post'];
			$pdfprnt_options_array['tmpl_search']					=	$_REQUEST['pdfprnt_tmpl_search'];
			$pdfprnt_options_array['tmpl_custom']					=	$_REQUEST['pdfprnt_tmpl_custom'];
			$pdfprnt_options_array['use_theme_stylesheet']			=	$_REQUEST['pdfprnt_use_theme_stylesheet'];
			$pdfprnt_options_array['show_btn_pdf']					=	isset( $_REQUEST['pdfprnt_show_btn_pdf'] ) ? 1 : 0;
			$pdfprnt_options_array['show_btn_print']				=	isset( $_REQUEST['pdfprnt_show_btn_print'] ) ? 1 : 0;
			$pdfprnt_options_array['show_btn_pdf_search_archive']	=	isset( $_REQUEST['pdfprnt_show_btn_pdf_search_archive'] ) ? 1 : 0;
			$pdfprnt_options_array['show_btn_print_search_archive']	=	isset( $_REQUEST['pdfprnt_show_btn_print_search_archive'] ) ? 1 : 0;
			$pdfprnt_options_array['show_btn_pdf_custom']			=	isset( $_REQUEST['pdfprnt_show_btn_pdf_custom'] ) ? 1 : 0;
			$pdfprnt_options_array['show_btn_print_custom']			=	isset( $_REQUEST['pdfprnt_show_btn_print_custom'] ) ? 1 : 0;
			$pdfprnt_options_array['tmpl_shorcode']					=	isset( $_REQUEST['pdfprnt_tmpl_shorcode'] ) ? 1 : 0;
			$pdfprnt_options_array['use_types_posts']				=	isset( $_REQUEST['pdfprnt_use_types_posts'] ) ? $_REQUEST['pdfprnt_use_types_posts'] : array();
			update_option ( 'pdfprnt_options_array', $pdfprnt_options_array );
			$message	=	__( 'Settings saved.', 'pdf-print' );
		} ?>
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2><?php echo __( 'PDF & Print Settings', 'pdf-print' ); ?></h2>
			<div class="updated fade" <?php if( empty( $message ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div id="pdfprnt_settings_notice" class="updated fade" style="display:none"><p><strong><?php _e( "Notice:", 'pdf-print' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'pdf-print' ); ?></p></div>
			<div>
				<form method="post" action="admin.php?page=pdf-print.php" id="pdfprnt_settings_form">
					<table class="form-table pdfprnt_settings_table">
						<tr>
							<th scope="row"><?php echo __( 'Use of theme stylesheet or plugin default style:', 'pdf-print' ); ?></th>
							<td>
								<select name="pdfprnt_use_theme_stylesheet">
									<option value="0" <?php if ( 0 == $pdfprnt_options_array['use_theme_stylesheet'] ) echo 'selected="selected"'; ?>><?php echo __( 'Default stylesheet', 'pdf-print' ); ?></option>
									<option value="1" <?php if ( 1 == $pdfprnt_options_array['use_theme_stylesheet'] ) echo 'selected="selected"'; ?>><?php echo __( 'Current theme stylesheet', 'pdf-print' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo __( 'The types of posts that the plugin will use:', 'pdf-print' ); ?></th>
							<td>
								<select name="pdfprnt_use_types_posts[]" multiple="multiple">
									<?php foreach ( get_post_types( array( 'public' => 1, 'show_ui' => 1 ), 'objects' ) as $key => $value  ) : ?>
										<option value="<?php echo $key; ?>" <?php if ( in_array( $key, $pdfprnt_options_array['use_types_posts'] ) ) echo 'selected="selected"'; ?>><?php echo $value->label; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<?php
							$active_plugins = get_option( 'active_plugins' );
							if( 0 < count( preg_grep( '/portfolio\/portfolio.php/', $active_plugins ) ) ) : ?>
								<tr>
									<th scope="row"></th>
									<td>
										<span class="pdfprnt_explanation"><?php echo __( 'If you are using a Portfolio plugin (powered by bestwebsoft.com) and you need the output pdf and print buttons next to each post, you just need to insert the strings in loop into the template source code in files portfolio.php and portfolio-post.php', 'pdf-print' ) . ' &#60;?php if ( function_exists( \'pdfprnt_show_buttons_for_bws_portfolio_post\' ) ) echo pdfprnt_show_buttons_for_bws_portfolio_post(); ?&#62;<br/> ' . __( ' In order to use PDF and Print buttons for all posts you need to put the strings below into the template source code in file portfolio.php', 'pdf-print' ) . ' &#60;?php if ( function_exists( \'pdfprnt_show_buttons_for_bws_portfolio\' ) ) echo pdfprnt_show_buttons_for_bws_portfolio(); ?&#62;'; ?></span>
									</td>
								</tr>
						<?php endif; ?>
						<tr>
							<th scope="row">
								<?php echo __( 'Position of buttons in content:', 'pdf-print' ); ?>
							</th>
							<td>
								<select name="pdfprnt_position">
									<?php foreach ( $pdfprnt_positions_values as $key => $value ) : ?>
										<option value="<?php echo $key; ?>" <?php if ( $pdfprnt_options_array['position'] == $key ) echo 'selected="selected"'; ?>><?php echo $value; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo __( 'Show:', 'pdf-print' ); ?></th>
							<td>
								<label><input type="checkbox" name="pdfprnt_show_btn_pdf" <?php if ( 1 == $pdfprnt_options_array['show_btn_pdf'] ) echo 'checked="checked"'; ?> /> <span><?php echo __( 'PDF button', 'pdf-print' ); ?></span></label><br />
								<label><input type="checkbox" name="pdfprnt_show_btn_print" <?php if ( 1 == $pdfprnt_options_array['show_btn_print'] ) echo 'checked="checked"'; ?> /> <span><?php echo __( 'Print button', 'pdf-print' ); ?></span></label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php echo __( 'Default template setting for post:', 'pdf-print' ); ?>
							</th>
							<td>
								<div>
									<label>
										<img src="<?php echo plugins_url( 'images/template_left.jpg', __FILE__ ); ?>" alt="template_left" />
										<input type="radio" name="pdfprnt_tmpl_post" value="1" <?php if ( 1 == $pdfprnt_options_array['tmpl_post'] ) echo 'checked="checked"'; ?> />
									</label>
								</div>
								<div>
									<label>
										<img src="<?php echo plugins_url( 'images/template_center.jpg', __FILE__ ); ?>" alt="template_center" />
										<input type="radio" name="pdfprnt_tmpl_post" value="2" <?php if ( 2 == $pdfprnt_options_array['tmpl_post'] ) echo 'checked="checked"'; ?> />
									</label>
								</div>
								<div>
									<label>
										<img src="<?php echo plugins_url( 'images/template_right.jpg', __FILE__ ); ?>" alt="template_right" />
										<input type="radio" name="pdfprnt_tmpl_post" value="3" <?php if ( 3 == $pdfprnt_options_array['tmpl_post'] ) echo 'checked="checked"'; ?> />
									</label>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php echo __( 'Position of buttons in content for custom post:', 'pdf-print' ); ?>
							</th>
							<td>
								<select name="pdfprnt_position_custom">
									<option value="pdfprnt-left" <?php if ( 'left' == $pdfprnt_options_array['position_custom'] ) echo 'selected="selected"'; ?>><?php echo __( 'Align Left', 'pdf-print' ); ?></option>
									<option value="pdfprnt-right" <?php if ( 'right' == $pdfprnt_options_array['position_custom'] ) echo 'selected="selected"'; ?>><?php echo __( 'Align Right', 'pdf-print' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo __( 'Show for custom posts:', 'pdf-print' ); ?></th>
							<td>
								<label><input type="checkbox" name="pdfprnt_show_btn_pdf_custom" <?php if ( 1 == $pdfprnt_options_array['show_btn_pdf_custom'] ) echo 'checked="checked"'; ?> /> <span><?php echo __( 'PDF button', 'pdf-print' ); ?></span></label><br />
								<label><input type="checkbox" name="pdfprnt_show_btn_print_custom" <?php if ( 1 == $pdfprnt_options_array['show_btn_print_custom'] ) echo 'checked="checked"'; ?> /> <span><?php echo __( 'Print button', 'pdf-print' ); ?></span></label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php echo __( 'Default template setting for custom posts:', 'pdf-print' ); ?>
							</th>
							<td>
								<div>
									<label>
										<img src="<?php echo plugins_url( 'images/template_left.jpg', __FILE__ ); ?>" alt="template_left" />
										<input type="radio" name="pdfprnt_tmpl_custom" value="1" <?php if ( 1 == $pdfprnt_options_array['tmpl_custom'] ) echo 'checked="checked"'; ?> />
									</label>
								</div>
								<div>
									<label>
										<img src="<?php echo plugins_url( 'images/template_center.jpg', __FILE__ ); ?>" alt="template_center" />
										<input type="radio" name="pdfprnt_tmpl_custom" value="2" <?php if ( 2 == $pdfprnt_options_array['tmpl_custom'] ) echo 'checked="checked"'; ?> />
									</label>
								</div>
								<div>
									<label>
										<img src="<?php echo plugins_url( 'images/template_right.jpg', __FILE__ ); ?>" alt="template_right" />
										<input type="radio" name="pdfprnt_tmpl_custom" value="3" <?php if ( 3 == $pdfprnt_options_array['tmpl_custom'] ) echo 'checked="checked"'; ?> />
									</label>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"></th>
							<td>
								<span class="pdfprnt_explanation"><?php echo __( 'In order to use PDF and Print buttons on the custom page you should use the', 'pdf-print' ) . '&#60;?php if ( function_exists( \'pdfprnt_show_buttons_for_custom_post_type\' ) ) echo pdfprnt_show_buttons_for_custom_post_type( $custom_query ); ?&#62;' . __( ' where you have to specify query parameters for your custom post.<br/> For example: ', 'pdf-print' ) . '&#60;?php if ( function_exists( \'pdfprnt_show_buttons_for_custom_post_type\' ) ) echo pdfprnt_show_buttons_for_custom_post_type( \'post_type=gallery&orderby=post_date\' ); ?&#62;' . __(' or ', 'pdf-print') . '&#60;?php if ( function_exists( \'pdfprnt_show_buttons_for_custom_post_type\' ) ) echo pdfprnt_show_buttons_for_custom_post_type( array( \'post_type\' => \'gallery\', \'orderby\' => \'post_date\' ) ); ?&#62;.' . __( ' For more information on the syntax for assigning parameters to function see <a target="_blank" href="http://codex.wordpress.org/Class_Reference/WP_Query#Parameters">here</a>.', 'pdf-print' ); ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php echo __( 'Position of buttons on search or archive page:', 'pdf-print' ); ?>
							</th>
							<td>
								<select name="pdfprnt_position_search_archive">
									<option value="pdfprnt-left" <?php if ( 'left' == $pdfprnt_options_array['position_search_archive'] ) echo 'selected="selected"'; ?>><?php echo __( 'Align Left', 'pdf-print' ); ?></option>
									<option value="pdfprnt-right" <?php if ( 'right' == $pdfprnt_options_array['position_search_archive'] ) echo 'selected="selected"'; ?>><?php echo __( 'Align Right', 'pdf-print' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo __( 'Show for search or archive page:', 'pdf-print' ); ?></th>
							<td>
								<label><input type="checkbox" name="pdfprnt_show_btn_pdf_search_archive" <?php if ( 1 == $pdfprnt_options_array['show_btn_pdf_search_archive'] ) echo 'checked="checked"'; ?> /> <span><?php echo __( 'PDF button', 'pdf-print' ); ?></span></label><br />
								<label><input type="checkbox" name="pdfprnt_show_btn_print_search_archive" <?php if ( 1 == $pdfprnt_options_array['show_btn_print_search_archive'] ) echo 'checked="checked"'; ?> /> <span><?php echo __( 'Print button', 'pdf-print' ); ?></span></label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php echo __( 'Default template setting for search and archive:', 'pdf-print' ); ?>
							</th>
							<td>
								<div>
									<label>
										<img src="<?php echo plugins_url( 'images/template_left.jpg', __FILE__ ); ?>" alt="template_left" />
										<input type="radio" name="pdfprnt_tmpl_search" value="1" <?php if ( 1 == $pdfprnt_options_array['tmpl_search'] ) echo 'checked="checked"'; ?> />
									</label>
								</div>
								<div>
									<label>
										<img src="<?php echo plugins_url( 'images/template_center.jpg', __FILE__ ); ?>" alt="template_center" />
										<input type="radio" name="pdfprnt_tmpl_search" value="2" <?php if ( 2 == $pdfprnt_options_array['tmpl_search'] ) echo 'checked="checked"'; ?> />
									</label>
								</div>
								<div>
									<label>
										<img src="<?php echo plugins_url( 'images/template_right.jpg', __FILE__ ); ?>" alt="template_right" />
										<input type="radio" name="pdfprnt_tmpl_search" value="3" <?php if ( 3 == $pdfprnt_options_array['tmpl_search'] ) echo 'checked="checked"'; ?> />
									</label>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"></th>
							<td>
								<span class="pdfprnt_explanation"><?php echo __( 'In order to use PDF and Print buttons on the search page or archive page you should put the strings below into the template source code in files search.php or archives.php ', 'pdf-print' ) . ' &#60;?php if ( function_exists( \'pdfprnt_show_buttons_search_archive\' ) ) echo pdfprnt_show_buttons_search_archive(); ?&#62;'; ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<?php echo __( 'Settings for shorcodes:', 'pdf-print' ); ?>
							</th>
							<td>
								<label><input type="checkbox" name="pdfprnt_tmpl_shorcode"  <?php if ( 1 == $pdfprnt_options_array['tmpl_shorcode'] ) echo 'checked="checked"'; ?> /> <span><?php echo __( 'Do!', 'pdf-print' ); ?></span></label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'pdf-print' ); ?>" />
							</td>
						</tr>
					</table>
					<?php wp_nonce_field( plugin_basename( __FILE__ ), 'pdfprnt_nonce_name' ); ?>
				</form>
				<br />
				<div class="bws-plugin-reviews">
					<div class="bws-plugin-reviews-rate">
					<?php _e( 'If you enjoy our plugin, please give it 5 stars on WordPress', 'pdf-print' ); ?>: 
					<a href="http://wordpress.org/support/view/plugin-reviews/pdf-print" target="_blank" title="PDF &amp; Print reviews"><?php _e( 'Rate the plugin', 'pdf-print' ); ?></a><br/>
					</div>
					<div class="bws-plugin-reviews-support">
					<?php _e( 'If there is something wrong about it, please contact us', 'pdf-print' ); ?>: 
					<a href="http://support.bestwebsoft.com">http://support.bestwebsoft.com</a>
					</div>
				</div>
			</div>
		</div>
	<?php }
}

/* Positioning buttons in the page */
if( ! function_exists( 'pdfprnt_content' ) ) {
	function pdfprnt_content( $content ) {
		global $pdfprnt_options_array, $post;
		if ( ! in_array( get_post_type( $post ), $pdfprnt_options_array['use_types_posts'] ) ) // Check for existence the type of posts.
			return $content;
		if ( 1 == $pdfprnt_options_array['show_btn_pdf'] || 1 == $pdfprnt_options_array['show_btn_print'] ) {
			$position = ( 'top-left' == $pdfprnt_options_array['position'] || 'top-right' == $pdfprnt_options_array['position'] ) ? true : false;
			$str = '<div class="pdfprnt-' . $pdfprnt_options_array['position'] . '">';
			if ( 1 == $pdfprnt_options_array['show_btn_pdf'] ) {
				if ( ! is_front_page () )
					$permalink = add_query_arg( 'print' , 'pdf' , get_permalink( $post->ID ) );
				else 
					$permalink = add_query_arg( 'print' , 'pdf' , get_permalink() . '?page_id=' . $post->ID );
				$str .= '<a href="' . $permalink . '" target="_blank"><img src="' . plugins_url( 'images/pdf.png', __FILE__ ) . '" alt="image_pdf" title="View PDF" /></a>';
			}
			if ( 1 == $pdfprnt_options_array['show_btn_print'] ) {
				if ( ! is_front_page () )
					$permalink = add_query_arg( 'print' , 'print' , get_permalink( $post->ID ) );
				else 
					$permalink = add_query_arg( 'print' , 'print' , get_permalink() . '?page_id=' . $post->ID );
				$str .= '<a href="' . $permalink . '" target="_blank"><img src="' . plugins_url( 'images/print.gif', __FILE__ ) . '" alt="image_print" title="Print Content" /></a>';
			}
			$str .= '</div>';
			if ( $position )
				$content = $str . $content;
			else
				$content = $content . $str;
			unset( $position );
			unset( $str );
		}
		return $content;
	}
}

/* Output buttons for search or archive pages */
if( ! function_exists( 'pdfprnt_show_buttons_search_archive' ) ) {
	function pdfprnt_show_buttons_search_archive() {
		global $pdfprnt_options_array, $pdfprnt_output_count_buttons, $wp, $posts;
		if ( 0 < $pdfprnt_output_count_buttons )
			return;
		if ( 1 !== $pdfprnt_options_array['show_btn_pdf_search_archive'] && 1 !== $pdfprnt_options_array['show_btn_print_search_archive'] )
			return;
		if ( ( ! is_search() && ! is_archive() ) || ( is_category() || ! have_posts() ) )
			return;
		/* Check for existence the type of posts. */
		$is_return = true;
		foreach ( $posts as $post ) {
			if ( in_array( get_post_type( $post ), $pdfprnt_options_array['use_types_posts'] ) ) {
				$is_return = false;
				break;
			}
		}
		if ( $is_return )
			return;
		$pdfprnt_output_count_buttons++;
		if ( empty( $wp->request ) )
			$current_url = add_query_arg( $wp->query_string, '', home_url() );
		else
			$current_url = home_url( $wp->request );
		$str = '<div class="' . $pdfprnt_options_array['position_search_archive'] . '">';
		if ( 1 == $pdfprnt_options_array['show_btn_pdf_search_archive'] ) {
			$permalink = add_query_arg( 'print' , 'pdf-page' , $current_url );
			$str .= '<a href="' . $permalink . '" target="_blank"><img src="' . plugins_url( 'images/pdf.png', __FILE__ ) . '" alt="image_pdf" title="Print PDF" /></a>';
		}
		if ( 1 == $pdfprnt_options_array['show_btn_print_search_archive'] ) {
			$permalink		=	add_query_arg( 'print' , 'print-page' , $current_url );
			$str .= '<a href="' . $permalink . '" target="_blank"><img src="' . plugins_url( 'images/print.gif', __FILE__ ) . '" alt="image_print" title="Print Content" /></a>';
		}
		$str .= '</div>';
		unset( $current_url );
		unset( $permalink );
		echo $str;
	}
}
if( ! function_exists( 'pdfprnt_auto_show_buttons_search_archive' ) ) {
	function pdfprnt_auto_show_buttons_search_archive() {
		if ( is_archive() || is_search() )
			add_action( 'loop_start', 'pdfprnt_show_buttons_search_archive' );
	}
	add_action( 'wp_head', 'pdfprnt_auto_show_buttons_search_archive' );
}

/* Output buttons of page for BWS Portfolio plugin */
if( ! function_exists( 'pdfprnt_show_buttons_for_bws_portfolio' ) ) {
	function pdfprnt_show_buttons_for_bws_portfolio() {
		global $pdfprnt_options_array, $pdfprnt_output_count_buttons, $wp;
		if ( 0 < $pdfprnt_output_count_buttons )
			return;
		if ( 1 !== $pdfprnt_options_array['show_btn_pdf_custom'] && 1 !== $pdfprnt_options_array['show_btn_print_custom'] )
			return;
		if ( ! in_array( 'portfolio', $pdfprnt_options_array['use_types_posts'] ) )
			return;
		$pdfprnt_output_count_buttons++;
		if ( empty( $wp->request ) )
			$current_url = add_query_arg( $wp->query_string, '', home_url() );
		else
			$current_url = home_url( $wp->request );
		$str = '<div class="' . $pdfprnt_options_array['position_custom'] . '">';
		if ( 1 == $pdfprnt_options_array['show_btn_pdf_custom'] ) {
			$permalink = add_query_arg( 'print' , 'pdf-portfolio-page' , $current_url );
			$str .= '<a href="' . $permalink . '" target="_blank"><img src="' . plugins_url( 'images/pdf.png', __FILE__ ) . '" alt="image_pdf" title="Print PDF" /></a>';
		}
		if ( 1 == $pdfprnt_options_array['show_btn_print_custom'] ) {
			$permalink = add_query_arg( 'print' , 'print-portfolio-page' , $current_url );
			$str .= '<a href="' . $permalink . '" target="_blank"><img src="' . plugins_url( 'images/print.gif', __FILE__ ) . '" alt="image_print" title="Print Content" /></a>';
		}
		$str .= '</div>';
		unset( $current_url );
		unset( $permalink );
		return $str;
	}
}

/* Output buttons of post for BWS Portfolio plugin */
if( ! function_exists( 'pdfprnt_show_buttons_for_bws_portfolio_post' ) ) {
	function pdfprnt_show_buttons_for_bws_portfolio_post() {
		global $pdfprnt_options_array, $post;
		if ( 1 !== $pdfprnt_options_array['show_btn_pdf_custom'] && 1 !== $pdfprnt_options_array['show_btn_print_custom'] )
			return;
		if ( ! in_array( get_post_type( $post ), $pdfprnt_options_array['use_types_posts'] ) )
			return;
		$current_url = get_permalink();
		$str = '<div class="' . $pdfprnt_options_array['position_custom'] . '">';
		if ( 1 == $pdfprnt_options_array['show_btn_pdf_custom'] ) {
			$permalink = add_query_arg( 'print' , 'pdf-portfolio' , $current_url );
			$str .= '<a href="' . $permalink . '" target="_blank"><img src="' . plugins_url( 'images/pdf.png', __FILE__ ) . '" alt="image_pdf" title="Print PDF" /></a>';
		}
		if ( 1 == $pdfprnt_options_array['show_btn_print_custom'] ) {
			$permalink = add_query_arg( 'print' , 'print-portfolio' , $current_url );
			$str .= '<a href="' . $permalink . '" target="_blank"><img src="' . plugins_url( 'images/print.gif', __FILE__ ) . '" alt="image_print" title="Print Content" /></a>';
		}
		$str .= '</div>';
		unset( $current_url );
		unset( $permalink );
		return $str;
	}
}

/* Output buttons of page for custom post type */
if( ! function_exists( 'pdfprnt_show_buttons_for_custom_post_type' ) ) {
	function pdfprnt_show_buttons_for_custom_post_type( $custom_query ) {
		global $pdfprnt_options_array, $pdfprnt_output_count_buttons;
		if ( 0 < $pdfprnt_output_count_buttons )
			return;
		if ( 1 !== $pdfprnt_options_array['show_btn_pdf_custom'] && 1 !== $pdfprnt_options_array['show_btn_print_custom'] )
			return;
		if ( ! is_array( $custom_query ) && ! is_string( $custom_query ) )
			return;
		$custom_query = new WP_Query( $custom_query );
		/* Check for existence the type of posts. */
		$is_return = true;
		foreach ( $custom_query->posts as $post ) {
			if ( in_array( get_post_type( $post ), $pdfprnt_options_array['use_types_posts'] ) ) {
				$is_return = false;
				break;
			}
		}
		if ( $is_return )
			return;
		$pdfprnt_output_count_buttons++;
		$current_url	=	add_query_arg( $custom_query->query, '', home_url() );
		$str 			=	'<div class="' . $pdfprnt_options_array['position_custom'] . '">';
		if ( 1 == $pdfprnt_options_array['show_btn_pdf_custom'] ) {
			$permalink = add_query_arg( 'print' , 'pdf-custom-page' , $current_url );
			$str .= '<a href="' . $permalink . '" target="_blank"><img src="' . plugins_url( 'images/pdf.png', __FILE__ ) . '" alt="image_pdf" title="Print PDF" /></a>';
		}
		if ( 1 == $pdfprnt_options_array['show_btn_print_custom'] ) {
			$permalink = add_query_arg( 'print' , 'print-custom-page' , $current_url );
			$str .= '<a href="' . $permalink . '" target="_blank"><img src="' . plugins_url( 'images/print.gif', __FILE__ ) . '" alt="image_print" title="Print Content" /></a>';
		}
		$str .= '</div>';
		unset( $current_url );
		unset( $permalink );
		unset( $is_return );
		unset( $custom_query );
		return $str;
	}
}

/* Add links */
if ( ! function_exists( 'pdfprnt_action_links' ) ) {
	function pdfprnt_action_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			$settings_link = '<a href="admin.php?page=pdf-print.php">' . __( 'Settings', 'pdf-print' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
}

/* Add links */
if ( ! function_exists( 'pdfprnt_links' ) ) {
	function pdfprnt_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			$links[]	=	'<a href="admin.php?page=pdf-print.php">' . __( 'Settings', 'pdf-print' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/pdf-print/faq/" target="_blank">' . __( 'FAQ', 'pdf-print' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'pdf-print' ) . '</a>';
		}
		return $links;
	}
}

/* Init plugin */
if ( ! function_exists ( 'pdfprnt_plugin_init' ) ) {
	function pdfprnt_plugin_init() {
		load_plugin_textdomain( 'pdf-print', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Add stylesheets */
if ( ! function_exists ( 'pdfprnt_admin_head' ) ) {
	function pdfprnt_admin_head() {
		global $wp_version;
		if ( $wp_version < 3.8 )
			wp_enqueue_style( 'pdfprnt-stylesheet', plugins_url( 'css/style_wp_before_3.8.css', __FILE__ ) );	
		else
			wp_enqueue_style( 'pdfprnt-stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

/* Generate templates for pdf file or print */
if ( ! function_exists( 'pdfprnt_generate_template' ) ) {
	function pdfprnt_generate_template( $content, $template = false, $isprint = false ) {
		global $pdfprnt_options_array;
		$tmpl	=	isset( $_GET['tmpl'] ) ? $_GET['tmpl'] : "";
		ob_start(); /* Starting output buffering */
		?>
			<html>
				<head>
					<?php if ( 1 == $pdfprnt_options_array['use_theme_stylesheet'] ) : ?>
						<link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo( 'stylesheet_url' ); ?>" media="all" />
					<?php else: ?>
						<link type="text/css" rel="stylesheet" href="<?php echo plugins_url( 'css/default.css', __FILE__ ); ?>" media="all" />
					<?php endif;
						if ( ! $template )
							$template = $pdfprnt_options_array['tmpl_post'];
						switch ( $template ) { /* Using template choosed on settings page */
							case 1: /* Allign left template */
								?>
								<style type="text/css">
									h1, h2, h3, h4, h5, h6 {
										text-align: left;
									}
									img {
										float: right;
									}
								</style>
								<?php
								break;
							case 2: /* Allign center template */
								?>
								<style type="text/css">
									h1, h2, h3, h4, h5, h6 {
										text-align: center;
									}
									img {
										float: left;
									}
								</style>
								<?php
								break;
							case 3: /* Allign right template */
								?>
								<style type="text/css">
									h1, h2, h3, h4, h5, h6 {
										text-align: right;
									}
									img {
										float: left;
									}
								</style>
								<?php
								break;
						}
						switch ( $tmpl ) { /* If got something by GET (from admin bar) */
							case 1: /* Allign left template */
								?>
								<style type="text/css">
									h1, h2, h3, h4, h5, h6 {
										text-align: left;
									}
									img {
										float: right;
									}
								</style>
								<?php
								break;
							case 2: /* Allign center template */
								?>
								<style type="text/css">
									h1, h2, h3, h4, h5, h6 {
										text-align: center;
									}
									img {
										float: left;
									}
								</style>
								<?php
								break;
							case 3: /* Allign right template */
								?>
								<style type="text/css">
									h1, h2, h3, h4, h5, h6 {
										text-align: right;
									}
									img {
										float: left;
									}
								</style>
							<?php break;
						}
						if ( $isprint ) : ?>
						<script type="text/javascript" src="<?php echo plugins_url( 'js/pdfprnt.print.js', __FILE__ ); ?>"></script>
					<?php endif; ?>
				</head>
				<body>
					<?php echo $content; ?>
				</body>
			</html>
		<?php
		$html = ob_get_contents(); /* Getting output buffering */
		ob_end_clean(); /* Closing output buffering */
		return $html; /* Now we done with template */
	}
}

if ( ! function_exists( 'pdfprnt_generate_template_for_bws_portfolio' ) ) {
	function pdfprnt_generate_template_for_bws_portfolio() {
		global $post;
		ob_start(); /* Starting output buffering */
		$portfolio_options		=	get_option( 'prtfl_options' );
		$meta_values			=	get_post_custom( $post->ID );
		$post_thumbnail_id		=	get_post_thumbnail_id( $post->ID );
		if( empty ( $post_thumbnail_id ) ) {
			$args				=	array(
				'post_parent'		=>	$post->ID,
				'post_type'			=>	'attachment',
				'post_mime_type'	=>	'image',
				'numberposts'		=>	1
			);
			$attachments		=	get_children( $args );
			$post_thumbnail_id	=	key( $attachments );
		}
		$image		=	wp_get_attachment_image_src( $post_thumbnail_id, 'portfolio-thumb' );
		$image_alt	=	get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true );
		$image_desc	=	get_post( $post_thumbnail_id );
		$image_desc	=	$image_desc->post_content;
		if ( '1' == get_option( 'prtfl_postmeta_update' ) ) {
			$post_meta	=	get_post_meta( $post->ID, 'prtfl_information', true );
			$date_compl	=	$post_meta['_prtfl_date_compl'];
			if ( ! empty( $date_compl ) && 'in progress' != $date_compl ) {
				$date_compl	=	explode( '/', $date_compl );
				$date_compl	=	date( get_option( 'date_format' ), strtotime( $date_compl[1] . '-' . $date_compl[0] . '-' . $date_compl[2] ) );
			}
			$link			=	$post_meta['_prtfl_link'];
			$short_descr	=	$post_meta['_prtfl_short_descr'];
		} else {
			$date_compl		=	get_post_meta( $post->ID, '_prtfl_date_compl', true );
			if ( ! empty( $date_compl ) && 'in progress' != $date_compl ) {
				$date_compl	=	explode( '/', $date_compl );
				$date_compl	=	date( get_option( 'date_format' ), strtotime( $date_compl[1] . '-' . $date_compl[0] . '-' . $date_compl[2] ) );
			}
			$link			=	get_post_meta( $post->ID, '_prtfl_link', true);
			$short_descr	=	$post_meta['_prtfl_short_descr'];
		} ?>
			<img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" alt="<?php echo $image_alt; ?>" />
			<div>
				<p>
					<strong><?php _e( 'Date of completion', 'pdf-print' ); ?>:</strong> <?php echo $date_compl; ?><br/>
					<strong><?php _e( 'Link', 'pdf-print' ); ?>:</strong> <a href="<?php echo $link; ?>"><?php echo $link; ?></a><br/>
					<strong><?php _e( 'Description', 'pdf-print' ); ?>:</strong> <?php echo $short_descr; ?><br/>
				</p>
			</div>
			<?php $terms = wp_get_object_terms( $post->ID, 'portfolio_technologies' );
			if ( is_array( $terms ) && count( $terms ) > 0 ) { ?>
				<div style="clear:both;">
					<strong><?php _e( 'Technologies', 'pdf-print' ); ?>: </strong>
				<?php $count = 0;
				foreach ( $terms as $term ) {
					if( 0 < $count )
						echo ', ';
					echo '<a href="' . get_term_link( $term->slug, 'portfolio_technologies' ) . '" title="' . sprintf( __( "View all posts in %s" ), $term->name ) . '" ' . '>' . $term->name . '</a>';
					$count++;
				} ?>
				</div>
			<?php }
		$content = ob_get_contents(); /* Getting output buffering */
		ob_end_clean(); /* Closing output buffering */
		return $content; /* Now we done with template */
	}
}

/* Generate query posts for Portfolio plugin */
if ( ! function_exists( 'generate_query_posts_for_portfolio' ) ) {
	function generate_query_posts_for_portfolio() {
		global $wp_query;
		$paged			=	isset( $wp_query->query_vars['paged'] ) ? $wp_query->query_vars['paged'] : 1;
		$technologies	=	isset( $wp_query->query_vars["technologies"] ) ? $wp_query->query_vars["technologies"] : "";
		if ( "" != $technologies ) {
			$args		=	array(
				'post_type'			=>	'portfolio',
				'post_status'		=>	'publish',
				'posts_per_page'	=>	get_option('posts_per_page'),
				'paged' 			=>	$paged,
				'tax_query'			=>	array(
					array(
						'taxonomy'	=>	'portfolio_technologies',
						'field'		=>	'slug',
						'terms'		=>	$technologies
					)
				)
			);
		} else {
			$args		=	array(
				'post_type'			=>	'portfolio',
				'post_status'		=>	'publish',
				'posts_per_page'	=>	get_option('posts_per_page'),
				'paged'				=>	$paged
			);
		}
		query_posts( $args );
	}
}

/* Output print page or pdf document and include plugin script */
if ( ! function_exists( 'pdfprnt_print' ) ) {
	function pdfprnt_print( $query ) {
		global $pdfprnt_options_array, $posts, $post;
		if ( $print	= get_query_var( 'print' ) ) {
			remove_all_filters( 'the_content' );
			add_filter( 'the_content', 'capital_P_dangit', 11 );
			add_filter( 'the_content', 'wptexturize' );
			add_filter( 'the_content', 'convert_smilies' );
			add_filter( 'the_content', 'convert_chars' );
			add_filter( 'the_content', 'wpautop' );
			if ( 1 == $pdfprnt_options_array['tmpl_shorcode'] ) {
				add_filter( 'the_content', 'do_shortcode' ); /* executing shorcodes on the page */
			} else {
				$pattern = get_shortcode_regex();
				if ( preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) ) { /* getting all shortcodes we are using */
					foreach ( array_unique( $matches[0] ) as $value) {
						$post->post_content = str_replace( $value, "", $post->post_content ); /* replacing shorcodes to an empty string */
					}
				}
			}
			switch ( $print ) {
				case 'pdf':	/* Content for PDF from post */
					include ( 'mpdf/mpdf.php' );
					$mpdf		=	new mPDF( get_bloginfo( 'charset' ) );
					$mpdf		=	new mPDF('+aCJK');
					$user_info	=	get_userdata( $post->post_author );
					$mpdf->SetAutoFont(AUTOFONT_ALL);
					$mpdf->SetAuthor( $user_info->display_name );
					$mpdf->SetTitle( $post->post_title );
					$mpdf->SetSubject( get_bloginfo( 'blogdescription' ) );
					$html = '<div class="container">
								<div class="title"><h1><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></h1></div><br/>
								<div class="content">' . apply_filters( 'the_content', $post->post_content ) . '</div>
							</div>';
					$mpdf->WriteHTML( pdfprnt_generate_template( $html ) );
					$mpdf->Output();
					unset( $user_info );
					unset( $html );
					unset( $mpdf );
					die();
					break;
				case 'print': /* Content for printing from post */
					$html = '<div class="container">
								<div class="title"><h1>' . $post->post_title . '</h1></div><br/>
								<div class="content">' . apply_filters( 'the_content', $post->post_content ) . '</div>
							</div>';
					echo pdfprnt_generate_template( $html, false, true );
					unset( $html );
					die();
					break;
				case 'pdf-page': /* Content for PDF from archive or searching page */
					$html		=	'';
					$titles		=	array();
					$authors	=	array();
					include ( 'mpdf/mpdf.php' );
					$mpdf		=	new mPDF( get_bloginfo( 'charset' ) );
					$mpdf		=	new mPDF('+aCJK');
					foreach ( $posts as $p ) {
						if ( ! in_array( get_post_type( $p ), $pdfprnt_options_array['use_types_posts'] ) )
							continue;
						$titles[]	=	$p->post_title;
						$user_info	=	get_userdata( $p->post_author );
						$authors[]	=	$user_info->display_name;
						$html		.=	'<div class="container">
											<div class="title"><h1><a href="' . get_permalink( $p->ID ) . '">' . $p->post_title . '</a></h1></div><br/>
											<div class="content">' . apply_filters( 'the_content', $p->post_content ) . '</div>
										</div><br/><hr/><br/>';
						unset( $user_info );
					}
					$titles		=	array_unique( $titles );
					$authors	=	array_unique( $authors );
					$mpdf->SetAutoFont(AUTOFONT_ALL);
					$mpdf->SetTitle( implode( ',', $titles ) );
					$mpdf->SetAuthor( implode( ',', $authors ) );
					$mpdf->SetSubject( get_bloginfo( 'blogdescription' ) );
					$mpdf->WriteHTML( pdfprnt_generate_template( $html, $pdfprnt_options_array['tmpl_search'] ) );
					$mpdf->Output();
					unset( $authors );
					unset( $titles );
					unset( $html );
					unset( $mpdf );
					die();
					break;
				case 'print-page': /* Content for printing from archive or searching page */
					$html = '';
					foreach ( $posts as $p ) {
						if ( ! in_array( get_post_type( $p ), $pdfprnt_options_array['use_types_posts'] ) )
							continue;
						$html .= '<div class="container">
									<div class="title"><h1>' . $p->post_title . '</h1></div><br/>
									<div class="content">' . apply_filters( 'the_content', $p->post_content ) . '</div>
								</div><br/><hr/><br/>';
					}
					echo pdfprnt_generate_template( $html, $pdfprnt_options_array['tmpl_search'], true );
					unset( $html );
					die();
					break;
				case 'pdf-portfolio': /* Content for PDF from portfolio post */
					include ( 'mpdf/mpdf.php' );
					$mpdf			=	new mPDF( get_bloginfo( 'charset' ) );
					$mpdf			=	new mPDF( '+aCJK' );
					$user_info		=	get_userdata( $post->post_author );
					$mpdf->SetAutoFont( AUTOFONT_ALL );
					$mpdf->SetAuthor( $user_info->display_name );
					$mpdf->SetTitle( $post->post_title );
					$mpdf->SetSubject( get_bloginfo( 'blogdescription' ) );
					$html = '<div class="container">
								<div class="title"><h1><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></h1></div><br/>
								<div class="content">' . pdfprnt_generate_template_for_bws_portfolio() . '</div>
							</div>';
					$mpdf->WriteHTML( pdfprnt_generate_template( $html, $pdfprnt_options_array['tmpl_custom'] ) );
					$mpdf->Output();
					unset( $html );
					unset( $user_info );
					unset( $mpdf );
					die();
					break;
				case 'print-portfolio': /* Content for printing from porfolio post */
					$html = '<div class="container">
								<div class="title"><h1>' . $post->post_title . '</h1></div><br/>
								<div class="content">' . pdfprnt_generate_template_for_bws_portfolio() . '</div>
							</div>';
					echo pdfprnt_generate_template( $html, $pdfprnt_options_array['tmpl_custom'], true );
					unset( $html );
					die();
					break;
				case 'pdf-portfolio-page': /* Content for PDF from portfolio page */
					$html		=	'';
					$titles		=	array();
					$authors	=	array();
					generate_query_posts_for_portfolio();
					while ( have_posts() ) {
						the_post();
						global $post;
						if ( ! in_array( get_post_type( $post ), $pdfprnt_options_array['use_types_posts'] ) )
							continue;
						$titles[]	=	$post->post_title;
						$user_info	=	get_userdata( $post->post_author );
						$authors[]	=	$user_info->display_name;
						$html		.=	'<div class="container">
											<div class="title"><h1><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></h1></div><br/>
											<div class="content">' . pdfprnt_generate_template_for_bws_portfolio() . '</div>
										</div><br/><hr/><br/>';
						unset( $user_info );
					}
					include ( 'mpdf/mpdf.php' );
					$mpdf		=	new mPDF( get_bloginfo( 'charset' ) );
					$mpdf		=	new mPDF( '+aCJK' );
					$titles		=	array_unique( $titles );
					$authors	=	array_unique( $authors );
					$mpdf->SetAutoFont( AUTOFONT_ALL );
					$mpdf->SetTitle( implode( ',', $titles ) );
					$mpdf->SetAuthor( implode( ',', $authors ) );
					$mpdf->SetSubject( get_bloginfo( 'blogdescription' ) );
					$mpdf->WriteHTML( pdfprnt_generate_template( $html, $pdfprnt_options_array['tmpl_custom'] ) );
					$mpdf->Output();
					unset( $authors );
					unset( $titles );
					unset( $html );
					unset( $mpdf );
					die();
					break;
				case 'print-portfolio-page': /* Content for printing from portfolio page */
					generate_query_posts_for_portfolio();
					$html = '';
					while ( have_posts() ) {
						the_post();
						global $post;
						if ( ! in_array( get_post_type( $post ), $pdfprnt_options_array['use_types_posts'] ) )
							continue;
						$html .= '<div class="container">
									<div class="title"><h1>' . $post->post_title . '</h1></div><br/>
									<div class="content">' . pdfprnt_generate_template_for_bws_portfolio() . '</div>
								</div><br/><hr/><br/>';
					}
					echo pdfprnt_generate_template( $html, $pdfprnt_options_array['tmpl_custom'], true );
					unset( $html );
					die();
					break;
				case 'pdf-custom-page': /* Content for PDF from custom post */
					$html		=	'';
					$titles		=	$authors	=	array();
					foreach ( $posts as $p ) {
						if ( ! in_array( get_post_type( $p ), $pdfprnt_options_array['use_types_posts'] ) )
							continue;
						$titles[]	=	$p->post_title;
						$user_info	=	get_userdata( $p->post_author );
						$authors[]	=	$user_info->display_name;
						$html		.=	'<div class="container">';
						$html		.=	'<div class="title"><h1><a href="' . get_permalink( $p->ID ) . '">' . $p->post_title . '</a></h1></div><br/>';
						$html		.=	'<div class="content">';
						if ( has_post_thumbnail( $p->ID ) )
							$html	.=	get_the_post_thumbnail( $p->ID, 'thumbnail' );
						$html		.=	apply_filters( 'the_content', $p->post_content ) . '</div></div><br/><hr/><br/>';
						unset( $user_info );
					}
					include ( 'mpdf/mpdf.php' );
					$mpdf		=	new mPDF( get_bloginfo( 'charset' ) );
					$mpdf		=	new mPDF( '+aCJK' );
					$titles		=	array_unique( $titles );
					$authors	=	array_unique( $authors );
					$mpdf->SetAutoFont( AUTOFONT_ALL );
					$mpdf->SetTitle( implode( ',', $titles ) );
					$mpdf->SetAuthor( implode( ',', $authors ) );
					$mpdf->SetSubject( get_bloginfo( 'blogdescription' ) );
					$mpdf->WriteHTML( pdfprnt_generate_template( $html, $pdfprnt_options_array['tmpl_custom'] ) );
					$mpdf->Output();
					unset( $authors );
					unset( $titles );
					unset( $html );
					unset( $mpdf );
					die();
					break;
				case 'print-custom-page': /* Content for printing from custom post */
					$html = '';
					foreach ( $posts as $p ) {
						if ( ! in_array( get_post_type( $p ), $pdfprnt_options_array['use_types_posts'] ) )
							continue;
						$html	.=	'<div class="container">';
						$html	.=	'<div class="title"><h1>' . $p->post_title . '</h1></div><br/>';
						$html	.=	'<div class="content">';
						if ( has_post_thumbnail( $p->ID ) )
							$html .= get_the_post_thumbnail( $p->ID, 'thumbnail' );
						$html 	.=	apply_filters( 'the_content', $p->post_content ) . '</div>
						</div><br/><hr/><br/>';
					}
					echo pdfprnt_generate_template( $html, $pdfprnt_options_array['tmpl_custom'], true );
					unset( $html );
					die();
					break;
			}
		}
	}
}

/* Add query vars */
if ( ! function_exists( 'print_vars_callback' ) ) {
	function print_vars_callback( $query_vars ) {
		$query_vars[]	=	'print';
		$query_vars[]	=	'tmpl';
		return $query_vars;
	}
}

/* Add custom admin bar menu */
if ( ! function_exists( 'pdfprnt_admin_bar_menu' ) ) {
	function pdfprnt_admin_bar_menu() {
		global $pdfprnt_options_array, $wp_admin_bar, $wp, $posts;
		if ( ! is_super_admin() || ! is_admin_bar_showing() ) /* Checking user */
			return;
		if ( ( ! is_search() && ! is_archive() ) || ( is_category() || ! have_posts() ) )
			return;
		$is_return = true;
		foreach ( $posts as $post ) {
			if ( in_array( get_post_type( $post ), $pdfprnt_options_array['use_types_posts'] ) ) {
				$is_return = false;
				break;
			}
		}
		if ( $is_return )
			return;
		if ( empty( $wp->request ) )
			$current_url = add_query_arg( $wp->query_string, '', home_url() );
		else
			$current_url = home_url( $wp->request );
		$permalink		=	add_query_arg( 'print', 'pdf-page', $current_url );
	    $wp_admin_bar->add_menu( array(
		    'id'		=>	'pdfprnt-bar-menu',
		    'title'		=>	'<span class="admin-bar-menu-bws-icon"></span>PDF',
		    'href'		=>	$permalink
		) );
		$template_url 	= add_query_arg( 'tmpl', '1', $permalink ); /* Adding parametrs to link */
		$wp_admin_bar->add_menu( array(
		    'parent'	=>	'pdfprnt-bar-menu',
			'id'		=>	'pdfprnt-template-1',
		    'title'		=>	'<img src="' . plugins_url( 'images/template_left.jpg', __FILE__ ) . '" alt="template_left" />',
		    'href'		=>	$template_url
		) );
		$template_url 	= add_query_arg( 'tmpl', '2', $permalink ); /* Adding parametrs to link */
		$wp_admin_bar->add_menu( array(
		    'parent'	=>	'pdfprnt-bar-menu',
			'id'		=>	'pdfprnt-template-2',
		    'title'		=>	'<img src="' . plugins_url( 'images/template_center.jpg', __FILE__ ) . '" alt="template_center" />',
		    'href'		=>	$template_url
		) );
		$template_url 	= add_query_arg( 'tmpl', '3', $permalink ); /* Adding parametrs to link */
		$wp_admin_bar->add_menu( array(
		    'parent'	=>	'pdfprnt-bar-menu',
			'id'		=>	'pdfprnt-template-3',
		    'title'		=>	'<img src="' . plugins_url( 'images/template_right.jpg', __FILE__ ) . '" alt="template_right" />',
		    'href'		=>	$template_url
		) );
		unset( $current_url );
		unset( $permalink );
		unset( $template_url );
	}
}

if ( ! function_exists('pdfprnt_admin_js') ) {
	function pdfprnt_admin_js() {
		if ( isset( $_GET['page'] ) && "pdf-print.php" == $_GET['page'] ) {
			/* Add notice about changing in the settings page */
			?>
			<script type="text/javascript">
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
			</script>
		<?php }
	}
}

/* Deleting plugin options on uninstalling */
if( ! function_exists( 'pdfprnt_uninstall' ) ) {
	function pdfprnt_uninstall() {
		delete_option( 'pdfprnt_options_array' );
		delete_site_option( 'pdfprnt_options_array' );
	}
}

/* Adding function to output PDF document or pirnt page */
add_action( 'wp', 'pdfprnt_print' );
/* Initialization default plugin settings */
add_action( 'init', 'pdfprnt_settings' );
/* Checks version of Wordpress required for plugin */
add_action( 'admin_init', 'pdfprnt_version_check' );
/* Load internationalization */
add_action( 'init', 'pdfprnt_plugin_init' );
/* Adding stylesheets */
add_action( 'init', 'pdfprnt_admin_head' );
/* Adding 'BWS Plugins' admin menu */
add_action( 'admin_menu', 'pdfprnt_add_pages' );
/* Add menu to bar menu WordPress */
add_action( 'admin_bar_menu', 'pdfprnt_admin_bar_menu', 1000 );
add_action( 'admin_head', 'pdfprnt_admin_js' );

/* Add query vars */
add_filter( 'query_vars', 'print_vars_callback' );
/* Adds "Settings" link to the plugin action page */
add_filter( 'plugin_action_links', 'pdfprnt_action_links', 10, 2 );
/* Additional links on the plugin page */
add_filter( 'plugin_row_meta', 'pdfprnt_links', 10, 2 );
/* Adding buttons plugin to content */
add_filter( 'the_content', 'pdfprnt_content' );

register_uninstall_hook( __FILE__, 'pdfprnt_uninstall' );
?>