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
	 * Holds the components build blueprint.
	 *
	 * @var string
	 */
	protected $blueprint = 'submit_button';

	/**
	 * Filter the link parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function submit_button( $struct ) {

		$struct['element']             = 'button';
		$struct['attributes']['type']  = $this->type;
		$struct['attributes']['value'] = $this->setting->get_param( 'label', __( 'Submit', 'cloudinary' ) );
		$struct['attributes']['class'] = array(
			'button',
			'button-primary',
		);

		return $struct;
	}
}
