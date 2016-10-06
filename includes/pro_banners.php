<?php
/**
 * Display banners on settings page
 * @package PDF & Print
 * @since 1.8.5
 */

/**
 * Show ads for PRO
 * @param     string     $func        function to call
 * @return    void
 */
if ( ! function_exists( 'pdfprnt_pro_block' ) ) {
	function pdfprnt_pro_block( $func, $show_cross = true ) {
		global $pdfprnt_plugin_info, $wp_version, $pdfprnt_options;
		if ( ! bws_hide_premium_options_check( $pdfprnt_options ) || ! $show_cross ) { ?>
			<div class="bws_pro_version_bloc pdfprnt_pro_block <?php echo $func;?>" title="<?php _e( 'This options is available in Pro version of plugin', 'pdf-print' ); ?>">
				<div class="bws_pro_version_table_bloc">
					<?php if ( $show_cross ) { ?>
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'pdf-print' ); ?>"></button>
					<?php } ?>
					<div class="bws_table_bg"></div>
					<?php call_user_func( $func ); ?>
				</div>
				<div class="bws_pro_version_tooltip">
					<div class="bws_info"><?php _e( 'Unlock premium options by upgrading to Pro version', 'pdf-print' ); ?></div>
					<a class="bws_button" href="http://bestwebsoft.com/products/wordpress/plugins/pdf-print/?k=d9da7c9c2046bed8dfa38d005d4bffdb&pn=101&v=<?php echo $pdfprnt_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="PDF & Print Pro Plugin"><?php _e( 'Learn More', 'pdf-print' ); ?></a>
				</div>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'pdfprnt_layout_block' ) ) {
	function pdfprnt_layout_block() { ?>
		<table class="form-table bws_pro_version">
			<tr class="pdfprnt_table_head">
				<th scope="row"></th>
				<th><?php _e( 'Posts and pages', 'pdf-print' ); ?></th>
				<th><?php _e( 'Search and archive pages', 'pdf-print' ); ?></th>
			</tr>
			<tr class="pdfprnt_layout">
				<th scope="row"><?php _e( 'Layout', 'pdf-print' ); ?></th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="radio" value="portrait" checked="checked" />&nbsp;<?php _e( "Portrait", 'pdf-print' ); ?></label><br/>
						<label><input disabled="disabled" type="radio" value="landscape" />&nbsp;<?php _e( "Landscape", 'pdf-print' ); ?></label>
					</fieldset>
				</td>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="radio" value="portrait" checked="checked" />&nbsp;<?php _e( "Portrait", 'pdf-print' ); ?></label><br/>
						<label><input disabled="disabled" type="radio" value="landscape" />&nbsp;<?php _e( "Landscape", 'pdf-print' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'PDF files name', 'pdf-print' ); ?></th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="radio" /> <?php _e( 'Use post or page slug', 'pdf-print' ); ?></label><br />
						<input type="radio" disabled="disabled" /><input disabled="disabled" type="text" value="mpdf" /><br />
						<span class="bws_info">
							<?php _e( 'File name cannot contain more than 195 symbols. The file name can include Latin letters, numbers and symbols "-" , "_" only.', 'pdf-print' )  ?>
						</span>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Do not index pdf and print pages', 'pdf-print' ); ?></th>
				<td>
					<input type="checkbox" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<th scope="row" colspan="2">
					* <?php _e( 'If you upgrade to Pro version, all your settings will be saved.', 'pdf-print' ); ?>
				</th>
			</tr>
		</table>
	<?php }
}

if ( ! function_exists( 'pdfprnt_pdf_image_block' ) ) {
	function pdfprnt_pdf_image_block() { ?>
		<div class="pdfprnt_pro_button_settings bws_pro_version">
			<input type="file" disabled="disabled" />
			<span><?php _e( 'Current image', 'pdf-print' ); ?>:</span>
			<img alt="Default Image" src="<?php echo plugins_url( "images/pdf.png", dirname( __FILE__ ) ); ?>" />
		</div>
	<?php }
}

if ( ! function_exists( 'pdfprnt_print_image_block' ) ) {
	function pdfprnt_print_image_block() { ?>
		<div class="pdfprnt_pro_button_settings bws_pro_version">
			<input type="file" disabled="disabled" />
			<span><?php _e( 'Current image', 'pdf-print' ); ?>:</span>
			<img alt="Default Image" src="<?php echo plugins_url( "images/print.gif", dirname( __FILE__ ) ); ?>" />
		</div>
	<?php }
}

