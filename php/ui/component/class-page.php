<?php
/**
 * Abstract UI Component.
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
class page extends UI\Component {

	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function content() {

		// Get the options setting input field.
		$option_group  = $this->get_active_tab()->get_option_slug();
		$atts          = $this->get_default_attributes( 'content' );
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
			wp_nonce_field( $option_group . '-options', $name = '_wpnonce', $referer = true, $echo = true ),
		);

		settings_fields( $option_group );

		return self::compile_html( $html );
	}

	/**
	 * @return string
	 */
	protected function start_wrapper() {
		settings_errors( $this->setting->get_option_slug() );
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

	/**
	 * Render the components settings.
	 *
	 * @return string
	 */
	protected function settings() {
		return $this->get_active_tab()->render_component();
	}

	/**
	 * Gets the active tab/setting.
	 *
	 * @return \Cloudinary\Settings\Setting
	 */
	protected function get_active_tab() {
		$active_tab = '_tmp_fallback';
		if ( $this->setting->has_settings() ) {
			$active_tab = $this->setting->get_setting_slugs()[0];
			if ( $this->setting->has_param( 'active_tab' ) ) {
				$active_tab = $this->setting->get_param( 'active_tab' );
			}
		}

		return $this->setting->get_setting( $active_tab );
	}

}

