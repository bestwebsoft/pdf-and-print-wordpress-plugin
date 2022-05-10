<?php
/**
 * Banners on plugin settings page
 *
 * @package PDF & Print by BestWebSoft
 * @since 1.8.5
 */

/**
 * Wrapper. Show ads for PRO on plugin settings page
 *
 * @param    string  $func      function to call
 * @param    boolean    $show_cross  when it is 'false' ad will be displayed regardless of if other blocks are closed
 * @return  void
 */
if ( ! function_exists( 'pdfprnt_pro_block' ) ) {
	function pdfprnt_pro_block( $func, $show_cross = true ) {
		global $pdfprnt_plugin_info, $wp_version, $pdfprnt_options;
		if ( ! bws_hide_premium_options_check( $pdfprnt_options ) || ! $show_cross ) { ?>
			<div class="bws_pro_version_bloc pdfprnt_pro_block <?php echo esc_attr( $func ); ?>" title="<?php esc_html_e( 'This options is available in Pro version of the plugin', 'pdf-print' ); ?>">
				<div class="bws_pro_version_table_bloc">
					<?php if ( $show_cross ) { ?>
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'pdf-print' ); ?>"></button>
					<?php } ?>
					<div class="bws_table_bg"></div>
					<?php call_user_func( $func ); ?>
				</div>
				<div class="bws_pro_version_tooltip">
					<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/pdf-print/?k=d9da7c9c2046bed8dfa38d005d4bffdb&pn=101&v=<?php echo esc_attr( $pdfprnt_plugin_info['Version'] ); ?>&wp_v=<?php echo esc_attr( $wp_version ); ?>" target="_blank" title="PDF & Print Pro Plugin"><?php esc_html_e( 'Upgrade to Pro', 'pdf-print' ); ?></a>
				</div>
			</div>
			<?php
		}
	}
}

/**
 * The content of ad block on the "Settings" tab
 *
 * @param    void
 * @return  void
 */
if ( ! function_exists( 'pdfprnt_button_image_block' ) ) {
	function pdfprnt_button_image_block( $buttons ) {
		?>
		<fieldset>
			<?php foreach ( $buttons as $button ) { ?>
				<div class="pdfprnt-col pdfprnt-col-2">
					<label><input disabled="disabled" type="radio" /><?php esc_html_e( 'Custom', 'pdf-print' ); ?></label><br />
					<input disabled="disabled" type="file" />
				</div>
			<?php } ?>
			<div class="clear"></div>
		</fieldset>
		<?php
	}
}

/**
 * The content of ad block on the "Settings" tab
 *
 * @param    void
 * @return  void
 */
if ( ! function_exists( 'pdfprnt_noindex_block' ) ) {
	function pdfprnt_noindex_block() {
		?>
		<table class="form-table pdfprnt-table-settings pdfprnt-table-search-engine">		   
			<tr>
				<th><?php esc_html_e( 'Search Engine Visibility', 'pdf-print' ); ?></th>
				<td>
					<input type="checkbox" disabled="disabled" />
					<span class="bws_info"><?php esc_html_e( 'Enable to disallow search engines from indexing PDF & Print pages.', 'pdf-print' ); ?></span>
				</td>
			</tr>
		</table>
		<?php
	}
}

/**
 * The content of ad block on the "Output" tab
 *
 * @param    void
 * @return  void
 */
