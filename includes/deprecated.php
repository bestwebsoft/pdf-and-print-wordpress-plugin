<?php
/**
* Includes deprecated functions
*/
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