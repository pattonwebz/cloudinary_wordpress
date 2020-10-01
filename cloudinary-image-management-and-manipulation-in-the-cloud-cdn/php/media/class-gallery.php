<?php
/**
 * Manages Gallery Widget and Block settings.
 *
 * @package Cloudinary
 */

namespace Cloudinary\Media;

use Cloudinary\Media;

/**
 * Class Filter.
 *
 * Handles filtering of HTML content.
 */
class Gallery implements \JsonSerializable {
	/**
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
	 * Init gallery.
	 *
	 * @param Media $media
	 */
	public function __construct( Media $media ) {
		$this->media = $media;
		$this->setup_hooks();
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

		$config        = $this->media->plugin->config['settings']['gallery'];
		$custom_config = $config['custom_settings'];

		// unset things that don't need to be in the final json.
		unset( $config['enable_gallery'], $config['custom_settings'] );

		$config = $this->prepare_config( $config );
		$config = $this->parse_dot_notation( $config );
		$config = $this->array_filter_recursive(
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
			$config        = array_merge( $config, $custom_config );
		}

		$this->config = $config;

		return $config;
	}

	/**
	 * Detects array keys with dot notation and expands them to form a new multi-dimensional array.
	 *
	 * @param  array $input The array that will be processed.
	 *
	 * @return array
	 */
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

	/**
	 * Filter an array recursively
	 *
	 * @param array          $input    The array to filter.
	 * @param callable|null  $callback The callback to run for filtering.
	 *
	 * @return array
	 */
	public function array_filter_recursive( array $input, $callback = null ) {
		foreach ( $input as &$value ) {
			if ( is_array( $value ) ) {
				$value = $this->array_filter_recursive( $value, $callback );
			}
		}

		return array_filter( $input, $callback );
	}

	/**
	 * Convert an array's keys to camelCase.
	 *
	 * @param array $input The array input that will have its keys camelcased.
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
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return wp_json_encode( $this->get_config() );
	}

	/**
	 * Register assets for the gallery.
	 */
	public function register_scripts_styles() {
		wp_enqueue_script( 'cld-gallery', 'https://product-gallery.cloudinary.com/all.js', null, '1', true );
	}

	public function override_woocommerce_gallery( $html, $attachment_id ) {
		$public_id = $this->media->get_public_id( $attachment_id, true );
		return '<script>galleryOptions.mediaAssets.push("' . esc_js( $public_id ) . '");</script>';
	}

	public function add_config_to_head() {
		// phpcs:disable
		?>
		<script>
			var galleryOptions = JSON.parse( '<?php echo $this->jsonSerialize(); ?>' )
		</script>
		<?php
		// phpcs:enable
	}

	public function prepare_gallery_assets( $html, $handle ) {
		if ( 'cld-gallery' === $handle ) {
			$html .= '<script>cloudinary.galleryWidget(galleryOptions).render();</script>';
		}

		return $html;
	}

	/**
	 * Setup hooks for the gallery.
	 */
	public function setup_hooks() {
		add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'override_woocommerce_gallery' ), 10, 2 );
		add_filter( 'wp_head', array( $this, 'add_config_to_head' ) );
		add_filter( 'script_loader_tag', array( $this, 'prepare_gallery_assets' ), 10, 2 );

		$this->register_scripts_styles();
	}
}
