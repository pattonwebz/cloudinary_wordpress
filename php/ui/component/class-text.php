<?php
/**
 * Text UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;

use Cloudinary\UI;

/**
 * Class Component
 *
 * @package Cloudinary\UI
 */
class Text extends UI\Component {


	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function content() {
		$atts            = $this->get_attributes( 'content' );
		$atts['type']    = 'text';
		$atts['name']    = $this->get_name();
		$atts['value']   = $this->setting->get_value();
		$atts['class'][] = 'regular-text';
		if ( $this->setting->has_param( 'required' ) ) {
			$atts['required'] = 'required';
		}

		return '<input ' . $this->build_attributes( $atts ) . ' />';
	}

	/**
	 * Creates the Header HTML.
	 *
	 * @return string
	 */
	protected function heading() {
		$html = array();
		if ( $this->setting->has_param( 'icon' ) ) {
			$html[] = $this->get_icon();
		}
		$html[] = '<strong ' . $this->build_attributes( $this->get_attributes( 'heading' ) ) . ' >';
		$html[] = $this->setting->get_param( 'title' );
		if ( $this->setting->has_param( 'tooltip' ) ) {
			$html[] = $this->tooltip();
		}
		$html[] = '</strong>';

		return self::compile_html( $html );
	}

	/**
	 * Get the field name.
	 *
	 * @return string
	 */
	protected function get_name() {
		return $this->setting->get_option_name() . '[' . $this->setting->get_slug() . ']';
	}

	/**
	 * Sanitize the value.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string
	 */
	public function sanitize_value( $value ) {
		return sanitize_text_field( $value );
	}
}
