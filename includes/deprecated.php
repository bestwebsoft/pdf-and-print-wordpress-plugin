<?php
/**
* Includes deprecated functions
*/

/**
 * Save converted options
 * @deprecated since 1.9.5
 * @todo remove after 01.11.2017
 */
if ( ! function_exists( 'pdfprnt_check_options' ) ) {
	function pdfprnt_check_options() {
		if ( is_multisite() ) {
			global $wpdb;
			if ( $pdfprnt_site_options = get_site_option( 'pdfprnt_options' ) ) {
				$pdfprnt_site_options = pdfprnt_convert_options( $pdfprnt_site_options );
				update_site_option( 'pdfprnt_options', $pdfprnt_site_options );
			}

			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			$old_blog = $wpdb->blogid;
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );

				if ( $pdfprnt_options = get_option( 'pdfprnt_options' ) ) {
					$pdfprnt_options = pdfprnt_convert_options( $pdfprnt_options );
					update_option( 'pdfprnt_options', $pdfprnt_options );
				}
			}
			switch_to_blog( $old_blog );
		} else {
			if ( $pdfprnt_options = get_option( 'pdfprnt_options' ) ) {
				$pdfprnt_options = pdfprnt_convert_options( $pdfprnt_options );
				update_option( 'pdfprnt_options', $pdfprnt_options );
			}
		}
	}
}

/**
 * Converting old version options to new
 * @deprecated since 1.9.5
 * @todo remove after 01.11.2017
 */
if ( ! function_exists( 'pdfprnt_convert_options' ) ) {
	function pdfprnt_convert_options( $pdfprnt_options = array() ) {
		if ( version_compare( $pdfprnt_options['plugin_option_version'], '1.9.5', '<' ) ) {
			$buttons = array( 'pdf', 'print' );
			$converted_options = array();
			foreach ( $buttons as $button ) {
				/* Add Button to */
				$converted_options['button_post_types'][ $button ] = $pdfprnt_options['use_types_posts'];

				if ( isset( $pdfprnt_options["show_btn_{$button}_search_archive"] ) && $pdfprnt_options["show_btn_{$button}_search_archive"] == 1 ) {
					$converted_options['button_post_types'][ $button ][] = 'pdfprnt_search';
					$converted_options['button_post_types'][ $button ][] = 'pdfprnt_archives';
				} else {
					$converted_options['button_post_types'][ $button ] = array();
				}

				/* Button Image */
				$converted_options['button_image'][ $button ] = array(
					'type'		=> ( ! empty( $pdfprnt_options[ "{$button}_button_image" ] ) ) ? $pdfprnt_options[ "{$button}_button_image" ] : 'default',
					'image_src'	=> plugins_url( "images/{$button}.png", dirname( __FILE__ ) )
				);

				/* Button Title */
				$converted_options['button_title'][ $button ] = ( ! empty( $pdfprnt_options[ "{$button}_button_title" ] ) ) ? $pdfprnt_options[ "{$button}_button_title" ] : '';
			}

			/* Buttons Position */
			$converted_options['buttons_position'] = ( ! empty( $pdfprnt_options['position'] ) ) ? $pdfprnt_options['position'] : 'top-right';

			/* Shortcode Settings */
			$converted_options['do_shorcodes'] = ( isset( $pdfprnt_options['tmpl_shorcode'] ) ) ? $pdfprnt_options['tmpl_shorcode'] : 1;

			/* Default CSS */
			$converted_options['use_default_css'] = ( isset( $pdfprnt_options[ 'use_theme_stylesheet' ] ) && $pdfprnt_options[ 'use_theme_stylesheet' ] == 1 ) ? 'theme' : 'default';

			/* Custom CSS */
			$converted_options['use_custom_css'] = ( isset( $pdfprnt_options[ 'use_custom_styles' ] ) ) ? $pdfprnt_options[ 'use_custom_styles' ] : 0;
			$converted_options['custom_css_code'] = ( ! empty( $pdfprnt_options[ 'custom_styles' ] ) ) ? $pdfprnt_options[ 'custom_styles' ] : '';

			/*
			 * Delete unnecessary options
			 */

			/* Add Button to */
			unset( $pdfprnt_options['use_types_posts'] );
			unset( $pdfprnt_options['show_btn_pdf'], $pdfprnt_options['show_btn_print'] );
			unset( $pdfprnt_options['show_btn_pdf_search_archive'], $pdfprnt_options['show_btn_print_search_archive'] );
			/* Buttons Position */
			unset( $pdfprnt_options['position'], $pdfprnt_options['position_search_archive'] );
			/* Button Image */
			unset( $pdfprnt_options['pdf_button_image'], $pdfprnt_options['print_button_image'] );
			/* Button Title */
			unset( $pdfprnt_options['pdf_button_title'], $pdfprnt_options['print_button_title'] );
			/* Shortcode Settings */
			unset( $pdfprnt_options['tmpl_shorcode'] );
			/* Default CSS */
			unset( $pdfprnt_options[ 'use_theme_stylesheet' ] );
			/* Custom CSS */
			unset ( $pdfprnt_options[ 'use_custom_styles' ], $pdfprnt_options[ 'custom_styles' ] );

			$pdfprnt_options = array_merge( $converted_options, $pdfprnt_options );
		}

		return $pdfprnt_options;
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