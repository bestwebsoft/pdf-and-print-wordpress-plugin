<?php
/**
 * Pdfprnt_Buttons_Widget Class
 */

/**
 * Buttons Widget
 */
class Pdfprnt_Buttons_Widget extends WP_Widget {
	/**
	 * Constract
	 */
	public function __construct() {
		parent::__construct( 'pdfprnt_buttons', esc_html__( 'PDF & Print Buttons', 'pdf-print' ), array( 'description' => esc_html__( 'Show PDF & Print Buttons on your site', 'pdf-print' ) ) );
	}

	/**
	 * Functiona for widget
	 *
	 * @param array $args     Arguments for widget.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		global $pdfprnt_options, $pdfprnt_is_search_archive, $pdfprnt_is_custom_post_type;
		$url     = isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ? ( is_ssl() ? 'https://' : 'http://' ) . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) . sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$url     = esc_url( $url );
		$buttons = '';
		$target  = '_blank';

		foreach ( array( 'pdf', 'print' ) as $button ) {
			$instance[ $button . '_button_show' ]  = ! empty( $instance[ $button . '_button_show' ] ) ? 1 : 0;
			$instance[ $button . '_button_title' ] = wp_strip_all_tags( $pdfprnt_options['button_title'][ $button ] );
			if (
				isset( $pdfprnt_options['button_image'][ $button ]['type'] ) &&
				in_array( $pdfprnt_options['button_image'][ $button ]['type'], array( 'none', 'default' ), true )
			) {
				$instance[ $button . '_button_image' ] = $pdfprnt_options['button_image'][ $button ]['type'];
			} else {
				$instance[ $button . '_button_image' ] = 'default';
			}
		}

		if ( ! empty( $pdfprnt_is_search_archive ) ) {
			$custom_query = '-search';
		} elseif ( ! empty( $pdfprnt_is_custom_post_type ) ) {
			$custom_query = '-custom';
		} else {
			$custom_query = '';
		}

		if ( 1 === intval( $instance['pdf_button_show'] ) ) {
			$pdf_url      = add_query_arg( 'print', 'pdf' . $custom_query, $url );
			$button_title = ! empty( $instance['pdf_button_title'] ) ? '<span class="pdfprnt-button-title pdfprnt-button-pdf-title">' . $instance['pdf_button_title'] . '</span>' : '';
			$button_image = 'none' !== $instance['pdf_button_image'] ? '<img src="' . plugins_url( 'images/pdf.png', dirname( __FILE__ ) ) . '" alt="image_pdf" title="' . esc_html__( 'View PDF', 'pdf-print' ) . '" />' : '';

			if ( $pdfprnt_options['image_to_pdf'] ) {
				$pdf_url = 'javascript: imageToPdf()';
				$target  = '_self';
			}
			$buttons_pdf = sprintf(
				'<a href="%s" class="pdfprnt-button pdfprnt-button-pdf" target="%s">%s%s</a>',
				$pdf_url,
				$target,
				$button_title,
				$button_image
			);
			$buttons_pdf = apply_filters( 'pdfprnt_add_atribute_rel_widget_pdf', $buttons_pdf, $pdf_url, $target, $button_image, $button_title );
			add_action( 'wp_footer', 'pdfprnt_add_script' );
		}
		if ( 1 === intval( $instance['print_button_show'] ) ) {
			$print_url     = add_query_arg( 'print', 'print' . $custom_query, $url );
			$button_title  = ! empty( $instance['print_button_title'] ) ? '<span class="pdfprnt-button-title pdfprnt-button-pdf-title">' . $instance['print_button_title'] . '</span>' : '';
			$button_image  = 'none' !== $instance['print_button_image'] ? '<img src="' . plugins_url( 'images/print.png', dirname( __FILE__ ) ) . '" alt="image_print" title="' . esc_html__( 'Print Content', 'pdf-print-plus' ) . '" />' : '';
			$buttons_print = sprintf(
				'<a href="%s" class="pdfprnt-button pdfprnt-button-print" target="_blank">%s%s</a>',
				$print_url,
				$button_image,
				$button_title
			);
			$buttons_print = apply_filters( 'pdfprnt_add_atribute_rel_widget_print', $buttons_print, $print_url, $button_image, $button_title );
		}
		$buttons = $buttons_pdf . $buttons_print;
		if ( ! empty( $buttons ) ) {
			$title   = ( ! empty( $instance['title'] ) ) ? apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) : '';
			$buttons = sprintf(
				'<div class="pdfprnt-buttons pdfprnt-widget">%s</div>',
				$buttons
			);
			echo wp_kses_post( $args['before_widget'] );
			if ( ! empty( $title ) ) {
				echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
			}
			echo wp_kses_post( $buttons );
			echo wp_kses_post( $args['after_widget'] );
		}
	}

	/**
	 * Functiona for update widget form
	 *
	 * @param array $new_instance New info for widget.
	 * @param array $old_instance Old info for widget.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                      = $old_instance;
		$instance['title']             = wp_strip_all_tags( $new_instance['title'] );
		$instance['pdf_button_show']   = ! empty( $new_instance['pdf_button_show'] ) ? 1 : 0;
		$instance['print_button_show'] = ! empty( $new_instance['print_button_show'] ) ? 1 : 0;
		return $instance;
	}

	/**
	 * Functiona for widget form
	 *
	 * @param array $instance Info for widget.
	 */
	public function form( $instance ) {
		global $pdfprnt_options;
		if ( empty( $pdfprnt_options ) ) {
			$pdfprnt_options = get_option( 'pdfprnt_options' );
		}

		if ( ! empty( $instance ) ) {
			$title             = ! empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$pdf_button_show   = ! empty( $instance['pdf_button_show'] ) ? 1 : 0;
			$print_button_show = ! empty( $instance['print_button_show'] ) ? 1 : 0;
		} else {
			$title             = '';
			$pdf_button_show   = 1;
			$print_button_show = 1;
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'pdf-print' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_html( $title ); ?>" />
		</p>
		<p>
			<input class="widefat pdfprnt-show-button-cb" id="<?php echo esc_attr( $this->get_field_id( 'pdf_button_show' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pdf_button_show' ) ); ?>" type="checkbox" value="1" <?php checked( 1, $pdf_button_show ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'pdf_button_show' ) ); ?>"><?php esc_html_e( 'Display PDF Button', 'pdf-print' ); ?></label>
		</p>
		<p>
			<input class="widefat pdfprnt-show-button-cb" id="<?php echo esc_attr( $this->get_field_id( 'print_button_show' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'print_button_show' ) ); ?>" type="checkbox" value="1" <?php checked( 1, $print_button_show ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'print_button_show' ) ); ?>"><?php esc_html_e( 'Display Print Button', 'pdf-print' ); ?></label>
		</p>
		<?php
	}
}
