<?php
/**
 * Page UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;

/**
 * Page Class Component
 *
 * @package Cloudinary\UI
 */
class Page extends Panel {

	/**
	 * Holds the components build blueprint.
	 *
	 * @var string
	 */
	protected $blueprint = 'wrap|header/|tabs/|form|body|/body|settings/|/form|/wrap';

	/**
	 * Filter the form parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function form( $struct ) {
		$form_atts            = array(
			'method'     => 'post',
			'action'     => 'options.php',
			'novalidate' => 'novalidate',
		);
		$struct['attributes'] = array_merge( $form_atts, $struct['attributes'] );
		$struct['children']   = $this->page_actions();
		$struct['content']    = wp_nonce_field( $this->get_option_name() . '-options', '_wpnonce', true, false );

		return $struct;
	}

	/**
	 * Filter the header part structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function header( $struct ) {
		if ( $this->setting->has_param( 'page_header' ) ) {
			$struct['element'] = null;
			$struct['content'] = $this->setting->get_param( 'page_header' )->render_component();
		}

		return $struct;
	}

	/**
	 * Creates the options page and action inputs.
	 *
	 * @return array
	 */
	protected function page_actions() {

		$option_name = $this->get_option_name();
		settings_errors( $option_name );

		$inputs = array(
			'option_page' => $this->get_part( 'input' ),
			'action'      => $this->get_part( 'input' ),
		);
		// Set the attributes for the field.
		$option_atts                         = array(
			'type'  => 'hidden',
			'name'  => 'option_page',
			'value' => $option_name,
		);
		$inputs['option_page']['attributes'] = $option_atts;

		// Set the attributes for the field action.
		$action_atts = array(
			'type'  => 'hidden',
			'name'  => 'action',
			'value' => 'update',
		);
		// Create the action input.
		$inputs['action']['attributes'] = $action_atts;

		return $inputs;
	}

	/**
	 * Get the option name for this component.
	 *
	 * @return string
	 */
	protected function get_option_name() {
		// Get the options setting input field.
		$option_name = $this->setting->get_option_name();
		if ( $this->setting->has_param( 'has_tabs' ) ) {
			$option_name = $this->get_active_setting()->get_option_name();
		}

		return $option_name;
	}

	/**
	 * Filter the settings based on active tab.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function settings( $struct ) {

		if ( $this->setting->has_param( 'has_tabs' ) ) {
			$struct['content'] = $this->get_active_setting()->render_component();
		}

		return parent::settings( $struct );
	}
}

