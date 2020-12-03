<?php
/**
 * Link UI Component.
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
class Link extends Component {


	/**
	 * Creates the Link HTML.
	 *
	 * @return string
	 */
	protected function content() {
		$atts           = $this->get_attributes( 'content' );
		$atts['href']   = $this->setting->get_param( 'url', '#' );
		$atts['target'] = $this->setting->get_param( 'target', '_blank' );
		$content        = $this->setting->get_param( 'content', basename( $atts['href'] ) );

		return '<a ' . $this->build_attributes( $atts ) . ' />' . $content . '</a>';
	}

}
