<?php
/**
 * Panel UI Component.
 *
 * @package Cloudinary
 */

namespace Cloudinary\UI\Component;

use Cloudinary\UI\Component;
use Cloudinary\Settings\Setting;

/**
 * Class Component
 *
 * @package Cloudinary\UI
 */
class Panel extends Component {

	/**
	 * Holds the components build blueprint.
	 *
	 * @var string
	 */
	protected $blueprint = 'wrap|header|icon/|title/|collapse/|/header|body|/body|section/|/wrap';

	/**
	 * Gets the active child setting.
	 *
	 * @return Setting
	 */
	protected function get_active_setting() {
		$active_setting = '_tmp_fallback';
		if ( $this->setting->has_settings() ) {
			$active_setting = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
			if ( ! $this->setting->has_setting( $active_setting ) ) { // Tab is invalid or not set, check if in a POST.
				$active_setting = filter_input( INPUT_POST, 'tab', FILTER_SANITIZE_STRING );
				if ( ! $this->setting->has_setting( $active_setting ) ) { // Tab is invalid or not set, load the default/first tab.
					$settings       = $this->setting->get_setting_slugs();
					$active_setting = array_shift( $settings );
				}
			}
		}

		return $this->setting->get_setting( $active_setting );
	}

	/**
	 * Filter the section parts structure.
	 *
	 * @param array $struct The array structure.
	 *
	 * @return array
	 */
	protected function section( $struct ) {
		$struct            = parent::settings( $struct );
		$struct['element'] = null;

		return $struct;
	}
}

