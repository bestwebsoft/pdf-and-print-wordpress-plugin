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
		global $pdfprnt_plugin_info, $wp_version, $pdfprnt_options_array;
		if ( ! bws_hide_premium_options_check( $pdfprnt_options_array ) || ! $show_cross ) { ?>
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
					<a class="bws_button" href="http://bestwebsoft.com/products/pdf-print/?k=d9da7c9c2046bed8dfa38d005d4bffdb&pn=101&v=<?php echo $pdfprnt_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="User Role Pro Plugin"><?php _e( 'Learn More', 'pdf-print' ); ?></a>
				</div>
			</div>
	<?php }
	}
}

if ( ! function_exists( 'pdfprnt_image_block' ) ) {
	function pdfprnt_image_block() { ?>
	<table class="form-table bws_pro_version">
		<?php $buttons = array(
			array( 'title' => __( 'PDF button image', 'pdf-print' ), 'image' => 'pdf.png' ),
			array( 'title' => __( 'Print button image', 'pdf-print' ), 'image' => 'print.gif' )
		);
		foreach ( $buttons as $button ) { ?>
			<tr>
				<th scope="row"><?php echo $button['title']; ?></th>
				<td>
					<select disabled="disabled" style="min-width: 120px;">
						<option value="1"><?php _e( 'Custom', 'pdf-print' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"></th>
				<td><input type="file" disabled="disabled" /></td>
			</tr>
			<tr>
				<th scope="row"></th>
				<td>
					<span><?php _e( 'Current image', 'pdf-print' ); ?>:</span>
					<img alt="Default Image" src="<?php echo plugins_url( "images/" . $button['image'], dirname( __FILE__ ) ); ?>" />
				</td>
			</tr>
		<?php } ?>
		<tr>
			<th scope="row"><?php _e( 'PDF files name', 'pdf-print' ); ?></th>
			<td>
				<fieldset>
					<label><input disabled="disabled" type="radio" /> <?php _e( 'use post or page slug', 'pdf-print' ); ?></label><br />
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
		<tr valign="top">
			<th scope="row" colspan="2">
				* <?php _e( 'If you upgrade to Pro version, all your settings will be saved.', 'pdf-print' ); ?>
			</th>
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
		<h2><?php _e( 'Add New template', 'pdf-print' ); ?></h2>
		<p class="hide-if-no-js desription"><i><?php _e( 'For more info click "Help" tab at the top of the page.', 'pdf-print' ); ?></i></p>
		<div id="pdfprntpr_template_page">
			<div id="titlediv"><div id="titlewrap"><input disabled type="text" name="pdfprntpr_template_title" size="30" value="" id="title" placeholder="<?php _e( 'Enter Template Title', 'pdf-print' ); ?>" /></div></div>
			<h3><span><?php _e( 'Top running title', 'pdf-print' ); ?></span></h3>
			<div class="postarea wp-editor-expand">
				<div id="wp-pdfprntpr_top-wrap" class="wp-core-ui wp-editor-wrap tmce-active">
				<div id="wp-pdfprntpr_top-editor-tools" class="wp-editor-tools hide-if-no-js">
					<div id="wp-pdfprntpr_top-media-buttons" class="wp-media-buttons">
						<link rel='stylesheet' id='editor-buttons-css'  href='#' type='text/css' media='all' />
						<a disabled href="#" id="insert-media-button" class="button insert-media add_media" data-editor="pdfprntpr_top" title="<?php _e( 'Add Media', 'pdf-print' ); ?>"><span class="wp-media-buttons-icon"></span> <?php _e( 'Add Media', 'pdf-print' ); ?></a>
					</div>
				</div>
				<div id="wp-pdfprntpr_top-editor-container" class="wp-editor-container"><textarea disabled style="width: 100%;" class="pdfprntpr_top wp-editor-area" rows="5" autocomplete="off" name="pdfprntpr_top" id="pdfprntpr_top"></textarea></div>
				</div>
			</div>
			<h3><span><?php _e( 'Bottom running title', 'pdf-print' ); ?></span></h3>
			<div class="postarea wp-editor-expand">
				<div id="wp-pdfprntpr_bottom-wrap" class="wp-core-ui wp-editor-wrap tmce-active">
					<div id="wp-pdfprntpr_bottom-editor-tools" class="wp-editor-tools hide-if-no-js">
						<div id="wp-pdfprntpr_bottom-media-buttons" class="wp-media-buttons"><a disabled href="#" class="button insert-media add_media" data-editor="pdfprntpr_bottom" title="<?php _e( 'Add Media', 'pdf-print' ); ?>"><span class="wp-media-buttons-icon"></span> <?php _e( 'Add Media', 'pdf-print' ); ?></a></div>
					</div>
					<div id="wp-pdfprntpr_bottom-editor-container" class="wp-editor-container"><textarea disabled style="width: 100%;" class="pdfprntpr_bottom wp-editor-area" rows="5" autocomplete="off" name="pdfprntpr_bottom" id="pdfprntpr_bottom"></textarea></div>
				</div>
			</div>
		</div>
		<p><input disabled name="pdfprntpr_template_submit" type="submit" class="button-primary" value="<?php _e( 'Save template', 'pdf-print' ); ?>" /></p>
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
					<td class='title column-title column-primary'><strong><a href="#">Custom template</a></strong> </td>
					<td class='pdf column-pdf'><input disabled="disabled" type="radio" /></td>
					<td class='print column-print'><input disabled="disabled" type="radio" /></td>
					<td class='date column-date'>June 26, 2015</td>
				</tr>
				<tr>
					<th scope="row" class="check-column"><input disabled="disabled" type="checkbox" /></th>
					<td class='title column-title column-primary'>	<strong><a href="#">New template</a></strong></td>
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
