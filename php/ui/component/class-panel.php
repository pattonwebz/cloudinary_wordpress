<?php
/**
 * Panel UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;

use Cloudinary\UI\Component;
use Cloudinary\Settings\Setting;

/**
 * Class Component
 *
 * @package Cloudinary\UI
 */
class Panel extends Component {

	public function __construct( $setting ) {
		parent::__construct( $setting );
		$this->attributes['wrapper']['class']         = array(
			'cld-box',
		);
		$this->attributes['heading_wrapper']['class'] = array(
			'cld-box__header',
			'cld-box__header--divided',
			'p-1.5',
		);
		if ( $this->setting->has_param( 'title' ) ) {
			$this->attributes['wrapper']['class'][] = 'p-0';
		}
	}

	/**
	 * Start the component Wrapper.
	 *
	 * @return string
	 */
	protected function start_heading() {
		return '<header ' . $this->build_attributes( $this->get_attributes( 'heading_wrapper' ) ) . ' >';
	}

	/**
	 * Creates the Header HTML.
	 *
	 * @return string
	 */
	protected function heading() {
		$html        = array();
		$header_atts = array();
		if ( $this->setting->has_param( 'icon' ) ) {
			$html[]                 = $this->get_icon();
			$header_atts['class'][] = 'ml-1';
		}
		$html[] = '<h2 ' . $this->build_attributes( $header_atts ) . ' >';
		$html[] = $this->setting->get_param( 'title' );
		if ( $this->setting->has_param( 'tooltip' ) ) {
			$html[] = $this->tooltip();
		}
		$html[] = '</h2>';

		return self::compile_html( $html );
	}

	protected function end_heading() {
		return '</header>';
	}

	/**
	 * Gets the active child setting.
	 *
	 * @return Setting
	 */
	protected function get_active_setting() {
		$active_setting = '_tmp_fallback';
		if ( $this->setting->has_settings() ) {
			$active_setting = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
			if ( ! $this->setting->has_setting( $active_setting ) ) { // Tab is invalid or not set, check if in a POST.
				$active_setting = filter_input( INPUT_POST, 'tab', FILTER_SANITIZE_STRING );
				if ( ! $this->setting->has_setting( $active_setting ) ) { // Tab is invalid or not set, load the default/first tab.
					$settings       = $this->setting->get_setting_slugs();
					$active_setting = array_shift( $settings );
				}
			}
		}

		return $this->setting->get_setting( $active_setting );
	}

	protected function settings() {
		$html = array();

		$html[] = parent::settings();
		if ( $this->setting->has_param( 'submit' ) ) {
			$footer_atts = array(
				'class' => 'cld-box__footer cld-box__footer--divided p-1.5',
			);
			$button_atts = array(
				'type'  => 'submit',
				'class' => $this->setting->get_param( 'class', 'button button-primary' ),
			);
			$html[]      = '<footer ' . $this->build_attributes( $footer_atts ) . ' >';
			$html[]      = '<button ' . $this->build_attributes( $button_atts ) . ' >';
			$html[]      = $this->setting->get_param( 'text', __( 'Save Changes', 'cloudinary' ) );
			$html[]      = '</button>';
			$html[]      = '</footer>';

		}

		return self::compile_html( $html );
	}
}

