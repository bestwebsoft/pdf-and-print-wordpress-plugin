<?php
/**
 * Displays the content on the plugin settings page
 */

require_once( dirname( dirname( __FILE__ ) ) . '/bws_menu/class-bws-settings.php' );

if ( ! class_exists( 'Pdfprnt_Settings_Tabs' ) ) {
	class Pdfprnt_Settings_Tabs extends Bws_Settings_Tabs {
		public $post_types, $button_positions, $button_image, $margin_positions, $default_css_types, $upload_dir, $page_sizes, $editable_roles;
		public $need_fonts_reload = false;

		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $pdfprnt_options, $pdfprnt_plugin_info, $wp_roles;

			$tabs = array(
				'settings'				=> array( 'label' => __( 'Settings', 'pdf-print' ) ),
				'output'				=> array( 'label' => __( 'Output', 'pdf-print' ) ),
				'display'				=> array( 'label' => __( 'Display', 'pdf-print' ), 'is_pro' => 1 ),
				'misc'					=> array( 'label' => __( 'Misc', 'pdf-print' ) ),
				'custom_fields'			=> array( 'label' => __( 'Custom Fields', 'pdf-print' ) , 'is_pro' => 1 ),
				'custom_code'			=> array( 'label' => __( 'Custom Code', 'pdf-print' ) ),
				'license'				=> array( 'label' => __( 'License Key', 'pdf-print' ) ),
			);

			parent::__construct( array(
				'plugin_basename'			=> $plugin_basename,
				'plugins_info'				=> $pdfprnt_plugin_info,
				'prefix'					=> 'pdfprnt',
				'default_options'			=> pdfprnt_get_options_default(),
				'options'					=> $pdfprnt_options,
				'is_network_options'		=> is_network_admin(),
				'tabs'						=> $tabs,
				'wp_slug'					=> 'pdf-print',
				'pro_page'					=> 'admin.php?page=pdf-print-pro.php',
				'bws_license_plugin'		=> 'pdf-print-pro/pdf-print-pro.php',
				'link_key'					=> 'd9da7c9c2046bed8dfa38d005d4bffdb',
				'link_pn'					=> '101'
			) );

			add_action( get_parent_class( $this ) . '_display_custom_messages', array( $this, 'display_custom_messages' ) );
			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );

			$this->buttons = array(
				'pdf'		=> __( 'PDF', 'pdf-print' ),
				'print'		=> __( 'Print', 'pdf-print' )
			);

			/* Get post types */
			$this->post_types = get_post_types( array( 'public' => 1, 'show_ui' => 1 ), 'objects' );
			unset( $this->post_types['attachment'] );
			$standard_post_types = array(
				'pdfprnt_search'		=> __( 'Search results', 'pdf-print' ),
				'pdfprnt_archives'		=> __( 'Archives', 'pdf-print' )
			);
			foreach ( $standard_post_types as $key => $value ) {
				$add_post_type = new stdClass();
				$this->post_types[ $key ] = (object) array( 'label' => $value );
			}

			$this->button_positions = array(
				'top-left'					=> __( 'Top Left', 'pdf-print' ),
				'top-right'					=> __( 'Top Right', 'pdf-print' ),
				'bottom-left'				=> __( 'Bottom Left', 'pdf-print' ),
				'bottom-right'				=> __( 'Bottom Right', 'pdf-print' ),
				'top-bottom-left'			=> __( 'Top & Bottom Left', 'pdf-print' ),
				'top-bottom-right'			=> __( 'Top & Bottom Right', 'pdf-print' )
			);

			$this->button_image = array(
				'none'		=> __( 'None', 'pdf-print' ),
				'default'	=> __( 'Default', 'pdf-print' )
			);

			$this->margin_positions = array(
				'top'			=> __( 'Top', 'pdf-print' ),
				'bottom'	=> __( 'Bottom', 'pdf-print' ),
				'left'		=> __( 'Left', 'pdf-print' ),
				'right'		=> __( 'Right', 'pdf-print' )
			);

			$this->default_css_types = array(
				'default'	=> __( 'Default', 'pdf-print' ),
				'theme'		=> __( 'Current theme', 'pdf-print' )
			);

			$this->page_sizes = pdfprnt_get_pdf_page_sizes();

			$this->editable_roles = $wp_roles->roles;

			if ( $this->is_multisite ) {
				switch_to_blog( 1 );
				$this->upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$this->upload_dir = wp_upload_dir();
			}
		}

		/**
		 * Display custom error\message\notice
		 * @access public
		 * @param  $save_results - array with error\message\notice
		 * @return void
		 */
		public function display_custom_messages( $save_results ) {
			global $pdfprnt_is_old_php;
			$message = $error = ""; ?>
			<noscript><div class="error below-h2"><p><strong><?php _e( "Please enable JavaScript in your browser.", 'pdf-print' ); ?></strong></p></div></noscript>
			<?php if ( $pdfprnt_is_old_php ) { ?>
				<div class="error below-h2"><p><strong><?php printf( __( "Pdf&Print plugin requires PHP %s or higher. Please contact your hosting provider to upgrade PHP version.", 'pdf-print' ), '5.4.0' ); ?></strong></p></div>
			<?php }

			/* Check fonts folder rights */
			$plugin_dir = realpath( dirname( __FILE__ ) . '/..' );
			$ttfontdata = $plugin_dir . '/mpdf/ttfontdata';
			if ( ! is_writable( $ttfontdata ) ) {
				if ( ! @chmod( $ttfontdata, 0755 ) ) { ?>
					<div class="error below-h2">
						<p>
							<strong>
								<?php _e( "Warning: Not enough permissions for the folder", 'pdf-print' ); ?>&nbsp;<i><?php echo $ttfontdata; ?></i>.<br />
								<?php printf( __( 'Please check and change permissions for your plugins folder (for folders - 755, for files - 644). For more info, please see %s and %s.', 'pdf-print' ),
									'<a href="https://codex.wordpress.org/Changing_File_Permissions" target="_blank">' . __( 'Changing File Permissions', 'pdf-print' ) . '</a>',
									'<a href="https://support.bestwebsoft.com/hc/en-us/articles/115000108003" target="_blank">' . __( 'FAQ', 'pdf-print' ) . '</a>' ); ?>
							</strong>
						</p>
					</div>
				<?php }
			}

			$fonts_path = $this->upload_dir['basedir'] .'/pdf-print-fonts';

			if ( ! is_dir( $fonts_path ) && $this->options['additional_fonts'] != 0 ) { /* if "pdf-print-fonts" folder was removed somehow */
				$error = sprintf( __( 'The folder %s was removed.', 'pdf-print' ), '"uploads/pdf-print-fonts"' );
				$this->need_fonts_reload = true;
			} elseif (
				is_dir( $fonts_path ) &&
				$this->options['additional_fonts'] != count( scandir( $fonts_path ) ) &&
				0 < $this->options['additional_fonts']
			) { /* if some fonts was removed somehow from "pdf-print-fonts" folder */
				$error = sprintf( __( 'Some fonts were removed from %s folder.', 'pdf-print' ), '"uploads/pdf-print-fonts"' );
				$this->need_fonts_reload = true;
			}

			if ( $this->need_fonts_reload ) {
				$error .= '&nbsp;' . sprintf(
					__( 'It is necessary to reload fonts. For more info, please see %s.', 'pdf-print' ),
					'<a href="https://support.bestwebsoft.com/hc/en-us/articles/206693223" target="_blank">' . __( 'FAQ', 'pdf-print' ) . '</a>'
				);
				pdfprnt_update_option( -1, true );
			}

			if ( isset( $_POST['pdfprnt_load_fonts'] ) ) {
				/* load additional fonts if javascript is disabled */
				$result = pdfprnt_load_fonts();
				if ( isset( $result['error'] ) ) {
					$error .= '&nbsp;' . $result['error'] . '.&nbsp;' .
						sprintf(
							__( 'It is necessary to reload fonts. For more info, please see %s', 'pdf-print' ),
							'<a href="https://support.bestwebsoft.com/hc/en-us/articles/206693223" target="_blank">' . __( 'FAQ', 'pdf-print' ) . '</a>'
						);
					$this->need_fonts_reload = true;
				}
				if ( isset( $result['done'] ) ) {
					$message .= '&nbsp;' . $result['done'];
				}
			} ?>
			<div class="updated below-h2" <?php if ( empty( $message ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="error below-h2" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
		<?php }

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {

			if ( isset( $_POST['pdfprnt_load_fonts'] ) ) {
				return;
			}

			foreach ( $this->buttons as $button => $button_name ) {

				/* Add Button to */
				$this->options['button_post_types'][ $button ] = array();
				if ( isset( $_POST['pdfprnt_button_post_types'][ $button ] ) && is_array( $_POST['pdfprnt_button_post_types'][ $button ] ) ) {
					foreach ( $_POST['pdfprnt_button_post_types'][ $button ] as $post_type ) {
						if ( array_key_exists( $post_type, $this->post_types ) ) {
							$this->options['button_post_types'][ $button ][] = $post_type;
						}
					}
				}

				/* Button Image */
				if ( isset( $_POST['pdfprnt_button_image'][ $button ] ) ) {
					$new_button_image = $_POST['pdfprnt_button_image'][ $button ];

					$this->options['button_image'][ $button ]['type'] =
						( array_key_exists( $new_button_image, $this->button_image ) )
					?
						$new_button_image
					:
						'default';
				}

				/* Button Title */
				$this->options['button_title'][ $button ] =
					( isset( $_POST['pdfprnt_button_title'][ $button ] ) )
				?
					stripslashes( sanitize_text_field( $_POST['pdfprnt_button_title'][ $button ] ) )
				:
					'';
			}

			/* Buttons Position */
			$this->options['buttons_position'] =
				(
					isset( $_POST['pdfprnt_buttons_position'] ) &&
					array_key_exists( $_POST['pdfprnt_buttons_position'], $this->button_positions )
				)
			?
				$_POST['pdfprnt_buttons_position']
			:
				'top-right';

			/* Print Preview Window */
			$this->options['show_print_window'] = ( isset( $_POST['pdfprnt_show_print_window'] ) ) ? 1 : 0;

			/* Shortcode Settings */
			$this->options['do_shorcodes'] = isset( $_POST['pdfprnt_do_shorcodes'] ) ? 1 : 0;

			$this->options['disable_links'] = isset( $_POST['pdfprnt_disable_links'] ) ? 1 : 0;

			/* PDF Page Size */
			$this->options['pdf_page_size'] = ( isset( $_POST['pdfprnt_pdf_page_size'] ) && in_array( $_POST['pdfprnt_pdf_page_size'], $this->page_sizes ) ) ? $_POST['pdfprnt_pdf_page_size'] : 'A4';

			/* Margins */
			if ( isset( $_POST['pdfprnt_pdf_margins'] ) && is_array( $_POST['pdfprnt_pdf_margins'] ) ) {

				foreach ( $this->margin_positions as $key => $value ) {
					if ( isset( $_POST['pdfprnt_pdf_margins'][ $key ] ) ) {
						$this->options['pdf_margins'][ $key ] = intval( $_POST['pdfprnt_pdf_margins'][ $key ] );
					}
				}
			}

			/* Additional Elements */
			$this->options['show_title'] = isset( $_POST['pdfprnt_show_title'] ) ? 1 : 0;
			$this->options['show_featured_image'] = isset( $_POST['pdfprnt_show_featured_image'] ) ? 1 : 0;

			/* Featured Image Size */
			$this->options['featured_image_size'] = isset( $_POST['pdfprnt_featured_image_size'] ) ? esc_attr( $_POST['pdfprnt_featured_image_size'] ) : 'thumbnail';

			/* Default CSS */
			$this->options['use_default_css'] =
				( isset( $_POST['pdfprnt_use_default_css'] ) && array_key_exists( $_POST['pdfprnt_use_default_css'], $this->default_css_types ) ) ? $_POST['pdfprnt_use_default_css'] : 'default';

			$this->options['use_custom_css'] = ( isset( $_POST['pdfprnt_use_custom_css'] ) ) ? 1 : 0;

			if ( isset( $_POST['pdfprnt_custom_css_code'] ) ) {
				$custom_css_code = trim( strip_tags( stripslashes( $_POST['pdfprnt_custom_css_code'] ) ) );
				if ( 10000 < strlen( $custom_css_code ) ) {
					$error = __( 'You have entered too much text in the "edit styles" field.', 'pdf-print' );
				} else {
					$this->options['custom_css_code'] = $custom_css_code;
				}
			}

			/* Buttons open */
			$this->options['file_action'] = ( isset( $_POST['pdfprnt_file_action'] ) ) ? $_POST['pdfprnt_file_action'] : 'open';

			/* All select */
			$this->options['enabled_roles'] = array();
			foreach ( $this->editable_roles as $role => $fields ) {
				$this->options['enabled_roles'][ $role ] = isset( $_POST['pdfprnt_' . $role ] ) ? 1 : 0;
			}
			$this->options['enabled_roles']['unauthorized'] = isset( $_POST['pdfprnt_unauthorized'] ) ? 1 : 0;

			update_option( 'pdfprnt_options', $this->options );
			$message = __( 'Settings saved.', 'pdf-print' );

			return compact( 'message', 'notice', 'error' );
		}

		public function tab_settings() { ?>
			<h3 class="bws_tab_label"><?php _e( 'PDF & Print Settings', 'pdf-print' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<table class="form-table pdfprnt-table-settings">
				<tr>
					<th scope="row"><?php _e( 'Add Button to', 'pdf-print' ); ?></th>
					<td>
						<?php foreach ( $this->buttons as $button => $button_name ) { ?>
							<div class="pdfprnt-col pdfprnt-col-2">
								<p><strong><?php echo $button_name; ?></strong></p>
								<br>
								<fieldset>
									<?php foreach ( $this->post_types as $key => $value ) { ?>
										<label>
											<input type="checkbox" name="pdfprnt_button_post_types[<?php echo $button; ?>][]" value="<?php echo $key; ?>" <?php checked( in_array( $key, $this->options['button_post_types'][ $button ] ) ); ?> /> <?php echo $value->label; ?>
										</label><br>
									<?php } ?>
								</fieldset>
							</div>
						<?php } ?>
						<div class="clear"></div>
						<div class="bws_info"><?php _e( 'Follow the instruction in order to add PDF & Print button to custom post or page template.', 'pdf-print' ); ?> <a href="https://support.bestwebsoft.com/hc/en-us/articles/205454643" target="_blank"><?php _e( 'Learn More', 'pdf-print' ); ?></a></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Buttons Position', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<?php foreach ( $this->button_positions as $key => $value ) { ?>
								<label><input type="radio" name="pdfprnt_buttons_position" value="<?php echo $key; ?>"<?php checked( $key, $this->options['buttons_position'] ); ?> />&nbsp;<?php echo $value; ?></label>
								<?php if ( $key != 'top-bottom-right' ) echo '<br>'; ?>
							<?php } ?>
						</fieldset>
						<div class="bws_info"><?php _e( 'Select buttons position in the content (default is Top Right).', 'pdf-print' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Button Image', 'pdf-print' ); ?></th>
					<td class="pdfprnt-td-button-image">
						<?php foreach ( $this->buttons as $button => $button_name ) { ?>
							<div class="pdfprnt-col pdfprnt-col-2">
								<p><strong><?php echo $button_name; ?></strong></p>
								<br>
								<fieldset>
									<?php foreach ( $this->button_image as $key => $value ) { ?>
										<div class="pdfprnt-button-image-<?php echo $button; ?>">
											<label><input type="radio" name="pdfprnt_button_image[<?php echo $button; ?>]" value="<?php echo $key; ?>" data-button="<?php echo $button; ?>" <?php checked( $key, $this->options['button_image'][ $button ]['type'] ); ?> /><?php echo $value; ?></label>
										</div>
									<?php } ?>
									<img class="pdfprnt-button-image-default-<?php echo $button; ?>" alt="<?php _e( 'Default Image', 'pdf-print' ); ?>" src="<?php echo $this->options['button_image'][ $button ]['image_src']; ?>" />
								</fieldset>
							</div>
						<?php } ?>
						<div class="clear"></div>
						<?php $this->pro_block( 'button_image_block', $this->buttons ); ?>
					</td>
				</tr>
			</table>
			<table class="form-table pdfprnt-table-settings">
				<tr>
					<th><?php _e( 'Button Title', 'pdf-print' ); ?></th>
					<td>
						<?php foreach ( $this->buttons as $button => $button_name ) { ?>
							<div class="pdfprnt-col pdfprnt-col-2">
								<p><strong><?php echo $button_name; ?></strong></p>
								<br>
								<input class="widefat" name="pdfprnt_button_title[<?php echo $button; ?>]" type="text" value="<?php echo $this->options['button_title'][ $button ]; ?>" maxlength="50" />
							</div>
						<?php } ?>
						<div class="clear"></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Print Preview Window', 'pdf-print' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="pdfprnt_show_print_window" value="1" <?php checked( $this->options['show_print_window'] ); ?> />
							<span class="bws_info"><?php _e( 'Enable to display print preview window with advanced settings.', 'pdf-print' ); ?></span>
						</label>
					</td>
				</tr>
			</table>
			<?php $this->pro_block( 'noindex_block' ); ?>
			<table class="form-table pdfprnt-table-settings">
				<tr>
					<th><?php _e( 'Print Shortcodes', 'pdf-print' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="pdfprnt_do_shorcodes" value="1" <?php checked( $this->options['do_shorcodes'] ); ?> />
							<span class="bws_info"><?php _e( 'Enable to print shortcodes with data generated by other plugins (recommended).', 'pdf-print' ); ?></span>
						</label>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Link Annotations', 'pdf-print' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="pdfprnt_disable_links" value="1" <?php checked( 1, $this->options['disable_links'] ); ?> />
							<span class="bws_info"><?php _e( 'Enable to remove hover link styles in PDF document.', 'pdf-print' ); ?></span>
						</label>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Download or Open', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="pdfprnt_file_action" value="download" <?php checked( $this->options['file_action'], 'download' ); ?> /><?php _e( 'Download PDF', 'pdf-print' ); ?>
							</label><br />
							<label>
								<input type="radio" name="pdfprnt_file_action" value="open" <?php checked( $this->options['file_action'], 'open' ); ?> /><?php _e( 'Open PDF', 'pdf-print' ); ?>
							</label><br />
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Enable Buttons for', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<label class=hide-if-no-js>
								<input type="checkbox" class="pdfprnt_select_all" /><strong><?php _e( 'All', 'pdf-print' ); ?></strong>
							</label><br />
							<?php foreach ( $this->editable_roles as $role => $fields ) {
									printf(
										'<label><input type="checkbox" name="%1$s" class="pdfprnt_role" value="%2$s" %3$s /> %4$s</label><br/>',
										'pdfprnt_' . $role,
										$role,
										checked( ! empty( $this->options['enabled_roles'][ $role ] ), true, false ),
										translate_user_role( $fields['name'] )
									);
								} ?>
							<label>
								<input type="checkbox" name="pdfprnt_unauthorized" class="pdfprnt_role" value="1" <?php checked( ! empty( $this->options['enabled_roles']['unauthorized'] ) ); ?> /><?php _e( 'Unauthorized', 'pdf-print' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php }

		public function tab_output() {
			$margin_positions_groups = array_chunk( array_keys( $this->margin_positions ), 2 );
			$wp_sizes = get_intermediate_image_sizes(); ?>
			<h3 class="bws_tab_label"><?php _e( 'Document Output Settings', 'pdf-print' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<?php $this->pro_block( 'filename_orientation_block', $this->post_types ); ?>
			<table class="form-table pdfprnt-table-settings">
				<tr>
					<th><?php _e( 'PDF Page Size', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<select name="pdfprnt_pdf_page_size">
								<?php for ($i=0; $i < count( $this->page_sizes ); $i++) {
										$selected = ( $this->options['pdf_page_size'] == $this->page_sizes[$i] ) ? ' selected' : '';
										echo '<option value="'.$this->page_sizes[$i].'"'.$selected.'>'.$this->page_sizes[$i].'</option>';
									}
								?>
							</select>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Margins', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<?php foreach ( $margin_positions_groups as $margin_position_group ) { ?>
								<?php foreach ( $margin_position_group as $margin_position ) { ?>
									<div class="pdfprnt-col">
										<p><strong><?php echo $this->margin_positions[ $margin_position ]; ?></strong></p>
										<input type="number" class="small-text" name="pdfprnt_pdf_margins[<?php echo $margin_position; ?>]" min="0" max="297" step="1" value="<?php echo $this->options['pdf_margins'][ $margin_position ]; ?>" /> <?php _e( 'px', 'pdf-print' ); ?>&emsp;
									</div>
								<?php } ?>
								<div class="clear"></div><br>
							<?php } ?>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Additional Elements', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<label><input type="checkbox" name="pdfprnt_show_title" value="1" <?php checked( 1, $this->options['show_title'] ); ?> /> <?php _e( 'Title', 'pdf-print' ); ?></label><br>
							<label><input type="checkbox" name="pdfprnt_show_featured_image" value="1" <?php checked( 1, $this->options['show_featured_image'] ); ?> /> <?php _e( 'Featured image', 'pdf-print' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<tr id="pdfprnt_featured_image_size_wrap" valign="top">
					<th scope="row"><?php _e( 'Featured Image Size', 'pdf-print' ); ?></th>
					<td>
						<select name="pdfprnt_featured_image_size">
							<?php for ($i=0; $i < count( $wp_sizes ); $i++) {
								printf(
									'<option value="%s" %s>%s</option>',
										$wp_sizes[$i],
										selected( ( $this->options['featured_image_size'] == $wp_sizes[$i] ), true, false ),
										$wp_sizes[$i]
								);
							} ?>
						</select>
					</td>
				</tr>
			</table>
			<?php $this->pro_block( 'woocommerce_watermark_block' ); ?>
			<table class="form-table pdfprnt-table-settings">
				<tr>
					<th><?php _e( 'Additional Fonts', 'pdf-print' ); ?></th>
					<td>
						<?php if ( class_exists( 'ZipArchive' ) ) {
							$fonts_button_title =
									0 < $this->options['additional_fonts'] || /* loading not called yet */
									$this->need_fonts_reload /* loading occurred with errors or neccessary files lacks */
								?
									__( 'Reload Fonts', 'pdf-print' )
								:
									__( 'Load Fonts', 'pdf-print' ); ?>
							<input type="submit" class="button bws_no_bind_notice" value="<?php echo $fonts_button_title; ?>" name="pdfprnt_load_fonts" />&nbsp;<span id="pdfprnt_font_loader" class="pdfprnt_loader"><img src="<?php echo plugins_url( '../images/ajax-loader.gif', __FILE__ ); ?>" alt="loader" /></span><br />
							<input type="hidden" name="pdfprnt_action" value="pdfprnt_load_fonts" />
							<input type="hidden" name="pdfprnt_ajax_nonce" value="<?php echo wp_create_nonce( 'pdfprnt_ajax_nonce' ); ?>" />
							<div class="pdfprnt-additional-fonts-info">
								<?php if ( 0 < $this->options['additional_fonts'] ) { ?>
									<div class="bws_info"><?php _e( 'Additional fonts were loaded successfully', 'pdf-print' ); ?>.</div>
								<?php } else {
									if ( -1 == $this->options['additional_fonts'] ) { ?>
										<span><?php _e( 'If you have some problems with your internet connection, please, try to load additional fonts manually. For more info, please see', 'pdf-print' ); ?>&nbsp;<a href="https://support.bestwebsoft.com/hc/en-us/articles/206693223" target="_blank"><?php _e( 'FAQ', 'pdf-print' ); ?></a>.</span><br />
									<?php } ?>
									<div class="bws_info"><?php _e( 'Load additional fonts required for PDF file(-s).', 'pdf-print' ); ?></div>
								<?php } ?>
							</div>
						<?php } else { ?>
							<span style="color: red;"><strong><?php _e( 'WARNING', 'pdf-print' ); ?>:&nbsp;</strong><?php _e( 'ZipArchive Class is not installed on your server. It is impossible to load additional fonts.', 'pdf-print' ); ?></span>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Default CSS', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<?php foreach ( $this->default_css_types as $key => $value ) { ?>
								<label><input name="pdfprnt_use_default_css" type="radio" value="<?php echo $key; ?>" <?php checked( $key, $this->options['use_default_css'] ); ?> /> <?php echo $value; ?></label>
								<?php if ( $key != 'theme' ) echo '<br>'; ?>
							<?php } ?>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Custom CSS', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input name="pdfprnt_use_custom_css" type="checkbox" value="1" <?php checked( 1, $this->options['use_custom_css'] ); ?> />
								<span class="bws_info"><?php _e( 'Enable to add custom CSS code to documents.', 'pdf-print' ); ?></span>
							</label>
							<div id="pdfprnt_custom_css_code_wrap" <?php if ( 0 == $this->options['use_custom_css'] ) echo 'style="display: none;"'; ?>>
								<textarea id="pdfprnt_custom_css_code" name="pdfprnt_custom_css_code" maxlength="10000" cols="50" rows="5"><?php echo $this->options['custom_css_code']; ?></textarea>
							</div>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php }

		public function tab_display() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Display Settings', 'pdf-print' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<?php $this->pro_block( 'fancytree_block' );
		}

		/* display Custom Fields settings tab */
		public function tab_custom_fields() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Custom Fields Settings', 'pdf-print' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<?php $this->pro_block( 'custom_fields_block' );
		}

		/**
		 * Display custom metabox
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function display_metabox() { ?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'PDF&Print Shortcode', 'pdf-print' ); ?>
				</h3>
				<div class="inside">
					<p><?php _e( 'Add PDF&Print buttons to a widget.', 'pdf-print' ); ?> <a href="widgets.php"><?php _e( 'Navigate to Widgets', 'pdf-print' ); ?></a></p>
				</div>
				<div class="inside">
					<?php _e( "Add PDF button to your posts or pages using the following shortcode:", 'pdf-print' ); ?>
					<?php bws_shortcode_output( "[bws_pdfprint display='pdf']" ); ?>
				</div>
				<div class="inside">
					<?php _e( "Add Print button to your posts or pages using the following shortcode:", 'pdf-print' ); ?>
					<?php bws_shortcode_output( "[bws_pdfprint display='print']" ); ?>
				</div>
				<div class="inside">
					<?php _e( "Add PDF&Print buttons to your posts or pages using the following shortcode:", 'pdf-print' ); ?>
					<?php bws_shortcode_output( "[bws_pdfprint display='pdf,print']" ); ?>
				</div>
			</div>
		<?php }

		/* Display bws_pro_version block by its name */
		public function pro_block( $block_name = '', $args = array(), $force = false ) {
			$block_name = 'pdfprnt_' . $block_name;
			if ( ( ! $this->hide_pro_tabs || $force ) && function_exists( $block_name ) ) { ?>
				<div class="bws_pro_version_bloc pdfprnt-pro-feature">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'pdf-print' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<div class="bws_pro_version">
							<?php $block_name( $args ); ?>
						</div>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php }
		}
	}
}
