<?php
/**
 * Base HTML UI Component.
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
class HTML extends Component {

	/**
	 * Holds the components build blueprint.
	 *
	 * @var string
	 */
	protected $blueprint = 'wrap|header|icon/|title/|collapse/|/header|body|/body|sections/|/wrap';

	protected function pre_render() {

	}

}
