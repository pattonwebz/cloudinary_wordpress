<?php
/**
 * Interface for settings based classes.
 *
 * @package Cloudinary
 */

namespace Cloudinary\Component;

/**
 * Defines an object that requires settings.
 */
interface Settings {

	/**
	 * Register Settings.
	 *
	 * @param \Cloudinary\Settings\Setting $setting The core setting.
	 */
	public function register_settings( $setting );

}