if ( ! function_exists( 'pdfprnt_filename_orientation_block' ) ) {
	function pdfprnt_filename_orientation_block( $orientation_post_types = false ) {
		?>
		<table class="form-table pdfprnt-table-settings">
			<tr>
				<th><?php esc_html_e( 'Default PDF File Name', 'pdf-print' ); ?></th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="radio" checked="checked" /> <?php esc_html_e( 'Post/page slug', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="radio" /> <?php esc_html_e( 'Custom', 'pdf-print' ); ?></label>
						<div id="pdfprnt-file-name-wrap">
							<input disabled="disabled" class="widefat" id="pdfprnt-file-name" type="text" name="pdfprnt_file_name" value="" maxlength="195" placeholder="{Publish_month} {Publish_date}, {Publish_year} - {Post_title} - {Site_title}" />
							<div class="bws_info"><?php esc_html_e( 'File name cannot contain more than 195 symbols. The file name can include Latin letters, numbers and symbols.', 'pdf-print' ); ?></div>
							<div class="bws_info">
								<span><?php esc_html_e( 'Available shortcodes', 'pdf-print' ); ?>:</span><br />
								<span>
									<strong>{Publish_full_date}</strong> - <?php esc_html_e( 'The full date when the post was created', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{Post_title}</strong> - <?php esc_html_e( 'Title of the post.', 'pdf-print' ); ?>
								</span><br />
								<span>
									<strong>{Site_title}</strong> - <?php esc_html_e( 'Title of the site.', 'pdf-print' ); ?>
								</span><br />
								<span>
									<strong>{Author_display_name}</strong> - <?php esc_html_e( 'Author of the post.', 'pdf-print' ); ?>
								</span><br />
								<span>
									<strong>{Publish_year}</strong> - <?php esc_html_e( 'The year when the post was created.', 'pdf-print' ); ?>
								</span><br />
								<span>
									<strong>{Publish_month}</strong> - <?php esc_html_e( 'The month when the post was created.', 'pdf-print' ); ?>
								</span><br />
								<span>
									<strong>{Publish_date}</strong> - <?php esc_html_e( 'The day when the post was created.', 'pdf-print' ); ?>
								</span><br />
							</div>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Layout', 'pdf-print' ); ?></th>
				<td>
					<?php if ( $orientation_post_types ) { ?>
						<div class="pdfprnt-col">
							<p><br /></p>
							<br />
							<fieldset>
								<?php
								foreach ( $orientation_post_types as $value ) {
									echo '<label>' . esc_attr( $value->label ) . '</label><br />';
								}
								?>
							</fieldset>
						</div>
						<?php
						foreach ( array(
							'portrait'  => esc_html__( 'Portrait', 'pdf-print' ),
							'landscape' => esc_html__( 'Landscape', 'pdf-print' ),
						) as $orientation => $orientation_name ) {
							?>
							<div class="pdfprnt-col pdfprnt-col-center">
								<p><strong><?php echo esc_attr( $orientation_name ); ?></strong></p>
								<br>
								<fieldset>
									<?php
									foreach ( $orientation_post_types as $key => $value ) {
										if ( 'attachment' !== $key ) {
											?>
											<label><input disabled="disabled" type="radio" <?php checked( 'portrait', $orientation ); ?> /></label>
											<br>
											<?php
										}
									}
									?>
								</fieldset>
							</div>
						<?php } ?>
						<div class="clear"></div>
					<?php } ?>
				</td>
			</tr>
		</table>
		<?php
	}
}

/**
 * The content of ad block on the "Output" tab
 *
 * @param    void
 * @return  void
 */
if ( ! function_exists( 'pdfprnt_woocommerce_watermark_block' ) ) {
	function pdfprnt_woocommerce_watermark_block() {
		?>
		<table class="form-table pdfprnt-table-settings">
			<tr id="pdfprnt_remove_written" valign="top">
				<th><?php esc_html_e( '"Written by" Text', 'pdf-print' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="pdfprnt_remove_written" value="1" disabled="disabled" />
						<span class="bws_info"><?php esc_html_e( 'Enable to add "Written by" text before an author\'s name.', 'pdf-print' ); ?></span>
					</label>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'WooCommerce Product Details', 'pdf-print' ); ?></th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="checkbox" /> <?php esc_html_e( 'Short Description', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php esc_html_e( 'Price', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php esc_html_e( 'SKU', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php esc_html_e( 'Gallery', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php esc_html_e( 'Rating', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php esc_html_e( 'Stock', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php esc_html_e( 'Variations', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php esc_html_e( 'Categories', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php esc_html_e( 'Tags', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php esc_html_e( 'Additional information', 'pdf-print' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Watermark Protection', 'pdf-print' ); ?></th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="radio" /> <?php esc_html_e( 'None', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="radio" checked="checked" /> <?php esc_html_e( 'Text', 'pdf-print' ); ?></label><br>
						<div id="pdfprnt-watermark-text-wrap">
							<textarea disabled="disabled" maxlength="100" rows="3" cols="45"></textarea>
						</div>
						<label><input disabled="disabled" type="radio" /> <?php esc_html_e( 'Image', 'pdf-print' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Watermark Opacity', 'pdf-print' ); ?></th>
				<td>
					<fieldset>
						<input id="pdfprnt-watermark-opacity" disabled="disabled" type="number" />
						<div id="pdfprnt_watermark_opacity_wrap">
							<span id="pdfprnt_watermark_opacity_value"></span>
							<div id="pdfprnt_watermark_opacity_slider"></div>
						</div>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Prevent Copying', 'pdf-print' ); ?></th>
				<td>
					<label>
						<input type="checkbox" disabled="disabled" />
						<span class="bws_info"><?php esc_html_e( 'Enable to prevent PDF files from being copied.', 'pdf-print' ); ?></span>
					</label>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Show Advanced Custom Fields', 'pdf-print' ); ?></th>
				<td>
					<label>
						<input type="checkbox" disabled="disabled" />
						<span class="bws_info"><?php esc_html_e( 'Enable to show all Advanced Custom Fields.', 'pdf-print' ); ?></span>
					</label>
				</td>
			</tr>
		</table>
		<?php
	}
}

if ( ! function_exists( 'pdfprnt_fancytree_block' ) ) {
	function pdfprnt_fancytree_block() {
		?>
		<p><?php esc_html_e( 'Choose the necessary post types (or single pages) where PDF & Print buttons will be displayed', 'pdf-print' ); ?>:</p>
		<p class="bws_jstree_url_wrap">
			<label>
				<input disabled="disabled" type="checkbox" checked="checked" />
				<?php esc_html_e( 'Show URL', 'pdf-print' ); ?>
			</label>
		</p>
		<img src="<?php echo esc_url( plugins_url( 'images/pro_screen_1.png', dirname( __FILE__ ) ) ); ?>">
		<?php
	}
}

if ( ! function_exists( 'pdfprnt_custom_fields_block' ) ) {
	function pdfprnt_custom_fields_block() {
		?>
		<div class="pdfprnt-custom-fields-tab-title">
				<?php esc_html_e( 'Available Registered Post Types:', 'pdf-print' ); ?>
			</div>
			<div class="pdfprnt-custom-fields-tab-desc">
				<?php esc_html_e( "Add custom fields and custom data before/after PDF & Print document's content", 'pdf-print' ); ?>
			</div>
			<div class="pdfprnt-custom-accordion">
				<h3><?php esc_html_e( 'Posts', 'pdf-print' ); ?></h3>
				<div>
					<div>
						<div class="pdfprnt-before-after-title">
								<?php esc_html_e( 'Data Before Content', 'pdf-print' ); ?>
						</div>
						<div class="pdfprnt-content-before-title">
							<label><input type="checkbox" disabled="disabled"/> <?php esc_html_e( 'Enable to show data before page title', 'pdf-print' ); ?></label>.
							<br />
							<br />
						</div>
						<img class="pdfprnt_banner_accordion" src="<?php echo esc_url( plugins_url( 'images/pro_screen_2.png', dirname( __FILE__ ) ) ); ?>">
						<div class="pdfprnt-before-after-title">
								<?php esc_html_e( 'Data After Content', 'pdf-print' ); ?>
							</div>
						<img class="pdfprnt_banner_accordion" src="<?php echo esc_url( plugins_url( 'images/pro_screen_2.png', dirname( __FILE__ ) ) ); ?>">
						<table class="form-table">
							<tr>
								<th scope="row"><?php esc_html_e( 'Custom Fields Displaying', 'pdf-print' ); ?></th>
								<td>
									<fieldset>
										<label>
											<input disabled="disabled" type="radio" >&nbsp;<?php esc_html_e( 'Default', 'pdf-print' ); ?>
											<br />
											<span class="pdfprnt-custom-fields-commentary">
												<?php esc_html_e( 'Custom fields are shown by default. You can hide them using "Do not show PDF & Print Custom fields" option.', 'pdf-print' ); ?>
											</span>
										</label>
										<br>
										<label>
											<input disabled="disabled" type="radio">&nbsp;<?php esc_html_e( 'Force Hide', 'pdf-print' ); ?>
											<br />
											<span class="pdfprnt-custom-fields-commentary">
												<?php esc_html_e( 'Custom fields are hidden even if the "Show PDF & Print Custom Fields" option is enabled.', 'pdf-print' ); ?>
											</span>
										</label>
										<br>
										<label>
											<input disabled="disabled" type="radio" >&nbsp;<?php esc_html_e( 'Force Show', 'pdf-print' ); ?>
											<br />
											<span class="pdfprnt-custom-fields-commentary">
												<?php esc_html_e( 'Custom fields are visible even if the "Show PDF & Print Custom Fields" option is disabled.', 'pdf-print' ); ?>
											</span>
										</label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Disable the "Show PDF & Print Custom Fields" option', 'pdf-print' ); ?></th>
								<td>
									<button name="pdfprnt_clean_custom_fields_for_all" class="button">
										<?php esc_html_e( 'Disable Now', 'pdf-print' ); ?>
									</button>
									<div class="bws_info"><?php esc_html_e( 'Disable the "Show PDF & Print Custom Fields" option for all Posts.', 'pdf-print' ); ?></div>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<h3><?php esc_html_e( 'Pages', 'pdf-print' ); ?></h3>
				<h3><?php esc_html_e( 'Products', 'pdf-print' ); ?></h3>
			</div>
		<?php
	}
}

/**
 * The content of ad block on the Headers & Footers page
 *
 * @param    void
 * @return  void
 */
if ( ! function_exists( 'pdfprnt_headers_footers_list_block' ) ) {
	function pdfprnt_headers_footers_list_block( $date_format ) {
		global $wp_version;
		$old_wp_version = ( version_compare( $wp_version, '4.3', '<' ) );
		?>
		<ul class="subsubsub">
			<li class="all"><a class="current" href="#"><?php esc_html_e( 'All', 'pdf-print' ); ?><span class="count"> ( 3 )</span></a> |</li>
			<li class="trash"><a href="#"><?php esc_html_e( 'Trash', 'pdf-print' ); ?><span class="count"> ( 0 )</span></a></li>
		</ul>
		<p class="search-box">
			<label class="screen-reader-text" for="pdfprnt-search-input"><?php esc_html_e( 'search', 'pdf-print' ); ?>:</label>
			<input disabled="disabled" type="search" name="s" id="pdfprnt-search-input" />
			<input disabled="disabled" type="submit" id="search-submit" class="button" value="<?php esc_html_e( 'search', 'pdf-print' ); ?>" />
		</p>
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action" id="bulk-action-selector-top">
					<option value="-1"><?php esc_html_e( 'Bulk Actions', 'pdf-print' ); ?></option>
					<option value="trash"><?php esc_html_e( 'Trash', 'pdf-print' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction" class="button action" value="<?php esc_html_e( 'Apply', 'pdf-print' ); ?>" />
			</div>
			<div class="tablenav-pages one-page">
				<span class="displaying-num">3 <?php esc_html_e( 'items', 'pdf-print' ); ?></span>
			</div>
			<br class="clear">
		</div>
		<table class="wp-list-table widefat fixed striped headersfooters
		<?php
		if ( $old_wp_version ) {
			echo ' pdfprnt_old_wp';}
		?>
		">
			<thead>
				<tr>
					<?php printf( '<%s id="cb" class="manage-column column-cb check-column">', ( $old_wp_version ? 'th' : 'td' ) ); ?>
						<label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e( 'Select All', 'pdf-print' ); ?></label>
						<input disabled="disabled" id="cb-select-all-1" type="checkbox" />
					<?php printf( '</%s>', ( $old_wp_version ? 'th' : 'td' ) ); ?>
					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#"><span><?php esc_html_e( 'Title', 'pdf-print' ); ?></span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="pdf" class="manage-column column-pdf">PDF</th>
					<th scope="col" id="print" class="manage-column column-print">Print</th>
					<th scope="col" id="date" class="manage-column column-date sortable desc">
						<a href="#">
							<span><?php esc_html_e( 'Date Added', 'pdf-print' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>
			<tbody id="the-list" data-wp-lists="list:headerfooter">
				<tr>
					<th scope="row" class="check-column">
						<input disabled="disabled" id="cb_19" type="checkbox" />
					</th>
					<td class="title column-title has-row-actions column-primary" data-colname="Title">
						<strong><a href="#">Template for PDF</a></strong>
						<div class="row-actions">
							<span class="edit"><a href="#"><?php esc_html_e( 'Edit', 'pdf-print' ); ?></a> | </span>
							<span class="trash"><a class="submitdelete" href="#"><?php esc_html_e( 'Trash', 'pdf-print' ); ?></a></span>
						</div>
						<?php if ( ! $old_wp_version ) { ?>
							<button type="button" class="toggle-row"></button>
							<button type="button" class="toggle-row"></button>
						<?php } ?>
					</td>
					<td class="pdf column-pdf" data-colname="PDF">
						<input disabled="disabled" type="radio" />
					</td>
					<td class="print column-print" data-colname="Print">
						<input disabled="disabled" type="radio" />
					</td>
					<td class="date column-date" data-colname="<?php esc_html_e( 'Date Added', 'pdf-print' ); ?>"><?php echo esc_html( date_i18n( $date_format, strtotime( 'May 3, 2017' ) ) ); ?></td>
				</tr>
				<tr>
					<th scope="row" class="check-column">
						<input disabled="disabled" id="cb_18" type="checkbox" />
					</th>
					<td class="title column-title has-row-actions column-primary" data-colname="Title">
						<strong><a href="#">New template</a></strong>
						<div class="row-actions">
							<span class="edit"><a href="#"><?php esc_html_e( 'Edit', 'pdf-print' ); ?></a> | </span>
							<span class="trash"><a class="submitdelete" href="#"><?php esc_html_e( 'Trash', 'pdf-print' ); ?></a></span>
						</div>
						<?php if ( ! $old_wp_version ) { ?>
							<button type="button" class="toggle-row"></button>
							<button type="button" class="toggle-row"></button>
						<?php } ?>
					</td>
					<td class="pdf column-pdf" data-colname="PDF">
						<input disabled="disabled" type="radio" />
					</td>
					<td class="print column-print" data-colname="Print">
						<input disabled="disabled" type="radio" />
					</td>
					<td class="date column-date" data-colname="<?php esc_html_e( 'Date Added', 'pdf-print' ); ?>"><?php echo esc_html( date_i18n( $date_format, strtotime( 'April 25, 2017' ) ) ); ?></td>
				</tr>
				<tr>
					<th scope="row" class="check-column">
						<input disabled="disabled" id="cb_1" type="checkbox" />
					</th>
					<td class="title column-title has-row-actions column-primary" data-colname="Title">
						<strong><a href="#">Custom template</a></strong>
						<div class="row-actions">
							<span class="edit"><a href="#"><?php esc_html_e( 'Edit', 'pdf-print' ); ?></a> | </span>
							<span class="trash"><a class="submitdelete" href="#"><?php esc_html_e( 'Trash', 'pdf-print' ); ?></a></span>
						</div>
						<?php if ( ! $old_wp_version ) { ?>
							<button type="button" class="toggle-row"></button>
							<button type="button" class="toggle-row"></button>
						<?php } ?>
					</td>
					<td class="pdf column-pdf" data-colname="PDF">
						<input disabled="disabled" type="radio" />
					</td>
					<td class="print column-print" data-colname="Print">
						<input disabled="disabled" type="radio" />
					</td>
					<td class="date column-date" data-colname="<?php esc_html_e( 'Date Added', 'pdf-print' ); ?>"><?php echo esc_html( date_i18n( $date_format, strtotime( 'April 25, 2017' ) ) ); ?></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<?php printf( '<%s class="manage-column column-cb check-column">', ( $old_wp_version ? 'th' : 'td' ) ); ?>
						<input disabled="disabled" id="cb-select-all-2" type="checkbox" />
					<?php printf( '</%s>', ( $old_wp_version ? 'th' : 'td' ) ); ?>
					<th scope="col" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span><?php esc_html_e( 'Title', 'pdf-print' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" class="manage-column column-pdf">PDF</th>
					<th scope="col" class="manage-column column-print">Print</th>
					<th scope="col" class="manage-column column-date sortable desc">
						<a href="#">
							<span><?php esc_html_e( 'Date Added', 'pdf-print' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
		</table>
		<div class="tablenav bottom">
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action2" id="bulk-action-selector-bottom">
					<option value="-1"><?php esc_html_e( 'Bulk Actions', 'pdf-print' ); ?></option>
					<option value="trash"><?php esc_html_e( 'Trash', 'pdf-print' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction2" class="button action" value="<?php esc_html_e( 'Apply', 'pdf-print' ); ?>" />
			</div>
			<div class="tablenav-pages one-page">
				<span class="displaying-num">3 <?php esc_html_e( 'items', 'pdf-print' ); ?></span>
			</div>
			<br class="clear">
		</div>
		<?php
	}
}

/**
 * The content of ad block on the Headers & Footers page
 *
 * @param    void
 * @return  void
 */
if ( ! function_exists( 'pdfprnt_headers_footers_editor_block' ) ) {
	function pdfprnt_headers_footers_editor_block() {
		?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" style="position: relative;">
					<div id="pdfprnt_template_page">
						<div id="titlediv">
							<div id="titlewrap">
								<input disabled="disabled" type="text" disabled="disabled" id="title" placeholder="<?php esc_html_e( 'Enter Title', 'pdf-print' ); ?>" />
							</div>
						</div>
						<div class="pdfprnt-template-editor">
							<h3 class="pdfprnt-template-editor-title"><span><?php esc_html_e( 'Header', 'pdf-print' ); ?></span></h3>
							<div class="clear"></div>
							<div class="postarea wp-editor-expand">
								<?php
								if ( function_exists( 'wp_editor' ) ) {
									$settings = array(
										'wpautop'       => 1,
										'media_buttons' => 1,
										'textarea_name' => 'pdfprnt_top',
										'textarea_rows' => 5,
										'tabindex'      => null,
										'editor_css'    => '<style>.mce-content-body { width: 100%; max-width: 100%; background: red;}</style>',
										'editor_class'  => 'pdfprnt_top',
										'teeny'         => 0,
										'dfw'           => 0,
										'tinymce'       => 1,
										'quicktags'     => 1,
									);
									wp_editor( '', 'pdfprnt_top', $settings );
								} else {
									?>
									<textarea disabled="disabled" class="pdfprnt_top_area" rows="5" autocomplete="off" cols="40" name="pdfprnt_top" id="pdfprnt_top"></textarea>
								<?php } ?>
							</div>
							<div class="pdfprnt-template-editor-shortcodes">
								<span><?php esc_html_e( 'Available shortcodes', 'pdf-print' ); ?>:</span><br />
								<span>
									<strong>{PAGENO}</strong> - <?php esc_html_e( 'Current page number (for PDF only)', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{TITLE}</strong> - <?php esc_html_e( 'Title of the post (for single posts or pages only)', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{PAGETOTAL}</strong> - <?php esc_html_e( 'Number of all pages in the document (for PDF only)', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{PAGEURL}</strong> - <?php esc_html_e( 'Link to the page where the document was generated', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{DATE}</strong> - <?php esc_html_e( 'Current date', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{POSTDATE}</strong> - <?php esc_html_e( 'The date when the post was created', 'pdf-print' ); ?>&nbsp;(<?php esc_html_e( 'this shortcode will be replaced to the current date on search or archive pages', 'pdf-print' ); ?>).
								</span>
								<span>
									<?php esc_html_e( 'You can specify your own DateTime format for {DATE} and {POSTDATE} shortcodes. For example', 'pdf-print' ); ?>:
								</span>
								<code>{DATE l, F j, Y g:i a}</code>&nbsp;<?php esc_html_e( 'or', 'pdf-print' ); ?>&nbsp;<code>{POSTDATE l, F j, Y g:i a}</code>
								<a target="_blank" href="https://codex.wordpress.org/Formatting_Date_and_Time"><?php esc_html_e( 'Learn More', 'pdf-print' ); ?></a><br />
								<span>
									<strong>{POSTAUTHOR}</strong> - <?php echo esc_html__( 'Author of the post', 'pdf-print' ) . '&nbsp;(' . esc_html__( 'for single posts or pages only', 'pdf-print' ); ?>).
								</span>
							</div><!-- .pdfprnt-template-editor-shortcodes -->
						</div>
						<div class="pdfprnt-template-editor">
							<h3 class="pdfprnt-template-editor-title"><span><?php esc_html_e( 'Footer', 'pdf-print' ); ?></span></h3>
							<div class="clear"></div>
							<div class="postarea wp-editor-expand">
								<?php
								if ( function_exists( 'wp_editor' ) ) {
									$settings = array(
										'wpautop'       => 1,
										'media_buttons' => 1,
										'textarea_name' => 'pdfprnt_bottom',
										'textarea_rows' => 5,
										'tabindex'      => null,
										'editor_css'    => '',
										'editor_class'  => 'pdfprnt_bottom',
										'teeny'         => 0,
										'dfw'           => 0,
										'tinymce'       => 1,
										'quicktags'     => 1,
									);
									wp_editor( '', 'pdfprnt_bottom', $settings );
								} else {
									?>
									<textarea disabled="disabled" class="pdfprnt_bottom_area" rows="5" autocomplete="off" cols="40" name="pdfprnt_bottom" id="pdfprnt_bottom"></textarea>
								<?php } ?>
							</div>
							<div class="pdfprnt-template-editor-shortcodes">
								<span><?php esc_html_e( 'Available shortcodes', 'pdf-print' ); ?>:</span><br />
								<span>
									<strong>{PAGENO}</strong> - <?php esc_html_e( 'Current page number (for PDF only)', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{TITLE}</strong> - <?php esc_html_e( 'Title of the post (for single posts or pages only)', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{PAGETOTAL}</strong> - <?php esc_html_e( 'Number of all pages in the document (for PDF only)', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{PAGEURL}</strong> - <?php esc_html_e( 'Link to the page where the document was generated', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{DATE}</strong> - <?php esc_html_e( 'Current date', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{POSTDATE}</strong> - <?php esc_html_e( 'The date when the post was created', 'pdf-print' ); ?>&nbsp;(<?php esc_html_e( 'this shortcode will be replaced to the current date on search or archive pages', 'pdf-print' ); ?>).
								</span>
								<span>
									<?php esc_html_e( 'You can specify your own DateTime format for {DATE} and {POSTDATE} shortcodes. For example', 'pdf-print' ); ?>:
								</span>
								<code>{DATE l, F j, Y g:i a}</code>&nbsp;<?php esc_html_e( 'or', 'pdf-print' ); ?>&nbsp;<code>{POSTDATE l, F j, Y g:i a}</code>
								<a target="_blank" href="https://codex.wordpress.org/Formatting_Date_and_Time"><?php esc_html_e( 'Learn More', 'pdf-print' ); ?></a><br />
								<span>
									<strong>{POSTAUTHOR}</strong> - <?php echo esc_html__( 'Author of the post', 'pdf-print' ) . '&nbsp;(' . esc_html__( 'for single posts or pages only', 'pdf-print' ); ?>).
								</span>
							</div><!-- .pdfprnt-template-editor-shortcodes -->
						</div>
					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables">
						<div id="submitdiv" class="postbox ">
							<h3 class="hndle" style="cursor: default !important;">
								<span><?php esc_html_e( 'Publish', 'pdf-print' ); ?></span>
							</h3>
							<div class="inside">
								<div class="submitbox" id="submitpost">
									<div id="minor-publishing">
										<div id="misc-publishing-actions">
											<div class="misc-pub-section misc-pub-post-status">
												<label for="post_status"><?php esc_html_e( 'Status', 'pdf-print' ); ?>:</label> <span id="post-status-display"><?php esc_html_e( 'New', 'pdf-print' ); ?></span>
											</div>
											<div class="misc-pub-section curtime misc-pub-curtime">
												<span id="timestamp">
													<?php printf( ' %s <b>%s</b>', esc_html__( 'Publish', 'pdf-print' ), esc_html__( 'immediately', 'pdf-print' ) ); ?>
												</span>
											</div>
										</div>
									</div>
									<div id="major-publishing-actions">
										<div id="delete-action"></div>
										<div id="publishing-action">
											<input disabled="disabled" name="pdfprnt_template_submit" type="submit" class="button-primary" value="<?php esc_html_e( 'Save Changes', 'pdf-print' ); ?>" />
										</div>
										<div class="clear"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}
}
