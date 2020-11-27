<?php
/**
 * Abstract UI Component.
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
class Frame extends Component {


	/**
	 * Render the content, but only child settings.
	 *
	 * @return string
	 */
	public function render() {
		$html = array();
		// Do settings.
		if ( $this->setting->has_settings() ) {
			$html[] = $this->settings();
		}

		return self::compile_html( $html );
	}


}

