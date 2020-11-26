<?php
/**
 * Abstract UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;

/**
 * Class Component
 *
 * @package Cloudinary\UI
 */
class Page extends Panel {

	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function content() {

		// Get the options setting input field.
		$option_group  = $this->setting->get_option_group();
		$atts          = $this->get_attributes( 'content' );
		$atts['type']  = 'hidden';
		$atts['value'] = $option_group;

		// Set the attributes for the field.
		$option = array(
			'type'  => 'hidden',
			'name'  => 'option_page',
			'value' => $option_group,
		);
		// Set the attributes for the field action.
		$action = array(
			'type'  => 'hidden',
			'name'  => 'action',
			'value' => 'update',
		);
		$html   = array(
			'<input ' . $this->build_attributes( $atts ) . ' />',
			'<input ' . $this->build_attributes( $option ) . ' />',
			'<input ' . $this->build_attributes( $action ) . ' />',
			wp_nonce_field( $option_group . '-options', '_wpnonce', true, false ),
		);

		return self::compile_html( $html );
	}

	/**
	 * Start the component Wrapper.
	 *
	 * @return string
	 */
	protected function start_wrapper() {
		settings_errors( $this->setting->get_option_group() );
		$form_atts = array(
			'method'     => 'post',
			'action'     => 'options.php',
			'novalidate' => 'novalidate',
			'class'      => 'render-trigger',
		);
		$html      = array(
			'<div ' . $this->build_attributes( array( 'class' => 'wrap' ) ) . '>',
			'<h1>' . $this->setting->get_param( 'page_title' ) . '</h1>',
			'<form ' . $this->build_attributes( $form_atts ) . ' >',
		);

		return self::compile_html( $html );
	}

	/**
	 * Create the end of the wrapper HTML.
	 *
	 * @return string
	 */
	protected function end_wrapper() {
		$html = array(
			'</form>',
			'</div>',
		);

		return self::compile_html( $html );
	}

}