if ( ! function_exists( 'pdfprnt_woocommerce_block' ) ) {
	function pdfprnt_woocommerce_block () { ?>
		<table class="form-table bws_pro_version">
			<tr class="pdfprnt_watermark_row"><th scope="row"><?php _e( 'Watermark', 'pdf-print' ); ?></th>
				<td>
					<input disabled="disabled" type="radio">
					<label for="pdfprnt_no_watermark">
						<?php _e( 'None', 'pdf-print' ); ?>
					</label>
				</td>
			</tr>
			<tr class="pdfprnt_watermark_row"><th scope="row"></th>
				<td>
					<input disabled="disabled" type="radio" checked="checked">
					<label>
						<?php _e( 'Text', 'pdf-print' ); ?>
					</label>
				</td>
			</tr>
			<tr class="pdfprnt_watermark_row"><th scope="row"></th>
				<td>
					<textarea disabled="disabled" rows="3" cols="45"></textarea>
				</td>
			</tr>
			<tr class="pdfprnt_watermark_row"><th scope="row"></th>
				<td>
					<input disabled="disabled" type="radio">
					<label for="pdfprnt_image_watermark">
						<?php _e( 'Image', 'pdf-print' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Compatibility with Woocommerce', 'pdf-print' ); ?></th>
				<td><label><input type="checkbox" disabled="disabled"></label></td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Display product data', 'pdf-print' ); ?></th>
				<td>
					<fieldset>
						<label><input type="checkbox" disabled="disabled"><?php _e( 'Rating', 'pdf-print' ); ?></label><br>
						<label><input type="checkbox" disabled="disabled"><?php _e( 'Price', 'pdf-print' ); ?></label><br>
						<label><input type="checkbox" disabled="disabled"><?php _e( 'Stock', 'pdf-print' ); ?></label><br>
						<label><input type="checkbox" disabled="disabled"><?php _e( 'SKU', 'pdf-print' ); ?></label><br>
						<label><input type="checkbox" disabled="disabled"><?php _e( 'Categories', 'pdf-print' ); ?></label><br>
						<label><input type="checkbox" disabled="disabled"><?php _e( 'Tags', 'pdf-print' ); ?></label><br>
						<label><input type="checkbox" disabled="disabled"><?php _e( 'Product Variations', 'pdf-print' ); ?></label><br>
						<label><input type="checkbox" disabled="disabled"><?php _e( 'Product Short Description', 'pdf-print' ); ?></label><br>
						<label><input type="checkbox" disabled="disabled"><?php _e( 'Additional Information', 'pdf-print' ); ?></label><br>
						<label><input type="checkbox" disabled="disabled"><?php _e( 'Product Gallery', 'pdf-print' ); ?></label>
					</fieldset>
				</td>
			</tr>
		</table>
	<?php }
}

if ( ! function_exists( 'pdfprnt_extra_block' ) ) {
	function pdfprnt_extra_block() { ?>
		<div class="bws_pro_version">
			<p><?php _e( 'Please choose the necessary post types (or single pages), where PDF & Print buttons should be displayed:', 'pdf-print' ); ?></p>
			<p>	<label>
					<input disabled="disabled" checked="checked" type="checkbox"/>
					<?php _e( "Show URL for pages", 'pdf-print' );?>
				</label>
			</p>
			<img src="<?php echo plugins_url( 'images/pro_screen_1.png', dirname( __FILE__ ) ); ?>" alt="<?php _e( "Example of site pages' tree", 'pdf-print' ); ?>" title="<?php _e( "Example of site pages' tree", 'pdf-print' ); ?>" />
			<p class="submit">
				<input disabled="disabled" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'pdf-print' ); ?>" />
			</p>
			<p><strong>* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'pdf-print' ); ?></strong></p>
		</div>
	<?php }
}

if ( ! function_exists( 'pdfprnt_templates_new_block' ) ) {
	function pdfprnt_templates_new_block() { ?>
		<h2><?php _e( 'Add New Running Titles', 'pdf-print' ); ?></h2>
		<p class="hide-if-no-js desription"><i><?php _e( 'For more info click "Help" tab at the top of the page.', 'pdf-print' ); ?></i></p>
		<div>
			<div id="titlediv"><div id="titlewrap"><input disabled type="text" size="30" value="" id="title" placeholder="<?php _e( 'Enter Title', 'pdf-print' ); ?>" /></div></div>
			<h3><span><?php _e( 'Top running title', 'pdf-print' ); ?></span></h3>
			<div class="postarea wp-editor-expand">
				<div class="wp-core-ui wp-editor-wrap tmce-active">
				<div class="wp-editor-tools hide-if-no-js">
					<div class="wp-media-buttons">
						<link rel='stylesheet' id='editor-buttons-css'  href='#' type='text/css' media='all' />
						<a disabled href="#" id="insert-media-button" class="button insert-media add_media" data-editor="pdfprnt_top" title="<?php _e( 'Add Media', 'pdf-print' ); ?>"><span class="wp-media-buttons-icon"></span> <?php _e( 'Add Media', 'pdf-print' ); ?></a>
					</div>
				</div>
				<div class="wp-editor-container"><textarea disabled style="width: 100%;" class="wp-editor-area" rows="5" autocomplete="off"></textarea></div>
				</div>
			</div>
			<h3><span><?php _e( 'Bottom running title', 'pdf-print' ); ?></span></h3>
			<div class="postarea wp-editor-expand">
				<div class="wp-core-ui wp-editor-wrap tmce-active">
					<div class="wp-editor-tools hide-if-no-js">
						<div class="wp-media-buttons"><a disabled href="#" class="button insert-media add_media" data-editor="pdfprnt_bottom" title="<?php _e( 'Add Media', 'pdf-print' ); ?>"><span class="wp-media-buttons-icon"></span> <?php _e( 'Add Media', 'pdf-print' ); ?></a></div>
					</div>
					<div class="wp-editor-container"><textarea disabled style="width: 100%;" class="wp-editor-area" rows="5" autocomplete="off"></textarea></div>
				</div>
			</div>
		</div>
		<p><input disabled type="submit" class="button-primary" value="<?php _e( 'Save running titles', 'pdf-print' ); ?>" /></p>
	<?php }
}

