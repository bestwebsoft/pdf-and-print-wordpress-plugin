<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! class_exists( 'Pdfprnt_Settings_Tabs' ) ) {
	class Pdfprnt_Settings_Tabs extends Bws_Settings_Tabs {
		public $post_types, $button_positions, $button_image, $margin_positions, $default_css_types, $upload_dir, $page_sizes, $editable_roles;
		public $need_fonts_reload = false;
		private $wp_sizes, $buttons;

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
				'settings'      => array( 'label' => __( 'Settings', 'pdf-print' ) ),
				'output'        => array( 'label' => __( 'Output', 'pdf-print' ) ),
				'display'       => array(
					'label'  => __( 'Display', 'pdf-print' ),
					'is_pro' => 1,
				),
				'misc'          => array( 'label' => __( 'Misc', 'pdf-print' ) ),
				'custom_fields' => array(
					'label'  => __( 'Custom Fields', 'pdf-print' ),
					'is_pro' => 1,
				),
				'custom_code'   => array( 'label' => __( 'Custom Code', 'pdf-print' ) ),
				'license'       => array( 'label' => __( 'License Key', 'pdf-print' ) ),
			);

			parent::__construct(
				array(
					'plugin_basename'    => $plugin_basename,
					'plugins_info'       => $pdfprnt_plugin_info,
					'prefix'             => 'pdfprnt',
					'default_options'    => pdfprnt_get_options_default(),
					'options'            => $pdfprnt_options,
					'is_network_options' => is_network_admin(),
					'tabs'               => $tabs,
					'wp_slug'            => 'pdf-print',
					'link_key'           => 'd9da7c9c2046bed8dfa38d005d4bffdb',
					'link_pn'            => '101',
					'doc_link'           => 'https://bestwebsoft.com/documentation/pdf-print/pdf-print-user-guide/',
					'doc_video_link'     => 'https://www.youtube.com/watch?v=Pec-6dDiou0',
				)
			);

			add_action( get_parent_class( $this ) . '_display_custom_messages', array( $this, 'display_custom_messages' ) );
			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
			add_action( get_parent_class( $this ) . '_additional_misc_options', array( $this, 'upgrade_mpdf' ) );

			$this->buttons = array(
				'pdf'   => __( 'PDF', 'pdf-print' ),
				'print' => __( 'Print', 'pdf-print' ),
			);

			/* Get post types */
			$this->post_types = get_post_types(
				array(
					'public'  => 1,
					'show_ui' => 1,
				),
				'objects'
			);
			unset( $this->post_types['attachment'] );
			$standard_post_types = array(
				'pdfprnt_search'   => __( 'Search results', 'pdf-print' ),
				'pdfprnt_archives' => __( 'Archives', 'pdf-print' ),
				'pdfprnt_blog'     => __( 'Posts page', 'pdf-print' ),
			);
			foreach ( $standard_post_types as $key => $value ) {
				$add_post_type            = new stdClass();
				$this->post_types[ $key ] = (object) array( 'label' => $value );
			}

			$this->button_positions = array(
				'top-left'         => __( 'Top Left', 'pdf-print' ),
				'top-right'        => __( 'Top Right', 'pdf-print' ),
				'bottom-left'      => __( 'Bottom Left', 'pdf-print' ),
				'bottom-right'     => __( 'Bottom Right', 'pdf-print' ),
				'top-bottom-left'  => __( 'Top & Bottom Left', 'pdf-print' ),
				'top-bottom-right' => __( 'Top & Bottom Right', 'pdf-print' ),
			);

			$this->button_image = array(
				'none'    => __( 'None', 'pdf-print' ),
				'default' => __( 'Default', 'pdf-print' ),
			);

			$this->margin_positions = array(
				'top'    => __( 'Top', 'pdf-print' ),
				'bottom' => __( 'Bottom', 'pdf-print' ),
				'left'   => __( 'Left', 'pdf-print' ),
				'right'  => __( 'Right', 'pdf-print' ),
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

			$this->wp_sizes = get_intermediate_image_sizes();
		}

		/**
		 * Display custom error\message\notice
		 *
		 * @access public
		 * @param  $save_results - array with error\message\notice
		 * @return void
		 */
		public function display_custom_messages( $save_results ) {
			global $pdfprnt_is_old_php;
			$message = $error = ''; ?>
			<noscript><div class="error below-h2"><p><strong><?php esc_html_e( 'Please enable JavaScript in your browser.', 'pdf-print' ); ?></strong></p></div></noscript>
			<?php if ( $pdfprnt_is_old_php ) { ?>
				<div class="error below-h2"><p><strong><?php printf( esc_html__( 'Pdf&Print plugin requires PHP %s or higher. Please contact your hosting provider to upgrade PHP version.', 'pdf-print' ), '5.4.0' ); ?></strong></p></div>
				<?php
			}

			/* Check fonts folder rights */
			if ( is_multisite() ) {
				switch_to_blog( 1 );
				$upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$upload_dir = wp_upload_dir();
			}
			$plugin_dir   = realpath( dirname( __FILE__ ) . '/..' );
			$ttfontdata   = $upload_dir['basedir'] . '/vendor/mpdf/mpdf/tmp';
			$is_installed = file_exists( $ttfontdata );
			if ( ! $is_installed ) {
				$ttfontdata = $plugin_dir . '/mpdf/ttfontdata';
			}
			if ( ! is_writable( $ttfontdata ) ) {
				if ( ! @chmod( $ttfontdata, 0755 ) ) {
					?>
					<div class="error below-h2">
						<p>
							<strong>
								<?php esc_html_e( 'Warning: Not enough permissions for the folder', 'pdf-print' ); ?>&nbsp;<i><?php echo esc_html( $ttfontdata ); ?></i>.<br />
								<?php
								printf(
									esc_html__( 'Please check and change permissions for your plugins folder (for folders - 755, for files - 644). For more info, please see %1$s and %2$s.', 'pdf-print' ),
									'<a href="https://codex.wordpress.org/Changing_File_Permissions" target="_blank">' . esc_html__( 'Changing File Permissions', 'pdf-print' ) . '</a>',
									'<a href="https://support.bestwebsoft.com/hc/en-us/articles/115000108003" target="_blank">' . esc_html__( 'FAQ', 'pdf-print' ) . '</a>'
								);
								?>
							</strong>
						</p>
					</div>
					<?php
				}
			}

			$fonts_path = $this->upload_dir['basedir'] . '/pdf-print-fonts';
			if ( file_exists( $fonts_path ) ) {
				$files = scandir( $fonts_path );
			}

			if ( ! is_dir( $fonts_path ) && 0 !== intval( $this->options['additional_fonts'] ) ) { /* if "pdf-print-fonts" folder was removed somehow */
				$error                   = sprintf( __( 'The folder %s was removed.', 'pdf-print' ), '"uploads/pdf-print-fonts"' );
				$this->need_fonts_reload = true;
			} elseif (
				is_dir( $fonts_path ) &&
				intval( $this->options['additional_fonts'] ) !== count( $files ) &&
				0 < $this->options['additional_fonts'] &&
				! $is_installed
			) { /* if some fonts was removed somehow from "pdf-print-fonts" folder */
				$error                   = sprintf( __( 'Some fonts were removed from %s folder.', 'pdf-print' ), '"uploads/pdf-print-fonts"' );
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
					$error                  .= '&nbsp;' . $result['error'] . '.&nbsp;' .
						sprintf(
						__( 'It is necessary to reload fonts. For more info, please see %s', 'pdf-print' ),
						'<a href="https://support.bestwebsoft.com/hc/en-us/articles/206693223" target="_blank">' . __( 'FAQ', 'pdf-print' ) . '</a>'
					);
					$this->need_fonts_reload = true;
				}
				if ( isset( $result['done'] ) ) {
					$message .= '&nbsp;' . $result['done'];
				}
			}
			if ( isset( $_POST['pdfprnt_upgrade_library'] ) ) {
				/* upgrade mPDF library if javascript is disabled */
				$result = pdfprnt_upgrade_library();
				if ( isset( $result['error'] ) ) {
					$error .= '&nbsp;' . $result['error'];
				}
				if ( isset( $result['done'] ) ) {
					$message .= '&nbsp;' . $result['done'];
				}
			}
			$new_library_path = $upload_dir['basedir'] . '/vendor/';
			if ( ! file_exists( $new_library_path ) ) {
				$message .= '&nbsp;' . __( 'The new version of the mPDF library which compatible with PHP V7.x.x is available now! Go to the Misc tab and upgrade the mPDF library.', 'pdf-print' );
			}
			?>
			<div class="updated below-h2" 
			<?php
			if ( empty( $message ) || '' !== $error ) {
				echo 'style="display:none"';}
			?>
			><p><strong><?php echo esc_html( $message ); ?></strong></p></div>
			<div class="error below-h2" 
			<?php
			if ( '' === $error ) {
				echo 'style="display:none"';}
			?>
			><p><strong><?php echo esc_html( $error ); ?></strong></p></div>
			<?php
		}

		/**
		 * Save plugin options to the database
		 *
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {

			$message = $notice = $error = '';

			if ( isset( $_POST['pdfprnt_load_fonts'] ) || isset( $_POST['pdfprnt_upgrade_library'] ) ) {
				return;
			}

			foreach ( $this->buttons as $button => $button_name ) {

				/* Add Button to */
				$this->options['button_post_types'][ $button ] = array();
				if ( isset( $_POST['pdfprnt_button_post_types'][ $button ] ) && is_array( $_POST['pdfprnt_button_post_types'][ $button ] ) ) {
					foreach ( $_POST['pdfprnt_button_post_types'][ $button ] as $post_type ) {
						if ( array_key_exists( sanitize_text_field( wp_unslash( $post_type ) ), $this->post_types ) ) {
							$this->options['button_post_types'][ $button ][] = sanitize_text_field( wp_unslash( $post_type ) );
						}
					}
				}

				/* Button Image */
				$this->options['button_image'][ $button ]['image_src'] = plugins_url( "images/{$button}.png", dirname( __FILE__ ) );

				if ( isset( $_POST['pdfprnt_button_image'][ $button ] ) ) {
					$new_button_image                                 = sanitize_text_field( wp_unslash( $_POST['pdfprnt_button_image'][ $button ] ) );
					$this->options['button_image'][ $button ]['type'] = array_key_exists( $new_button_image, $this->button_image ) ? $new_button_image : $this->options['button_image'][ $button ]['type'];
				}

				/* Button Title */
				$this->options['button_title'][ $button ] = isset( $_POST['pdfprnt_button_title'][ $button ] ) ? sanitize_text_field( wp_unslash( $_POST['pdfprnt_button_title'][ $button ] ) ) : $this->options['button_title'][ $button ];
			}

			/* Buttons Position */
			$this->options['buttons_position'] = isset( $_POST['pdfprnt_buttons_position'] ) && array_key_exists( sanitize_text_field( wp_unslash( $_POST['pdfprnt_buttons_position'] ) ), $this->button_positions ) ? sanitize_text_field( wp_unslash( $_POST['pdfprnt_buttons_position'] ) ) : $this->options['buttons_position'];

			/* Print Preview Window */
			$this->options['show_print_window'] = ( isset( $_POST['pdfprnt_show_print_window'] ) ) ? 1 : 0;

			/* Creating pdf with  image of screen */
			$this->options['image_to_pdf'] = ( isset( $_POST['pdfprnt_image_to_pdf'] ) ) ? 1 : 0;

			/* Shortcode Settings */
			$this->options['do_shorcodes'] = isset( $_POST['pdfprnt_do_shorcodes'] ) ? 1 : 0;

			$this->options['disable_links'] = isset( $_POST['pdfprnt_disable_links'] ) ? 1 : 0;
			$this->options['remove_links']  = isset( $_POST['pdfprnt_remove_links'] ) ? 1 : 0;
			/* PDF Page Size */
			$this->options['pdf_page_size'] = ( isset( $_POST['pdfprnt_pdf_page_size'] ) && in_array( sanitize_text_field( wp_unslash( $_POST['pdfprnt_pdf_page_size'] ) ), $this->page_sizes ) ) ? sanitize_text_field( wp_unslash( $_POST['pdfprnt_pdf_page_size'] ) ) : $this->options['pdf_page_size'];

			/* Margins */
			if ( isset( $_POST['pdfprnt_pdf_margins'] ) && is_array( $_POST['pdfprnt_pdf_margins'] ) ) {

				foreach ( $this->margin_positions as $key => $value ) {
					if ( isset( $_POST['pdfprnt_pdf_margins'][ $key ] ) ) {
						$this->options['pdf_margins'][ $key ] = intval( $_POST['pdfprnt_pdf_margins'][ $key ] );
					}
				}
			}

			/* Additional Elements */
			$this->options['show_title']          = isset( $_POST['pdfprnt_show_title'] ) ? 1 : 0;
			$this->options['show_author']         = isset( $_POST['pdfprnt_show_author'] ) ? 1 : 0;
			$this->options['show_date']           = isset( $_POST['pdfprnt_show_date'] ) ? 1 : 0;
			$this->options['show_featured_image'] = isset( $_POST['pdfprnt_show_featured_image'] ) ? 1 : 0;

			/* Featured Image Size */
			$this->options['featured_image_size'] = isset( $_POST['pdfprnt_featured_image_size'] ) && in_array( sanitize_text_field( wp_unslash( $_POST['pdfprnt_featured_image_size'] ) ), $this->wp_sizes ) ? sanitize_text_field( wp_unslash( $_POST['pdfprnt_featured_image_size'] ) ) : $this->options['featured_image_size'];

			$this->options['use_default_css'] = ( isset( $_POST['pdfprnt_use_default_css'] ) ) ? 1 : 0;
			$this->options['use_custom_css']  = ( isset( $_POST['pdfprnt_use_custom_css'] ) ) ? 1 : 0;

			if ( isset( $_POST['pdfprnt_custom_css_code'] ) ) {
				$custom_css_code = sanitize_text_field( trim( wp_strip_all_tags( wp_unslash( $_POST['pdfprnt_custom_css_code'] ) ) ) );
				if ( 10000 < strlen( $custom_css_code ) ) {
					$error = __( 'You have entered too much text in the "edit styles" field.', 'pdf-print' );
				} else {
					$this->options['custom_css_code'] = $custom_css_code;
				}
			}

			/* Buttons open */
			$this->options['file_action'] = isset( $_POST['pdfprnt_file_action'] ) && in_array( sanitize_text_field( wp_unslash( $_POST['pdfprnt_file_action'] ) ), array( 'download', 'open' ) ) ? sanitize_text_field( wp_unslash( $_POST['pdfprnt_file_action'] ) ) : $this->options['file_action'];

			/* All select */
			$this->options['enabled_roles'] = array();
			foreach ( $this->editable_roles as $role => $fields ) {
				$this->options['enabled_roles'][ $role ] = isset( $_POST[ 'pdfprnt_' . $role ] ) ? 1 : 0;
			}
			$this->options['enabled_roles']['unauthorized'] = isset( $_POST['pdfprnt_unauthorized'] ) ? 1 : 0;

			$this->options = apply_filters( 'pdfprnt_before_save_options', $this->options );
			update_option( 'pdfprnt_options', $this->options );
			$message .= __( 'Settings saved.', 'pdf-print' );

			return compact( 'message', 'notice', 'error' );
		}
		/**
		 * Display PDF & Print Settings tab
		 */
		public function tab_settings() {
			?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'PDF & Print Settings', 'pdf-print' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<table class="form-table pdfprnt-table-settings">
				<tr>
					<th scope="row"><?php esc_html_e( 'Add Button to', 'pdf-print' ); ?></th>
					<td>
						<?php foreach ( $this->buttons as $button => $button_name ) { ?>
							<div class="pdfprnt-col pdfprnt-col-2">
								<p><strong><?php echo esc_html( $button_name ); ?></strong></p>
								<br>
								<fieldset>
									<?php foreach ( $this->post_types as $key => $value ) { ?>
										<label>
											<input type="checkbox" name="pdfprnt_button_post_types[<?php echo esc_attr( $button ); ?>][]" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $this->options['button_post_types'][ $button ] ) ); ?> /> <?php echo esc_html( $value->label ); ?>
										</label><br>
									<?php } ?>
								</fieldset>
							</div>
						<?php } ?>
						<div class="clear"></div>
						<div class="bws_info"><?php esc_html_e( 'Follow the instruction in order to add the PDF & Print button to a custom post or page template.', 'pdf-print' ); ?> <a href="https://support.bestwebsoft.com/hc/en-us/articles/205454643" target="_blank"><?php esc_html_e( 'Learn More', 'pdf-print' ); ?></a></div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Buttons Position', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<?php foreach ( $this->button_positions as $key => $value ) { ?>
								<label><input type="radio" name="pdfprnt_buttons_position" value="<?php echo esc_attr( $key ); ?>"<?php checked( $key, $this->options['buttons_position'] ); ?> />&nbsp;<?php echo esc_html( $value ); ?></label>
								<?php
								if ( 'top-bottom-right' !== $key ) {
									echo '<br>';}
								?>
							<?php } ?>
						</fieldset>
						<div class="bws_info"><?php esc_html_e( 'Select buttons position in the content (default is Top Right).', 'pdf-print' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Button Image', 'pdf-print' ); ?></th>
					<td class="pdfprnt-td-button-image">
						<?php foreach ( $this->buttons as $button => $button_name ) { ?>
							<div class="pdfprnt-col pdfprnt-col-2">
								<p><strong><?php echo esc_html( $button_name ); ?></strong></p>
								<br>
								<fieldset>
									<?php foreach ( $this->button_image as $key => $value ) { ?>
										<div class="pdfprnt-button-image-<?php echo esc_attr( $button ); ?>">
											<label><input type="radio" name="pdfprnt_button_image[<?php echo esc_attr( $button ); ?>]" value="<?php echo esc_attr( $key ); ?>" data-button="<?php echo esc_attr( $button ); ?>" <?php checked( $key, $this->options['button_image'][ $button ]['type'] ); ?> /><?php echo esc_html( $value ); ?></label>
										</div>
									<?php } ?>
									<img class="pdfprnt-button-image-default-<?php echo esc_attr( $button ); ?>" alt="<?php esc_html_e( 'Default Image', 'pdf-print' ); ?>" src="<?php echo esc_url( $this->options['button_image'][ $button ]['image_src'] ); ?>" />
								</fieldset>
							</div>
						<?php } ?>
						<div class="clear"></div>
						<!-- pls -->
						<?php $this->pro_block( 'button_image_block', $this->buttons ); ?>
						<!-- end pls -->
					</td>
				</tr>
			</table>
			<table class="form-table pdfprnt-table-settings">
				<tr>
					<th><?php esc_html_e( 'Button Title', 'pdf-print' ); ?></th>
					<td>
						<?php foreach ( $this->buttons as $button => $button_name ) { ?>
							<div class="pdfprnt-col pdfprnt-col-2">
								<p><strong><?php echo esc_html( $button_name ); ?></strong></p>
								<br>
								<input class="widefat" name="pdfprnt_button_title[<?php echo esc_attr( $button ); ?>]" type="text" value="<?php echo esc_attr( $this->options['button_title'][ $button ] ); ?>" maxlength="50" />
							</div>
						<?php } ?>
						<div class="clear"></div>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Print Preview Window', 'pdf-print' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="pdfprnt_show_print_window" value="1" <?php checked( $this->options['show_print_window'] ); ?> />
							<span class="bws_info"><?php esc_html_e( 'Enable to display print preview window with advanced settings.', 'pdf-print' ); ?></span>
						</label>
					</td>
				</tr>
				<?php do_action( 'pdfprnt_display_settings_search_engine_visibility', $this->options, $this->change_permission_attr ); ?>
			</table>
			<!-- pls -->
			<?php $this->pro_block( 'noindex_block' ); ?>
			<!-- end pls -->
			<table class="form-table pdfprnt-table-settings">
				<tr>
					<th><?php esc_html_e( 'Default PDF Button Action', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="pdfprnt_file_action" value="download" <?php checked( $this->options['file_action'], 'download' ); ?> /><?php esc_html_e( 'Download PDF', 'pdf-print' ); ?>
							</label><br />
							<label>
								<input type="radio" name="pdfprnt_file_action" value="open" <?php checked( $this->options['file_action'], 'open' ); ?> /><?php esc_html_e( 'Open PDF', 'pdf-print' ); ?>
							</label><br />
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Enable Buttons for', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<label class=hide-if-no-js>
								<input type="checkbox" class="pdfprnt_select_all" /><strong><?php esc_html_e( 'All', 'pdf-print' ); ?></strong>
							</label><br />
							<?php
							foreach ( $this->editable_roles as $role => $fields ) {
								printf(
									'<label><input type="checkbox" name="%1$s" class="pdfprnt_role" value="%2$s" %3$s /> %4$s</label><br/>',
									'pdfprnt_' . esc_attr( $role ),
									esc_attr( $role ),
									checked( ! empty( $this->options['enabled_roles'][ $role ] ), true, false ),
									esc_attr( translate_user_role( $fields['name'] ) )
								);
							}
							?>
							<label>
								<input type="checkbox" name="pdfprnt_unauthorized" class="pdfprnt_role" value="1" <?php checked( ! empty( $this->options['enabled_roles']['unauthorized'] ) ); ?> /><?php esc_html_e( 'Unauthorized', 'pdf-print' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php
		}
		/**
		 * Display Output Settings tab
		 */
		public function tab_output() {
			$margin_positions_groups = array_chunk( array_keys( $this->margin_positions ), 2 );
			?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Document Output Settings', 'pdf-print' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<!-- pls -->
			<?php $this->pro_block( 'filename_orientation_block', $this->post_types ); ?>
			<!-- end pls -->
			<table class="form-table pdfprnt-table-settings">
				<tr>
					<th><?php esc_html_e( 'Full Page Capture to PDF', 'pdf-print' ); ?></th>
					<td>
						<label>
							<input<?php echo $this->change_permission_attr; ?> type="checkbox" name="pdfprnt_image_to_pdf" value="1" <?php checked( 1, $this->options['image_to_pdf'] ); ?> />
							<span class="bws_info"><?php esc_html_e( 'Enable to take a screenshot of the entire page and generate a PDF file from it.', 'pdf-print' ); ?></span>
						</label>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'PDF Page Size', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<select name="pdfprnt_pdf_page_size">
								<?php
								for ( $i = 0; $i < count( $this->page_sizes ); $i++ ) {
										$selected = ( $this->options['pdf_page_size'] === $this->page_sizes[ $i ] ) ? ' selected' : '';
										echo '<option value="' . esc_attr( $this->page_sizes[ $i ] ) . '"' . esc_attr( $selected ) . '>' . esc_attr( $this->page_sizes[ $i ] ) . '</option>';
								}
								?>
							</select>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Margins', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<?php foreach ( $margin_positions_groups as $margin_position_group ) { ?>
								<?php foreach ( $margin_position_group as $margin_position ) { ?>
									<div class="pdfprnt-col">
										<p><strong><?php echo esc_html( $this->margin_positions[ $margin_position ] ); ?></strong></p>
										<input type="number" class="pdfprnt_small_text" name="pdfprnt_pdf_margins[<?php echo esc_attr( $margin_position ); ?>]" min="0" max="297" step="1" value="<?php echo esc_attr( $this->options['pdf_margins'][ $margin_position ] ); ?>" /> <?php esc_html_e( 'px', 'pdf-print' ); ?>&emsp;
									</div>
								<?php } ?>
								<div class="clear"></div><br>
							<?php } ?>
						</fieldset>
					</td>
				</tr>
				<tr id="pdfprnt_print_shortcodes_wrap">
					<th><?php esc_html_e( 'Print Shortcodes', 'pdf-print' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="pdfprnt_do_shorcodes" value="1" <?php checked( $this->options['do_shorcodes'] ); ?> />
							<span class="bws_info"><?php esc_html_e( 'Enable to print shortcodes with data generated by other plugins (recommended).', 'pdf-print' ); ?></span>
						</label>
					</td>
				</tr>
				<tr id="pdfprnt_remove_links_wrap">
					<th><?php esc_html_e( 'Remove Links', 'pdf-print' ); ?></th>
					<td>
						<label>
							<input type="checkbox" class="bws_option_affect" data-affect-hide="#pdfprnt-disable-links" name="pdfprnt_remove_links" value="1" <?php checked( $this->options['remove_links'] ); ?> />
							<span class="bws_info"><?php esc_html_e( 'Enable to remove links from PDF and Print document.', 'pdf-print' ); ?></span>
						</label>
					</td>
				</tr>
				<tr id="pdfprnt-disable-links">
					<th><?php esc_html_e( 'Link Annotations', 'pdf-print' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="pdfprnt_disable_links" value="1" <?php checked( 1, $this->options['disable_links'] ); ?> />
							<span class="bws_info"><?php esc_html_e( 'Enable to remove hover link styles in PDF document.', 'pdf-print' ); ?></span>
						</label>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Additional Elements', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<label><input<?php echo $this->change_permission_attr; ?> type="checkbox" name="pdfprnt_show_title" value="1" class="bws_option_affect" data-affect-show=".pdfprnt-content-before-title" <?php checked( 1, $this->options['show_title'] ); ?> /> <?php esc_html_e( 'Title', 'pdf-print' ); ?></label><br>
							<label><input<?php echo $this->change_permission_attr; ?> type="checkbox" name="pdfprnt_show_author" value="1" <?php checked( 1, $this->options['show_author'] ); ?> /> <?php esc_html_e( 'Author', 'pdf-print' ); ?>
								<br>
								<span class="bws_info"><?php esc_html_e( 'for posts only', 'pdf-print' ); ?></span>
							</label><br>
							<label><input<?php echo $this->change_permission_attr; ?> type="checkbox" name="pdfprnt_show_date" value="1" <?php checked( 1, $this->options['show_date'] ); ?> /> <?php esc_html_e( 'Date', 'pdf-print' ); ?>
								<br>
								<span class="bws_info"><?php esc_html_e( 'for posts only', 'pdf-print' ); ?></span>
							</label><br>
							<label><input<?php echo $this->change_permission_attr; ?> type="checkbox" name="pdfprnt_show_featured_image" value="1" <?php checked( 1, $this->options['show_featured_image'] ); ?> /> <?php esc_html_e( 'Featured image', 'pdf-print' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<?php do_action( 'pdfprnt_display_settings_woocommerce', $this->options, $this->change_permission_attr ); ?>
				<tr id="pdfprnt_featured_image_size_wrap" valign="top">
					<th scope="row"><?php esc_html_e( 'Featured Image Size', 'pdf-print' ); ?></th>
					<td>
						<select name="pdfprnt_featured_image_size">
							<?php
							for ( $i = 0; $i < count( $this->wp_sizes ); $i++ ) {
								printf(
									'<option value="%s" %s>%s</option>',
									esc_attr( $this->wp_sizes[ $i ] ),
									selected( ( $this->options['featured_image_size'] === $this->wp_sizes[ $i ] ), true, false ),
									esc_attr( $this->wp_sizes[ $i ] )
								);
							}
							?>
						</select>
					</td>
				</tr>
			</table>
			<?php $this->pro_block( 'woocommerce_watermark_block' ); ?>
			<table class="form-table pdfprnt-table-settings">
				<?php
				$upload_dir   = wp_upload_dir();
				$path         = $upload_dir['basedir'] . '/vendor';
				$is_installed = file_exists( $path );
				if ( ! $is_installed ) {
					?>
					<tr id="pdfprnt_additional_fonts_wrap">
						<th><?php esc_html_e( 'Additional Fonts', 'pdf-print' ); ?></th>
						<td>						
							<?php
							if ( class_exists( 'ZipArchive' ) ) {
								$fonts_button_title =
										0 < $this->options['additional_fonts'] || /* loading not called yet */
										$this->need_fonts_reload /* loading occurred with errors or neccessary files lacks */
									?
											__( 'Reload Fonts', 'pdf-print' )
									:
											__( 'Load Fonts', 'pdf-print' );
								?>
								<input type="submit" class="button bws_no_bind_notice" value="<?php echo esc_attr( $fonts_button_title ); ?>" name="pdfprnt_load_fonts" />&nbsp;<span id="pdfprnt_font_loader" class="pdfprnt_loader"><img src="<?php echo esc_url( plugins_url( '../images/ajax-loader.gif', __FILE__ ) ); ?>" alt="loader" /></span><br />
								<input type="hidden" name="pdfprnt_action" value="pdfprnt_load_fonts" />
								<input type="hidden" name="pdfprnt_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'pdfprnt_ajax_nonce' ) ); ?>" />
								<div class="pdfprnt-additional-fonts-info">
									<?php if ( 0 < $this->options['additional_fonts'] ) { ?>
										<div class="bws_info"><?php esc_html_e( 'Additional fonts were loaded successfully', 'pdf-print' ); ?>.</div>
										<?php
									} else {
										if ( -1 === intval( $this->options['additional_fonts'] ) ) {
											?>
											<span><?php esc_html_e( 'If you have some problems with your internet connection, please, try to load additional fonts manually. For more info, please see', 'pdf-print' ); ?>&nbsp;<a href="https://support.bestwebsoft.com/hc/en-us/articles/206693223" target="_blank"><?php esc_html_e( 'FAQ', 'pdf-print' ); ?></a>.</span><br />
										<?php } ?>
										<div class="bws_info"><?php esc_html_e( 'Load additional fonts required for correct PDF file(-s) display.', 'pdf-print' ); ?></div>
									<?php } ?>
								</div>
							<?php } else { ?>
								<span style="color: red;"><strong><?php esc_html_e( 'WARNING', 'pdf-print' ); ?>:&nbsp;</strong><?php esc_html_e( 'ZipArchive Class is not installed on your server. It is impossible to load additional fonts.', 'pdf-print' ); ?></span>
								<?php
							}
							?>
						</td>
					</tr>
				<?php } ?>
				<tr id="pdfprnt_default_css_wrap">
					<th><?php esc_html_e( 'Default CSS', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input name="pdfprnt_use_default_css" type="checkbox" value="1" <?php checked( 1, $this->options['use_default_css'] ); ?> />
								<span class="bws_info"><?php esc_html_e( 'Enable to apply CSS from the current theme. Disable to use default CSS.', 'pdf-print' ); ?></span>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr id="pdfprnt_custom_css_wrap">
					<th><?php esc_html_e( 'Custom CSS', 'pdf-print' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input name="pdfprnt_use_custom_css" type="checkbox" value="1" <?php checked( 1, $this->options['use_custom_css'] ); ?> />
								<span class="bws_info"><?php esc_html_e( 'Enable to add custom CSS code to documents.', 'pdf-print' ); ?></span>
							</label>
							<div id="pdfprnt_custom_css_code_wrap" 
							<?php
							if ( 0 === intval( $this->options['use_custom_css'] ) ) {
								echo 'style="display: none;"';}
							?>
							>
								<textarea id="pdfprnt_custom_css_code" name="pdfprnt_custom_css_code" maxlength="10000" cols="50" rows="5"><?php echo esc_textarea( $this->options['custom_css_code'] ); ?></textarea>
							</div>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php
		}
		/**
		 * Display Settings tab
		 */
		public function tab_display() {
			?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Display Settings', 'pdf-print' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>

			<?php
			$this->pro_block( 'fancytree_block' );
		}

		/**
		 * Display Custom Fields settings tab
		 */
		public function tab_custom_fields() {
			?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Custom Fields Settings', 'pdf-print' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<?php
			$this->pro_block( 'custom_fields_block' );
		}
		/**
		 * Display custom metabox
		 *
		 * @access public
		 * @param  void
		 * @return html
		 */
		public function display_metabox() {
			?>
			<div class="postbox">
				<h3 class="hndle">
					<?php esc_html_e( 'PDF & Print Shortcode', 'pdf-print' ); ?>
				</h3>
				<div class="inside">
					<p><?php esc_html_e( 'Add PDF & Print buttons to a widget.', 'pdf-print' ); ?> <a href="widgets.php"><?php esc_html_e( 'Navigate to Widgets', 'pdf-print' ); ?></a></p>
				</div>
				<div class="inside">
					<?php esc_html_e( 'Add PDF button to your posts or pages using the following shortcode:', 'pdf-print' ); ?>
					<?php bws_shortcode_output( "[bws_pdfprint display='pdf']" ); ?>
				</div>
				<div class="inside">
					<?php esc_html_e( 'Add Print button to your posts or pages using the following shortcode:', 'pdf-print' ); ?>
					<?php bws_shortcode_output( "[bws_pdfprint display='print']" ); ?>
				</div>
				<div class="inside">
					<?php esc_html_e( 'Add PDF & Print buttons to your posts or pages using the following shortcode:', 'pdf-print' ); ?>
					<?php bws_shortcode_output( "[bws_pdfprint display='pdf,print']" ); ?>
				</div>
				<div class="inside">
					<?php esc_html_e( 'Create a page break in PDF document:', 'pdf-print' ); ?>
					<?php bws_shortcode_output( '[bws_pdfprint_pagebreak]' ); ?>
				</div>
			</div>
			<?php
		}

		/**
		 * Display bws_pro_version block by its name
		 */
		public function pro_block( $block_name = '', $args = array(), $force = false ) {
			$block_name = 'pdfprnt_' . $block_name;
			if ( ( ! $this->hide_pro_tabs || $force ) && function_exists( $block_name ) ) {
				?>
				<div class="bws_pro_version_bloc pdfprnt-pro-feature">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'pdf-print' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<div class="bws_pro_version">
							<?php $block_name( $args ); ?>
						</div>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
				<?php
			}
		}

		/**
		 * Custom functions for "Upgrade the mPDF"
		 *
		 * @access public
		 */
		public function upgrade_mpdf() {
			/* Disable block if mPDF 7 already installed */
			$path         = dirname( __FILE__ );
			if ( is_multisite() ) {
				switch_to_blog( 1 );
				$upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$upload_dir = wp_upload_dir();
			}
			$path         = $upload_dir['basedir'] . '/vendor';
			$is_installed = file_exists( $path );
			if ( ! $is_installed || ! isset( $this->options['mpdf_library_version'] ) || $this->options['mpdf_library_version'] !== $this->default_options['mpdf_library_version'] ) {
				?>
				<table class="form-table">
					<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Upgrade the mPDF library', 'pdf-print' ); ?></th>
						<td>
							<?php
							$is_class_exists = class_exists( 'ZipArchive' );
							if ( $is_class_exists ) {
								$upgrade = __( 'Upgrade', 'pdf-print' );
								?>
								<input name="pdfprnt_upgrade_library" type="submit" class="button" value="<?php echo $upgrade; ?>">&nbsp;<span id="pdfprnt_library_loader" class="pdfprnt_loader"><img src="<?php echo plugins_url( '../images/ajax-loader.gif', __FILE__ ); ?>" alt="loader" /></span><br />
								<input type="hidden" name="pdfprnt_action" value="pdfprnt_upgrade_library" />
								<input type="hidden" name="pdfprnt_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'pdfprnt_ajax_nonce' ) ); ?>" />
								<div class="bws_info">
									<?php
									printf(
										esc_html__( 'This will upgrade the mPDF library to version %s (recommended).', 'pdf-print' ),
										esc_attr( $this->default_options['mpdf_library_version'] )
									);
									?>
								</div>
							<?php } else { ?>
								<span style="color: red;"><strong><?php esc_html_e( 'WARNING', 'pdf-print' ); ?>:&nbsp;</strong><?php esc_html_e( 'ZipArchive сlass is not installed on your server. It is impossible to upgrade the mPDF library.', 'pdf-print' ); ?></span>
							<?php } ?>
						</td>
					</tr>
					</tbody>
				</table>
				<?php
			}
		}
	}
}
