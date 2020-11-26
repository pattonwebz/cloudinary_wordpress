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
class Text extends UI\Component {


	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function content() {
		$atts            = $this->get_attributes( 'content' );
		$atts['type']    = 'text';
		$atts['name']    = $this->setting->get_option_group() . '[' . $this->setting->get_slug() . ']';
		$atts['value']   = $this->setting->get_value();
		$atts['class'][] = 'regular-text';
		if ( $this->setting->has_param( 'required' ) ) {
			$atts['required'] = 'required';
		}

		return '<input ' . $this->build_attributes( $atts ) . ' />';
	}

}
