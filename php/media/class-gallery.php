<?php
/**
 * Manages Gallery Widget and Block settings.
 *
 * @package Cloudinary
 */

namespace Cloudinary\Media;

use Cloudinary\Media;
use Cloudinary\REST_API;
use Cloudinary\Utils;

/**
 * Class Filter.
 *
 * Handles filtering of HTML content.
 */
class Gallery {

	/**
	 * The enqueue script handle for the gallery widget lib.
	 *
	 * @var string
	 */
	const GALLERY_LIBRARY_HANDLE = 'cld-gallery';

	/**
	 * The gallery widget lib cdn url.
	 *
	 * @var string
	 */
	const GALLERY_LIBRARY_URL = 'https://product-gallery.cloudinary.com/all.js';

	/**
	 * Holds instance of the Media class.
	 *
	 * @var Media
	 */
	protected $media;

	/**
	 * Holds the current config.
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Holds the original, unparsed config.
	 *
	 * @var array
	 */
	protected $original_config = array();

	/**
	 * Init gallery.
	 *
	 * @param Media $media
	 */
	public function __construct( Media $media ) {
		$this->media           = $media;
		$this->original_config = isset( $media->plugin->config['settings']['gallery'] ) ? $media->plugin->config['settings']['gallery'] : null;

		if ( $this->gallery_enabled() ) {
			$this->setup_hooks();
		}
	}

	/**
	 * Gets the gallery settings in the expected json format.
	 *
	 * @return array
	 */
	public function get_config() {
		if ( count( $this->config ) ) {
			return $this->config;
		}

		if ( ! count( $this->original_config ) ) {
			return array();
		}

		$config        = $this->original_config;
		$custom_config = $config['custom_settings'];

		// unset things that don't need to be in the final json.
		unset( $config['enable_gallery'], $config['custom_settings'] );

		$config = $this->prepare_config( $config );
		$config = Utils::expand_dot_notation( $config );
		$config = Utils::array_filter_recursive(
			$config,
			function ( $item ) {
				return ! empty( $item );
			}
		);

		$config['cloudName']   = $this->media->plugin->components['connect']->get_cloud_name();
		$config['container']   = '.woocommerce-product-gallery';
		$config['mediaAssets'] = array();

		if ( ! empty( $custom_config ) ) {
			$custom_config = json_decode( $custom_config, true );

			if ( ! empty( $custom_config ) ) {
				$config = array_merge( $config, $custom_config );
			}
		}

		$this->config = $config;

		return $config;
	}

	/**
	 * Convert an array's keys to camelCase and transform booleans.
	 * This is used for Cloudinary's gallery widget lib.
	 *
	 * @param array $input The array input that will have its keys camelcase-d.
	 *
	 * @return array
	 */
	public function prepare_config( array $input ) {
		foreach ( $input as $key => $val ) {
			if ( 'on' === $val || 'off' === $val ) {
				$val = 'on' === $val;
			} elseif ( is_numeric( $val ) ) {
				$val = (int) $val;
			}

			if ( 'none' !== $val ) {
				$new_key           = lcfirst( implode( '', array_map( 'ucfirst', explode( '_', $key ) ) ) );
				$input[ $new_key ] = $val;
			}

			unset( $input[ $key ] );
		}

		return $input;
	}

	/**
	 * Returns JSON format of the current config.
	 *
	 * @return string|null
	 */
	public function get_json() {
		return count( $this->get_config() ) ? wp_json_encode( $this->get_config() ) : null;
	}

	/**
	 * Register frontend assets for the gallery.
	 */
	public function enqueue_gallery_library() {
		wp_enqueue_script(
			self::GALLERY_LIBRARY_HANDLE,
			self::GALLERY_LIBRARY_URL,
			array(),
			$this->media->plugin->version,
			true
		);

		$config = $this->get_config();

		if ( $this->woocommerce_active() ) {
			$product = wc_get_product();

			if ( $product ) {
				$config['mediaAssets'] = $this->get_image_data( $product->get_gallery_image_ids() );
			}
		}

		wp_localize_script(
			self::GALLERY_LIBRARY_HANDLE,
			'cloudinaryGallery',
			array( 'config' => wp_json_encode( $config ) )
		);

		wp_enqueue_script(
			'cloudinary-gallery-init',
			$this->media->plugin->dir_url . 'assets/dist/gallery-init.js',
			array( self::GALLERY_LIBRARY_HANDLE ),
			$this->media->plugin->version,
			true
		);
	}

