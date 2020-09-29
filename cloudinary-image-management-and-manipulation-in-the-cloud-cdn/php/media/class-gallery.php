<?php
/**
 * Manages Gallery Wdiget and Block settings.
 *
 * @package Cloudinary
 */

namespace Cloudinary\Media;

/**
 * Class Filter.
 *
 * Handles filtering of HTML content.
 */
class Gallery implements \JsonSerializable {

	/**
	 * Holds the current global gallery settinngs.
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Init gallery.
	 *
	 * @param array $config Holds the current global gallery settinngs.
	 */
	public function __construct( array $config ) {
		$this->config = $config;
		$this->setup_hooks();
	}

	/**
	 * Gets the gallery settings in the expected json format.
	 *
	 * @return string
	 */
	public function get_json() {
		$config = $this->config;

		if ( isset( $config['custom_settings'] ) ) {
			$custom_config = json_decode( $config['custom_settings'], true );
			$config        = array_merge( $config, $custom_config );

			unset( $config['custom_settings'] );
		}

		return wp_json_encode( $config );
	}

	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return $this->get_json();
	}

	/**
	 * Register assets for the gallery.
	 */
	public static function register_scripts_styles() {
		wp_register_script( 'cld-gallery', 'https://product-gallery.cloudinary.com/all.js', null, '1', true );
	}

	/**
	 * Setup hooks for the gallery.
	 */
	public function setup_hooks() {
		$this->register_scripts_styles();
	}
}
