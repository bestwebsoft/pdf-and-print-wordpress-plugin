<?php
/**
 * Banners on plugin settings page
 * @package PDF & Print by BestWebSoft
 * @since 1.8.5
 */

/**
 * Wrapper. Show ads for PRO on plugin settings page
 * @param     string     $func        function to call
 * @param     boolean    $show_cross  when it is 'false' ad will be displayed regardless of if other blocks are closed
 * @return    void
 */
if ( ! function_exists( 'pdfprnt_pro_block' ) ) {
	function pdfprnt_pro_block( $func, $show_cross = true ) {
		global $pdfprnt_plugin_info, $wp_version, $pdfprnt_options;
		if ( ! bws_hide_premium_options_check( $pdfprnt_options ) || ! $show_cross ) { ?>
			<div class="bws_pro_version_bloc pdfprnt_pro_block <?php echo $func;?>" title="<?php _e( 'This options is available in Pro version of the plugin', 'pdf-print' ); ?>">
				<div class="bws_pro_version_table_bloc">
					<?php if ( $show_cross ) { ?>
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'pdf-print' ); ?>"></button>
					<?php } ?>
					<div class="bws_table_bg"></div>
					<?php call_user_func( $func ); ?>
				</div>
				<div class="bws_pro_version_tooltip">
					<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/pdf-print/?k=d9da7c9c2046bed8dfa38d005d4bffdb&pn=101&v=<?php echo $pdfprnt_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="PDF & Print Pro Plugin"><?php _e( 'Upgrade to Pro', 'pdf-print' ); ?></a>
				</div>
			</div>
		<?php }
	}
}

/**
 * The content of ad block on the "Settings" tab
 * @param     void
 * @return    void
 */
if ( ! function_exists( 'pdfprnt_button_image_block' ) ) {
	function pdfprnt_button_image_block( $buttons ) { ?>
		<fieldset>
			<?php foreach ( $buttons as $button ) { ?>
				<div class="pdfprnt-col pdfprnt-col-2">
					<label><input disabled="disabled" type="radio" /><?php _e( 'Custom', 'pdf-print' ); ?></label><br />
					<input disabled="disabled" type="file" />
				</div>
			<?php } ?>
			<div class="clear"></div>
		</fieldset>
	<?php }
}

/**
 * The content of ad block on the "Settings" tab
 * @param     void
 * @return    void
 */
if ( ! function_exists( 'pdfprnt_noindex_block' ) ) {
	function pdfprnt_noindex_block() { ?>
		<table class="form-table pdfprnt-table-settings pdfprnt-table-search-engine">
			<tr>
				<th><?php _e( 'Search Engine Visibility', 'pdf-print' ); ?></th>
				<td>
					<input type="checkbox" disabled="disabled" />
					<span class="bws_info"><?php _e( 'Enable to disallow search engines from indexing PDF & Print pages.', 'pdf-print' ); ?></span>
				</td>
			</tr>
		</table>
	<?php }
}

/**
 * The content of ad block on the "Output" tab
 * @param     void
 * @return    void
 */