	/**
	 * Register blocked editor assets for the gallery.
	 */
	public function block_editor_scripts_styles() {
		$this->enqueue_gallery_library();

		wp_enqueue_style(
			'cloudinary-gallery-block-css',
			$this->media->plugin->dir_url . 'assets/dist/block-gallery.css',
			array(),
			$this->media->plugin->version
		);

		wp_enqueue_script(
			'cloudinary-gallery-block-js',
			$this->media->plugin->dir_url . 'assets/dist/block-gallery.js',
			array( 'wp-blocks', 'wp-editor', 'wp-element', self::GALLERY_LIBRARY_HANDLE ),
			$this->media->plugin->version,
			true
		);

		wp_localize_script(
			'cloudinary-gallery-block-js',
			'cloudinaryGalleryApi',
			array(
				'endpoint' => rest_url( REST_API::BASE . '/image_data' ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	/**
	 * Check if WooCommerce is active.
	 *
	 * @return bool
	 */
	protected function woocommerce_active() {
		return class_exists( 'WooCommerce' ) && function_exists( 'wc_get_product' );
	}

	/**
	 * Checks if the Cloudinary Gallery Widget is enabled.
	 *
	 * @return bool
	 */
	public function gallery_enabled() {
		return isset( $this->original_config['enable_gallery'] ) && 'on' === $this->original_config['enable_gallery'];
	}

	/**
	 * Fetches image public id and transformations.
	 *
	 * @param array|int[]|array[] $images An array of image IDs or a multi-dimensional array with url and id keys.
	 *
	 * @return array
	 */
	public function get_image_data( array $images ) {
		$image_data = array();

		foreach ( $images as $index => $image ) {
			$image_id  = is_int( $image ) ? $image : $image['id'];
			$image_url = is_int( $image ) ? $this->media->cloudinary_url( $image_id ) : $image['url'];

			if ( ! $this->media->sync->is_synced( $image_id ) ) {
				continue;
			}

			$image_data[ $index ]             = array();
			$image_data[ $index ]['publicId'] = $this->media->get_public_id( $image_id );

			$transformations = $this->media->get_transformations_from_string( $image_url );

			if ( $transformations ) {
				$image_data[ $index ]['transformation'] = array( 'transformation' => $transformations );
			}
		}

		return $image_data;
	}

	/**
	 * This rest endpoint handler will fetch the public_id and transformations off of a list of images.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function rest_cloudinary_image_data( \WP_REST_Request $request ) {
		$request_body = json_decode( $request->get_body(), true );

		if ( empty( $request_body ) || empty( $request_body['images'] ) ) {
			return new \WP_Error( 400, 'The "images" key must be present in the request body.' );
		}

		$image_data = $this->get_image_data( $request_body['images'] );

		return new \WP_REST_Response( $image_data );
	}

	/**
	 * Add endpoints to the \Cloudinary\REST_API::$endpoints array.
	 *
	 * @param array $endpoints Endpoints from the filter.
	 *
	 * @return array
	 */
	public function rest_endpoints( $endpoints ) {

		$endpoints['image_data'] = array(
			'method'              => \WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'rest_cloudinary_image_data' ),
			'args'                => array(),
			'permission_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
		);

		return $endpoints;
	}

	/**
	 * Setup hooks for the gallery.
	 */
	public function setup_hooks() {
		add_filter( 'cloudinary_api_rest_endpoints', array( $this, 'rest_endpoints' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_scripts_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_gallery_library' ) );

		if ( ! is_admin() && $this->woocommerce_active() ) {
			add_filter( 'woocommerce_single_product_image_thumbnail_html', '__return_empty_string' );
		}
	}
}
