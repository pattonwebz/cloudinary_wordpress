<?php
/**
 * Text UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;

use Cloudinary\UI\Component;

/**
 * Class Component
 *
 * @package Cloudinary\UI
 */
class Text extends Component {


	protected function pre_render() {
		// TODO: Implement pre_render() method.
	}

	public function __construct( $setting ) {
		parent::__construct( $setting );

		$this->attributes['heading_wrapper']['class'] = array(
			'cld-input-group__label',
		);
	}

	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function content() {
		$atts            = $this->get_attributes( 'content' );
		$atts['type']    = 'text';
		$atts['name']    = $this->get_name();
		$atts['id']      = $this->setting->get_slug();
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
		$html[] = '<span ' . $this->build_attributes( $this->get_attributes( 'heading' ) ) . ' >';
		$html[] = $this->setting->get_param( 'title' );
		$html[] = '</span>';
		if ( $this->setting->has_param( 'tooltip' ) ) {
			$html[] = $this->tooltip();
		}

		return self::compile_html( $html );
	}

	/**
	 * Start the component Wrapper.
	 *
	 * @return string
	 */
	protected function start_heading() {
		$atts        = $this->get_attributes( 'heading_wrapper' );
		$atts['for'] = $this->setting->get_slug();

		return '<label ' . $this->build_attributes( $atts ) . ' >';
	}

	/**
	 * Create the end of the heading wrapper HTML.
	 *
	 * @return string
	 */
	protected function end_heading() {
		return '</label>';
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

	/**
	 * Renders the component.
	 */
	public function render() {

		$html = array();


		// Component heading/title.
		if ( $this->setting->has_param( 'title' ) ) {
			$html[] = $this->start_heading();
			$html[] = $this->heading();
			$html[] = $this->end_heading();
		}
		// Main Component Wrapper.
		$html[] = $this->start_wrapper();

		// Component content prefix.
		if ( $this->setting->has_param( 'prefix' ) ) {
			$html[] = $this->prefix();
		}
		// Component Content.
		if ( $this->setting->has_param( 'content' ) ) {
			$html[] = $this->content();
		}

		// Component Suffix.
		if ( $this->setting->has_param( 'suffix' ) ) {
			$html[] = $this->suffix();
		}
		// Component description.
		if ( $this->setting->has_param( 'description' ) ) {
			$html[] = $this->description();
		}
		// Do settings.
		if ( $this->setting->has_settings() ) {
			$html[] = $this->settings();
		}
		// End component wrapper.
		$html[] = $this->end_wrapper();

		return self::compile_html( $html );
	}
}
