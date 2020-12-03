<?php
/**
 * Columns UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;

use Cloudinary\UI\Component;

/**
 * Frame Component to render components only.
 *
 * @package Cloudinary\UI
 */
class Columns extends Component {


	/**
	 * Render the content, but only child settings.
	 *
	 * @return string
	 */
	public function render() {

		$atts   = array(
			'class' => 'cld-' . $this->setting->get_param( 'count', 1 ) . '-columns',
		);
		$html   = array();
		$html[] = '<div ' . $this->build_attributes( $atts ) . ' >';
		$html[] = $this->settings();
		$html[] = '</div>';

		return self::compile_html( $html );
	}

	protected function settings() {

		$columns  = $this->setting->get_param( 'count', 1 );
		$settings = $this->setting->get_settings();
		$args     = array(
			'class' => 'cld-' . $columns . '-columns__left',
		);
		$html     = array();
		$html[]   = '<section ' . $this->build_attributes( $args ) . ' >';
		foreach ( $settings as $setting ) {
			if ( 'left' === $setting->get_param( 'side', 'left' ) ) {
				$html[] = $setting->render_component();
			}
		}
		$html[] = '</section>';
		if ( 2 === $this->setting->get_param( 'count', 1 ) ) {
			$args['class'] = 'cld-' . $columns . '-columns__right';
			$html[]        = '<section ' . $this->build_attributes( $args ) . ' >';
			foreach ( $settings as $setting ) {
				if ( 'right' === $setting->get_param( 'side', 'left' ) ) {
					$html[] = $setting->render_component();
				}
			}
			$html[] = '</section>';
		}

		return self::compile_html( $html );
	}
}
