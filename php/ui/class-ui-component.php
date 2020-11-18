<?php
/**
 * Abstract UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI;

/**
 * Class Component
 *
 * @package Cloudinary\UI
 */
abstract class Component {

	/**
	 * Component Type.
	 *
	 * @var string
	 */
	public $type = 'text_input';

	/**
	 * @var mixed
	 */
	protected $value;

	public function get_value() {

	}

	/**
	 * Sets the component value.
	 *
	 * @param string $value The value to set.
	 */
	public function set_value( $value ) {
		$this->value = $this->sanitize_value( $value );
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

	/**
	 * Renders the component.
	 */
	public function render() {

	}

}
