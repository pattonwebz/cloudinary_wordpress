<?php
/**
 * Page Header UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;

use Cloudinary\UI\Component;

/**
 * Header Component
 *
 * @package Cloudinary\UI
 */
class Page_Header extends Component {


	/**
	 * Start the component Wrapper.
	 *
	 * @return string
	 */
	protected function start_wrapper() {
		$atts = $this->get_attributes( 'wrapper' );

		return '<header ' . $this->build_attributes( $atts ) . ' >';
	}

	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function content() {
		return $this->setting->get_param( 'content' );
	}

	/**
	 * Create the end of the wrapper HTML.
	 *
	 * @return string
	 */
	protected function end_wrapper() {
		return '</header>';
	}

}
