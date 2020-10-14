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
class Gallery implements \JsonSerializable {

	/**
	 * @var string
	 */
	const GALLERY_LIBRARY_HANDLE = 'cld-gallery';

	/**
	 * @var string
	 */
	const GALLERY_LIBRARY_URL = 'https://product-gallery.cloudinary.com/all.js';

	/**
	 * Flag on whether this page is a WooCommerce product page with a gallery.
	 *
	 * @var bool
	 */
	protected $is_woo_page = false;

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
		$this->original_config = $media->plugin->config['settings']['gallery'];

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

		$config        = $this->media->plugin->config['settings']['gallery'];
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
			$config        = array_merge( $config, $custom_config );
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
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return wp_json_encode( $this->get_config() );
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
	}

	/**
	 * Register blocked editor assets for the gallery.
	 */
	public function block_editor_scripts_styles() {
		wp_enqueue_style(
			'cloudinary-gallery-block-css',
			$this->media->plugin->dir_url . 'assets/dist/block-gallery.css',
			array(),
			$this->media->plugin->version
		);

		wp_enqueue_script(
			'cloudinary-gallery-block-js',
			$this->media->plugin->dir_url . 'assets/dist/block-gallery.js',
			array( 'wp-blocks', 'wp-editor', 'wp-element' ),
			$this->media->plugin->version,
			true
		);

		wp_localize_script(
			'cloudinary-gallery-block-js',
			'defaultGalleryConfig',
			$this->get_config()
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
	 * This is a woocommerce gallery hook which is run for each gallery item.
	 *
	 * @param string $html
	 * @param int    $attachment_id
	 *
	 * @return string
	 */
	public function override_woocommerce_gallery( $html, $attachment_id ) {
		$this->is_woo_page = true;

		$cloudinary_url  = $this->media->filter->get_url_from_tag( $html );
		$public_id       = $this->media->get_public_id( $attachment_id );
		$transformations = $this->media->get_transformations_from_string( $cloudinary_url );

		$json = array(
			'publicId'       => $public_id,
			'transformation' => array( 'transformation' => $transformations ),
		);
		$json = wp_json_encode( $json );

		return "<script>galleryOptions.mediaAssets.push( JSON.parse( '{$json}' ) )</script>\n\t\t";
	}

	/**
	 * Sets galleryOptions JS variable which will be used to init the gallery.
	 */
	public function add_config_to_head() {
		// phpcs:disable
		?>
		<script>var galleryOptions = JSON.parse( '<?php echo $this->jsonSerialize(); ?>' )</script>
		<?php
		// phpcs:enable
	}

	/**
	 * Deals with rendering the gallery in a WooCommerce or Post Block setting.
	 *
	 * @param string $html   The HTML to output.
	 * @param string $handle Current JS handle.
	 *
	 * @return string
	 */
	public function prepare_gallery_assets( $html, $handle ) {
		if ( self::GALLERY_LIBRARY_HANDLE === $handle ) {
			$is_woo = $this->is_woo_page ? 'true' : 'false';
			$html  .= <<<SCRIPT_TAG
<script>
	var configElements = document.querySelectorAll( '[data-cloudinary-gallery-config]' );

	if ( configElements.length ) {
		configElements.forEach( function ( el ) {
			var configJson = decodeURIComponent( el.getAttribute( 'data-cloudinary-gallery-config' ) );
			var options = JSON.parse( configJson );
			options.container = '.' + options.container;
			cloudinary.galleryWidget( options ).render();
		});
	} else if ( {$is_woo} ) {
		cloudinary.galleryWidget( galleryOptions ).render();
	}

</script>
SCRIPT_TAG;
		}

		return $html;
	}

	/**
	 * Check if WooCommerce is active.
	 *
	 * @return bool
	 */
	protected function woocommerce_active() {
		return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );
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
	 * Setup hooks for the gallery.
	 */
	public function setup_hooks() {
		$this->enqueue_gallery_library();

		if ( $this->woocommerce_active() && ! is_admin() ) {
			add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'override_woocommerce_gallery' ), 10, 2 );
			add_filter( 'wp_head', array( $this, 'add_config_to_head' ) );
			add_filter( 'script_loader_tag', array( $this, 'prepare_gallery_assets' ), 10, 2 );
		} else {
			add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_scripts_styles' ) );
		}
	}
}