if ( ! function_exists( 'pdfprnt_templates_block' ) ) {
	function pdfprnt_templates_block() { ?>
		<ul class='subsubsub'>
			<li class='all'><a class="current" href="#"><?php _e( 'All', 'pdf-print' ); ?><span class="count"> ( 2 )</span></a> |</li>
			<li class='trash'><a href="#"><?php _e( 'Trash', 'pdf-print' ); ?><span class="count"> ( 0 )</span></a></li>
		</ul>
		<p class="search-box">
			<input disabled="disabled" type="search" name="s" />
			<input disabled="disabled" type="submit" id="search-submit" class="button" value="<?php _e( 'search', 'pdf-print' ); ?>" />
		</p>
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<select disabled="disabled">
					<option value='-1' selected='selected'><?php _e( 'Bulk Actions', 'pdf-print' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction" class="button action" value="<?php _e( 'Apply', 'pdf-print' ); ?>" />
			</div>
			<div class='tablenav-pages one-page'>
				<span class="displaying-num">2 <?php _e( 'items', 'pdf-print' ); ?></span>
			</div>
			<br class="clear" />
		</div>
		<table class="wp-list-table widefat striped templates">
			<thead>
				<tr>
					<th scope='col' id='cb'    class='manage-column column-cb'><input disabled="disabled" id="cb-select-all-1" type="checkbox" /></th>
					<th scope='col' id='title' class='manage-column column-title column-primary' style=""><a href="#"><span><?php _e( 'Title', 'pdf-print' ); ?></span></a></th>
					<th scope='col' id='pdf'   class='manage-column column-pdf'>PDF</th>
					<th scope='col' id='print' class='manage-column column-print'>Print</th>
					<th scope='col' id='date'  class='manage-column column-date sortable desc'><a href="#"><span><?php _e( 'Date', 'pdf-print' ); ?></span></th>
				</tr>
			</thead>
			<tbody id="the-list" data-wp-lists='list:template'>
				<tr>
					<th scope="row" class="check-column"><input disabled="disabled" type="checkbox" /></th>
					<td class='title column-title column-primary'><strong><a href="#"><?php _e( 'Custom running titles', 'pdf-print' ); ?></a></strong> </td>
					<td class='pdf column-pdf'><input disabled="disabled" type="radio" /></td>
					<td class='print column-print'><input disabled="disabled" type="radio" /></td>
					<td class='date column-date'>June 26, 2015</td>
				</tr>
				<tr>
					<th scope="row" class="check-column"><input disabled="disabled" type="checkbox" /></th>
					<td class='title column-title column-primary'>	<strong><a href="#"><?php _e( 'New running titles', 'pdf-print' ); ?></a></strong></td>
					<td class='pdf column-pdf'><input disabled="disabled" type="radio" /></td>
					<td class='print column-print'><input disabled="disabled" type="radio" /></td>
					<td class='date column-date'>June 25, 2015</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th scope='col' class='manage-column column-cb'><input disabled id="cb-select-all-2" type="checkbox" /></th>
					<th scope='col' class='manage-column column-title column-primary' style=""><a href="#"><span><?php _e( 'Title', 'pdf-print' ); ?></span></a></th>
					<th scope='col' class='manage-column column-pdf'>PDF</th>
					<th scope='col' class='manage-column column-print'>Print</th>
					<th scope='col' class='manage-column column-date sortable desc'><a href="#"><span><?php _e( 'Date', 'pdf-print' ); ?></span></th>
				</tr>
			</tfoot>
		</table>
		<div class="tablenav bottom">
			<div class="alignleft actions bulkactions">
				<select disabled="disabled">
					<option value='-1' selected='selected'><?php _e( 'Bulk Actions', 'pdf-print' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction" class="button action" value="<?php _e( 'Apply', 'pdf-print' ); ?>" />
			</div>
			<div class='tablenav-pages one-page'>
				<span class="displaying-num">2 <?php _e( 'items', 'pdf-print' ); ?></span>
			</div>
			<br class="clear" />
		</div>
	<?php }
}