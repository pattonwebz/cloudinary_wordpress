<?php
/**
 * Abstract UI Component.
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
class panel extends Component {

	/**
	 * Gets the active child setting.
	 *
	 * @return \Cloudinary\Settings\Setting
	 */
	protected function get_active_setting() {
		$active_tab = '_tmp_fallback';
		if ( $this->setting->has_settings() ) {
			$active_tab = $this->setting->get_setting_slugs()[0];
			if ( $this->setting->has_param( 'active_tab' ) ) {
				$active_tab = $this->setting->get_param( 'active_tab' );
			}
		}

		return $this->setting->get_setting( $active_tab );
	}
}

