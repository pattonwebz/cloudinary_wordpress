<?php
/**
 * Text UI Component.
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
class Text extends Component {

	/**
	 * Holds the components build blueprint.
	 *
	 * @var string
	 */
	protected $blueprint = 'icon/|title/|tooltip/|prefix/|input/|suffix/|description/';

	/**
	 * Filter the link parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function input( $struct ) {

		$struct['element']               = 'input';
		$struct['attributes']['type']    = $this->type;
		$struct['attributes']['name']    = $this->get_name();
		$struct['attributes']['id']      = $this->setting->get_slug();
		$struct['attributes']['value']   = $this->setting->get_value();
		$struct['attributes']['class'][] = 'regular-text';
		if ( $this->setting->has_param( 'required' ) ) {
			$struct['attributes']['required'] = 'required';
		}

		return $struct;
	}

	/**
	 * Get the field name.
	 *
	 * @return string
	 */
	protected function get_name() {
		return $this->setting->get_option_name() . '[' . $this->setting->get_slug() . ']';
	}

	/**
	 * Sanitize the value.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return string
	 */
	public function sanitize_value( $value ) {
		return sanitize_text_field( $value );
	}
}
