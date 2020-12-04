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

	protected $blueprint = 'wrap/';

	protected function wrap( $stuct ) {

		$stuct['element'] = 'header';
		$stuct['content'] = $this->content();

		return $stuct;
	}


	/**
	 * Creates the Content/Input HTML.
	 *
	 * @return string
	 */
	protected function content() {
		return $this->setting->get_param( 'content' );
	}

}
