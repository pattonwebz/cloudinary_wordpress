<?php
/**
 * Manages Gallery Widget and Block settings.
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
	 * Holds the current global gallery settings.
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Holds the cloud name.
	 *
	 * @var string
	 */
	private $cloud_name;

	/**
	 * Init gallery.
	 *
	 * @param array  $config     Holds the current global gallery settings.
	 * @param string $cloud_name The cloud name.
	 */
	public function __construct( array $config, $cloud_name ) {
		$this->config     = $config;
		$this->cloud_name = $cloud_name;

		$this->setup_hooks();
	}

	/**
	 * Gets the gallery settings in the expected json format.
	 *
	 * @return string
	 */
	public function get_json() {
		$config              = $this->config;
		$config['cloudName'] = $this->cloud_name;
		$custom_config       = $config['custom_settings'];

		// unset things that don't need to be in the final json.
		unset( $config['enable_gallery'], $config['custom_settings'] );

		foreach ( $config as $key => $val ) {
			$new_key            = lcfirst( implode( '', array_map( 'ucfirst', explode( '_', $key ) ) ) );
			$config[ $new_key ] = $val;

			unset( $config[ $key ] );
		}

		$config = $this->array_filter_recursive(
			$this->parse_dot_notation( $config ),
			function ( $item ) {
				return ! empty( $item );
			}
		);

		if ( ! empty( $custom_config ) ) {
			$custom_config = json_decode( $custom_config, true );
			$config        = array_merge( $config, $custom_config );
		}

		return wp_json_encode( $config );
	}

	public function parse_dot_notation( array $input ) {
		$result = array();
		foreach ( $input as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = $this->parse_dot_notation( $value );
			}

			foreach ( array_reverse( explode( '.', $key ) ) as $key ) {
				$value = array( $key => $value );
			}

			$result = array_merge_recursive( $result, $value );
		}

		return $result;
	}

	public function array_filter_recursive( $input, $callback = null ) {
		foreach ( $input as &$value ) {
			if ( is_array( $value ) ) {
				$value = $this->array_filter_recursive( $value, $callback );
			}
		}

		return array_filter( $input, $callback );
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
	public function register_scripts_styles() {
		wp_register_script( 'cld-gallery', 'https://product-gallery.cloudinary.com/all.js', null, '1', true );
	}

	public function override_woocommerce_gallery( $html, $post_thumbnail_id ) {
		return 'lol';
	}

	/**
	 * Setup hooks for the gallery.
	 */
	public function setup_hooks() {
		add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'override_woocommerce_gallery' ), 10, 2 );
		$this->register_scripts_styles();
	}
}
