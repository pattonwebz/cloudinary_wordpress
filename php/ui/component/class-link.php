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

	protected $blueprint = 'link_tag';


	protected function link_tag( $struct ) {


		$struct['element']              = 'a';
		$struct['content']              = $this->setting->get_param( 'content' );
		$struct['attributes']['href']   = $this->setting->get_param( 'url' );
		$struct['attributes']['target'] = '_blank';
		$struct['attributes']['class']  = array(
			'button',
			'button-primary',
		);

		return $struct;
	}
}
