<?php
/**
 * Submit UI Component.
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
class Submit extends UI\Component {


	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function content() {
		$atts            = $this->get_attributes( 'content' );
		$atts['type']    = 'submit';
		$atts['value']   = $this->setting->get_param( 'label', __( 'Submit', 'cloudinary' ) );
		$atts['class'][] = 'button-primary';

		return '<input ' . $this->build_attributes( $atts ) . ' />';
	}

}
