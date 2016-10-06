<?php
/**
* Includes deprecated functions
 * @deprecated since 1.9.0
 * @todo remove after 01.03.2017
 */

/* Renaming old version options name */
if ( ! function_exists( 'pdfprnt_check_old_options' ) ) {
	function pdfprnt_check_old_options() {
		if ( is_multisite() ) {
			global $wpdb;
			if ( $old_options = get_site_option( 'pdfprnt_options_array' ) ) {
				if ( ! get_site_option( 'pdfprnt_options' ) )
					add_site_option( 'pdfprnt_options', $old_options );
				else
					update_site_option( 'pdfprnt_options', $old_options );
				delete_site_option( 'pdfprnt_options_array' );
			}
			$blogids  = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			$old_blog = $wpdb->blogid;
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				if ( $old_options = get_option( 'pdfprnt_options_array' ) ) {
					if ( ! get_option( 'pdfprnt_options' ) )
						add_option( 'pdfprnt_options', $old_options );
					else
						update_option( 'pdfprnt_options', $old_options );
					delete_option( 'pdfprnt_options_array' );
				}
			}
			switch_to_blog( $old_blog );
		} else {
			if ( $old_options = get_option( 'pdfprnt_options_array' ) ) {
				if ( ! get_option( 'pdfprnt_options' ) )
					add_option( 'pdfprnt_options', $old_options );
				else
					update_option( 'pdfprnt_options', $old_options );
				delete_option( 'pdfprnt_options_array' );
			}
		}
	}
}