if ( ! function_exists( 'pdfprnt_filename_orientation_block' ) ) {
	function pdfprnt_filename_orientation_block( $orientation_post_types = false ) { ?>
		<table class="form-table pdfprnt-table-settings">
			<tr>
				<th><?php _e( 'Default PDF File Name', 'pdf-print' ); ?></th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="radio" checked="checked" /> <?php _e( 'Post/page slug', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="radio" /> <?php _e( 'Custom', 'pdf-print' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Layout', 'pdf-print' ); ?></th>
				<td>
					<?php if ( $orientation_post_types ) {
						foreach ( array( 'portrait' => __( 'Portrait', 'pdf-print' ), 'landscape' => __( 'Landscape', 'pdf-print' ) ) as $orientation => $orientation_name ) { ?>
							<div class="pdfprnt-col pdfprnt-col-2">
								<p><strong><?php echo $orientation_name; ?></strong></p>
								<br>
								<fieldset>
									<?php foreach ( $orientation_post_types as $key => $value ) {
										if ( 'attachment' != $key ) { ?>
											<label><input disabled="disabled" type="radio" <?php checked( 'portrait', $orientation ); ?> /> <?php echo $value->label; ?></label>
											<br>
										<?php }
									} ?>
								</fieldset>
							</div>
						<?php } ?>
						<div class="clear"></div>
					<?php } ?>
				</td>
			</tr>
		</table>
	<?php }
}

/**
 * The content of ad block on the "Output" tab
 * @param     void
 * @return    void
 */
if ( ! function_exists( 'pdfprnt_woocommerce_watermark_block' ) ) {
	function pdfprnt_woocommerce_watermark_block() { ?>
		<table class="form-table pdfprnt-table-settings">
			<tr>
				<th><?php _e( 'WooCommerce Product Details', 'pdf-print' ); ?></th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="checkbox" /> <?php _e( 'Short Description', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php _e( 'Price', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php _e( 'SKU', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php _e( 'Gallery', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php _e( 'Rating', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php _e( 'Stock', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php _e( 'Variations', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php _e( 'Categories', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php _e( 'Tags', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" /> <?php _e( 'Additional information', 'pdf-print' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Watermark Protection', 'pdf-print' ); ?></th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="radio" /> <?php _e( 'None', 'pdf-print' ); ?></label><br>
						<label><input disabled="disabled" type="radio" checked="checked" /> <?php _e( 'Text', 'pdf-print' ); ?></label><br>
						<div id="pdfprnt-watermark-text-wrap">
							<textarea maxlength="100" rows="3" cols="45"></textarea>
						</div>
						<label><input disabled="disabled" type="radio" /> <?php _e( 'Image', 'pdf-print' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Watermark Opacity', 'pdf-print' ); ?></th>
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
		</table>
	<?php }
}

if ( ! function_exists( 'pdfprnt_fancytree_block' ) ) {
	function pdfprnt_fancytree_block() { ?>
		<p><?php _e( 'Please choose the necessary post types (or single pages) where PDF & Print buttons will be displayed', 'pdf-print' ); ?>:</p>
		<p class="bws_jstree_url_wrap">
			<label>
				<input disabled="disabled" type="checkbox" checked="checked" />
				<?php _e( "Show URL", 'pdf-print' ); ?>
			</label>
		</p>
		<img src="<?php echo plugins_url( "images/pro_screen_1.png", dirname( __FILE__ ) ); ?>">
	<?php }
}

if ( ! function_exists( 'pdfprnt_custom_fields_block' ) ) {
	function pdfprnt_custom_fields_block() { ?>
		<div class="pdfprnt-custom-fields-tab-title">
				<?php _e( 'Available Registered Post Types:', 'pdf-print' );?>
			</div>
			<div class="pdfprnt-custom-fields-tab-desc">
				<?php _e( "Add custom fields and custom data before/after PDF & Print document's content", 'pdf-print' );?>
			</div>
			<div class="pdfprnt-custom-accordion">
				<h3><?php _e( 'Posts', 'pdf-print' );?></h3>
				<div>
					<div>
						<div class="pdfprnt-before-after-title">
								<?php _e( 'Data Before Content', 'pdf-print' );?>
							</div>
						<img class="pdfprnt_banner_accordion" src="<?php echo plugins_url( "images/pro_screen_2.png", dirname( __FILE__ ) ); ?>">
						<div class="pdfprnt-before-after-title">
								<?php _e( 'Data After Content', 'pdf-print' );?>
							</div>
						<img class="pdfprnt_banner_accordion" src="<?php echo plugins_url( "images/pro_screen_2.png", dirname( __FILE__ ) ); ?>">
						<table class="form-table">
							<tr>
								<th scope="row"><?php _e( 'Custom Fields Displaying', 'pdf-print' ) ?></th>
								<td>
									<fieldset>
										<label>
											<input disabled="disabled" type="radio" >&nbsp;<?php _e( 'Default', 'pdf-print' ); ?>
												<span class="pdfprnt-custom-fields-commentary">
													(<?php _e( 'custom fields are shown by default; you can hide them using "Do not show PDF & Print Custom fields" option', 'pdf-print' ); ?>)
												</span>
										</label>
										<br>
										<label>
											<input disabled="disabled" type="radio">&nbsp;<?php _e( 'Force Hide', 'pdf-print' ); ?>
												<span class="pdfprnt-custom-fields-commentary">
													(<?php _e( 'custom fields are hidden regardless the "Do not show PDF & Print Custom fields" option', 'pdf-print' ); ?>)
												</span>
										</label>
										<br>
										<label>
											<input disabled="disabled" type="radio" >&nbsp;<?php _e( 'Force Show', 'pdf-print' ); ?>
												<span class="pdfprnt-custom-fields-commentary">
													(<?php _e( 'custom fields are shown regardless the "Do not show PDF & Print Custom fields" option', 'pdf-print' ); ?>)
												</span>
										</label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Clear all "Do not show PDF & Print Custom fields" options', 'pdf-print' );?></th>
								<td>
									<button name="pdfprnt_clean_custom_fields_for_all" class="button">
										<?php _e( 'Clear Options', 'pdf-print' ); ?>
									</button>
									<div class="bws_info"><?php _e( 'This will clear all "Do not show PDF & Print Custom Fields" options for all Posts.' , 'pdf-print' );?></div>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<h3><?php _e( 'Pages', 'pdf-print' );?></h3>
				<h3><?php _e( 'Products', 'pdf-print' );?></h3>
			</div>
	<?php }
}

/**
 * The content of ad block on the Headers & Footers page
 * @param     void
 * @return    void
 */
if ( ! function_exists( 'pdfprnt_headers_footers_list_block' ) ) {
	function pdfprnt_headers_footers_list_block( $date_format ) {
		global $wp_version;
		$old_wp_version = ( version_compare( $wp_version, '4.3', '<' ) ); ?>
		<ul class="subsubsub">
			<li class="all"><a class="current" href="#"><?php _e( 'All', 'pdf-print' ); ?><span class="count"> ( 3 )</span></a> |</li>
			<li class="trash"><a href="#"><?php _e( 'Trash', 'pdf-print' ); ?><span class="count"> ( 0 )</span></a></li>
		</ul>
		<p class="search-box">
			<label class="screen-reader-text" for="pdfprnt-search-input"><?php _e( 'search', 'pdf-print' ); ?>:</label>
			<input disabled="disabled" type="search" name="s" id="pdfprnt-search-input" />
			<input disabled="disabled" type="submit" id="search-submit" class="button" value="search" />
		</p>
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action" id="bulk-action-selector-top">
					<option value="-1"><?php _e( 'Bulk Actions', 'pdf-print' ); ?></option>
					<option value="trash"><?php _e( 'Trash', 'pdf-print' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction" class="button action" value="Apply" />
			</div>
			<div class="tablenav-pages one-page">
				<span class="displaying-num">3 <?php _e( 'items', 'pdf-print' ); ?></span>
			</div>
			<br class="clear">
		</div>
		<table class="wp-list-table widefat fixed striped headersfooters<?php if ( $old_wp_version ) echo ' pdfprnt_old_wp'; ?>">
			<thead>
				<tr>
					<?php printf( '<%s id="cb" class="manage-column column-cb check-column">', ( $old_wp_version ? 'th' : 'td' ) ); ?>
						<label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'pdf-print' ); ?></label>
						<input disabled="disabled" id="cb-select-all-1" type="checkbox" />
					<?php printf( '</%s>', ( $old_wp_version ? 'th': 'td' ) ); ?>
					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#"><span><?php _e( 'Title', 'pdf-print' ); ?></span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="pdf" class="manage-column column-pdf">PDF</th>
					<th scope="col" id="print" class="manage-column column-print">Print</th>
					<th scope="col" id="date" class="manage-column column-date sortable desc">
						<a href="#">
							<span><?php _e( 'Date Added', 'pdf-print' ); ?></span>
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
							<span class="edit"><a href="#"><?php _e( 'Edit', 'pdf-print' ); ?></a> | </span>
							<span class="trash"><a class="submitdelete" href="#"><?php _e( 'Trash', 'pdf-print' ); ?></a></span>
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
					<td class="date column-date" data-colname="<?php _e( 'Date Added', 'pdf-print' ); ?>"><?php echo date_i18n( $date_format, strtotime( 'May 3, 2017' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row" class="check-column">
						<input disabled="disabled" id="cb_18" type="checkbox" />
					</th>
					<td class="title column-title has-row-actions column-primary" data-colname="Title">
						<strong><a href="#">New template</a></strong>
						<div class="row-actions">
							<span class="edit"><a href="#"><?php _e( 'Edit', 'pdf-print' ); ?></a> | </span>
							<span class="trash"><a class="submitdelete" href="#"><?php _e( 'Trash', 'pdf-print' ); ?></a></span>
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
					<td class="date column-date" data-colname="<?php _e( 'Date Added', 'pdf-print' ); ?>"><?php echo date_i18n( $date_format, strtotime( 'April 25, 2017' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row" class="check-column">
						<input disabled="disabled" id="cb_1" type="checkbox" />
					</th>
					<td class="title column-title has-row-actions column-primary" data-colname="Title">
						<strong><a href="#">Custom template</a></strong>
						<div class="row-actions">
							<span class="edit"><a href="#"><?php _e( 'Edit', 'pdf-print' ); ?></a> | </span>
							<span class="trash"><a class="submitdelete" href="#"><?php _e( 'Trash', 'pdf-print' ); ?></a></span>
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
					<td class="date column-date" data-colname="<?php _e( 'Date Added', 'pdf-print' ); ?>"><?php echo date_i18n( $date_format, strtotime( 'April 25, 2017' ) ); ?></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<?php printf( '<%s class="manage-column column-cb check-column">', ( $old_wp_version ? 'th' : 'td' ) ); ?>
						<input disabled="disabled" id="cb-select-all-2" type="checkbox" />
					<?php printf( '</%s>', ( $old_wp_version ? 'th': 'td' ) ); ?>
					<th scope="col" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span><?php _e( 'Title', 'pdf-print' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" class="manage-column column-pdf">PDF</th>
					<th scope="col" class="manage-column column-print">Print</th>
					<th scope="col" class="manage-column column-date sortable desc">
						<a href="#">
							<span><?php _e( 'Date Added', 'pdf-print' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
		</table>
		<div class="tablenav bottom">
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action2" id="bulk-action-selector-bottom">
					<option value="-1"><?php _e( 'Bulk Actions', 'pdf-print' ); ?></option>
					<option value="trash"><?php _e( 'Trash', 'pdf-print' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction2" class="button action" value="Apply" />
			</div>
			<div class="tablenav-pages one-page">
				<span class="displaying-num">3 <?php _e( 'items', 'pdf-print' ); ?></span>
			</div>
			<br class="clear">
		</div>
	<?php }
}

/**
 * The content of ad block on the Headers & Footers page
 * @param     void
 * @return    void
 */
if ( ! function_exists( 'pdfprnt_headers_footers_editor_block' ) ) {
	function pdfprnt_headers_footers_editor_block() { ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" style="position: relative;">
					<div id="pdfprnt_template_page">
						<div id="titlediv">
							<div id="titlewrap">
								<input disabled="disabled" type="text" disabled="disabled" id="title" placeholder="<?php _e( 'Enter Title', 'pdf-print' ); ?>" />
							</div>
						</div>
						<div class="pdfprnt-template-editor">
							<h3 class="pdfprnt-template-editor-title"><span><?php _e( 'Header', 'pdf-print' ); ?></span></h3>
							<div class="clear"></div>
							<div class="postarea wp-editor-expand">
								<?php if ( function_exists( 'wp_editor' ) ) {
									$settings = array(
											'wpautop'		=> 1,
											'media_buttons'	=> 1,
											'textarea_name'	=> 'pdfprnt_top',
											'textarea_rows'	=> 5,
											'tabindex'		=> null,
											'editor_css'	=> '<style>.mce-content-body { width: 100%; max-width: 100%; background: red;}</style>',
											'editor_class'	=> 'pdfprnt_top',
											'teeny'			=> 0,
											'dfw'			=> 0,
											'tinymce'		=> 1,
											'quicktags'		=> 1
										);
									wp_editor( '', 'pdfprnt_top', $settings );
								} else { ?>
									<textarea disabled="disabled" class="pdfprnt_top_area" rows="5" autocomplete="off" cols="40" name="pdfprnt_top" id="pdfprnt_top"></textarea>
								<?php }?>
							</div>
							<div class="bws_info pdfprnt-template-editor-shortcodes">
								<span><?php _e( 'Available shortcodes', 'pdf-print' ); ?>:</span><br />
								<span>
									<strong>{PAGENO}</strong> - <?php _e( 'Current page number (for PDF only)', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{PAGETOTAL}</strong> - <?php _e( 'Number of all pages in the document (for PDF only)', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{PAGEURL}</strong> - <?php _e( 'Link to the page where the document was generated', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{DATE}</strong> - <?php _e( 'Current date', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{POSTDATE}</strong> - <?php _e( 'The date when the post was created', 'pdf-print' ); ?>&nbsp;(&nbsp;<?php _e( 'this shortcode will be replaced to the current date on search or archive pages', 'pdf-print' ); ?>&nbsp;).
								</span>
								<span>
									<?php _e( 'You can specify your own DateTime format for {DATE} and {POSTDATE} shortcodes. For example', 'pdf-print' ); ?>:
								</span>
								<code>{DATE l, F j, Y g:i a}</code>&nbsp;<?php _e( 'or', 'pdf-print' ); ?>&nbsp;<code>{POSTDATE l, F j, Y g:i a}</code>
								<a target="_blank" href="https://codex.wordpress.org/Formatting_Date_and_Time"><?php _e( 'Read more about date and time formatting', 'pdf-print' ); ?></a><br />
								<span>
									<strong>{POSTAUTHOR}</strong> - <?php echo __( 'Author of the post', 'pdf-print' ) . '&nbsp;(&nbsp;' . __( 'for single posts or pages only', 'pdf-print' ); ?>&nbsp;).
								</span>
							</div><!-- .pdfprnt-template-editor-shortcodes -->
						</div>
						<div class="pdfprnt-template-editor">
							<h3 class="pdfprnt-template-editor-title"><span><?php _e( 'Footer', 'pdf-print' ); ?></span></h3>
							<div class="clear"></div>
							<div class="postarea wp-editor-expand">
								<?php if ( function_exists( 'wp_editor' ) ) {
									$settings = array(
										'wpautop'		=> 1,
										'media_buttons'	=> 1,
										'textarea_name'	=> 'pdfprnt_bottom',
										'textarea_rows'	=> 5,
										'tabindex'		=> null,
										'editor_css'	=> '',
										'editor_class'	=> 'pdfprnt_bottom',
										'teeny'			=> 0,
										'dfw'			=> 0,
										'tinymce'		=> 1,
										'quicktags'		=> 1
									);
									wp_editor( '', 'pdfprnt_bottom', $settings );
								} else { ?>
									<textarea disabled="disabled" class="pdfprnt_bottom_area" rows="5" autocomplete="off" cols="40" name="pdfprnt_bottom" id="pdfprnt_bottom"></textarea>
								<?php } ?>
							</div>
							<div class="bws_info pdfprnt-template-editor-shortcodes">
								<span><?php _e( 'Available shortcodes', 'pdf-print' ); ?>:</span><br />
								<span>
									<strong>{PAGENO}</strong> - <?php _e( 'Current page number (for PDF only)', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{PAGETOTAL}</strong> - <?php _e( 'Number of all pages in the document (for PDF only)', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{PAGEURL}</strong> - <?php _e( 'Link to the page where the document was generated', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{DATE}</strong> - <?php _e( 'Current date', 'pdf-print' ); ?>.
								</span><br />
								<span>
									<strong>{POSTDATE}</strong> - <?php _e( 'The date when the post was created', 'pdf-print' ); ?>&nbsp;(&nbsp;<?php _e( 'this shortcode will be replaced to the current date on search or archive pages', 'pdf-print' ); ?>&nbsp;).
								</span>
								<span>
									<?php _e( 'You can specify your own DateTime format for {DATE} and {POSTDATE} shortcodes. For example', 'pdf-print' ); ?>:
								</span>
								<code>{DATE l, F j, Y g:i a}</code>&nbsp;<?php _e( 'or', 'pdf-print' ); ?>&nbsp;<code>{POSTDATE l, F j, Y g:i a}</code>
								<a target="_blank" href="https://codex.wordpress.org/Formatting_Date_and_Time"><?php _e( 'Read more about date and time formatting', 'pdf-print' ); ?></a><br />
								<span>
									<strong>{POSTAUTHOR}</strong> - <?php echo __( 'Author of the post', 'pdf-print' ) . '&nbsp;(&nbsp;' . __( 'for single posts or pages only', 'pdf-print' ); ?>&nbsp;).
								</span>
							</div><!-- .pdfprnt-template-editor-shortcodes -->
						</div>
					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables">
						<div id="submitdiv" class="postbox ">
							<h3 class="hndle" style="cursor: default !important;">
								<span><?php _e( 'Publish', 'pdf-print' ); ?></span>
							</h3>
							<div class="inside">
								<div class="submitbox" id="submitpost">
									<div id="minor-publishing">
										<div id="misc-publishing-actions">
											<div class="misc-pub-section misc-pub-post-status">
												<label for="post_status"><?php _e( 'Status', 'pdf-print' ); ?>:</label> <span id="post-status-display"><?php _e( 'New', 'pdf-print' ); ?></span>
											</div>
											<div class="misc-pub-section curtime misc-pub-curtime">
												<span id="timestamp">
													<?php printf( ' %s <b>%s</b>',__( 'Publish', 'pdf-print' ), __( 'immediately', 'pdf-print' ) ); ?>
												</span>
											</div>
										</div>
									</div>
									<div id="major-publishing-actions">
										<div id="delete-action"></div>
										<div id="publishing-action">
											<input disabled="disabled" name="pdfprnt_template_submit" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'pdf-print' ); ?>" />
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
	<?php }
}