<?php
/**
 * Page Footer UI Component.
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
class Page_Footer extends Component {

	protected function pre_render() {
		// TODO: Implement pre_render() method.
	}

	/**
	 * Start the component Wrapper.
	 *
	 * @return string
	 */
	protected function start_wrapper() {
		$atts = $this->get_attributes( 'wrapper' );

		return '<footer ' . $this->build_attributes( $atts ) . ' >';
	}

	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function scontent() {
		return $this->setting->get_param( 'content' );
	}

	/**
	 * Create the end of the wrapper HTML.
	 *
	 * @return string
	 */
	protected function end_wrapper() {
		$html = array();
		$html[] = '</footer>';
		// Output canceled HTML.
		$html[] = '<div class="clear">';
		$html[] = '</div>';
		$html[] = '</div>';
	}

}
