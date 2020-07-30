<?php
/**
 * Sync manages all of the sync components for the Cloudinary plugin.
 *
 * @package Cloudinary
 */

namespace Cloudinary;

use Cloudinary\Component\Assets;
use Cloudinary\Component\Setup;
use Cloudinary\Sync\Delete_Sync;
use Cloudinary\Sync\Download_Sync;
use Cloudinary\Sync\Push_Sync;
use Cloudinary\Sync\Upload_Queue;
use Cloudinary\Sync\Upload_Sync;

/**
 * Class Sync
 */
class Sync implements Setup, Assets {

	/**
	 * Holds the plugin instance.
	 *
	 * @since   0.1
	 *
	 * @var     Plugin Instance of the global plugin.
	 */
	public $plugin;

	/**
	 * Contains all the different sync components.
	 *
	 * @var array A collection of sync components.
	 */
	public $managers;

	/**
	 * Contains the sync base structure and callbacks.
	 *
	 * @var array
	 */
	protected $sync_base_struct;

	/**
	 * Holds the meta keys for sync meta to maintain consistency.
	 */
	const META_KEYS = array(
		'pending'        => '_cloudinary_pending',
		'signature'      => '_sync_signature',
		'version'        => '_cloudinary_version',
		'breakpoints'    => '_cloudinary_breakpoints',
		'public_id'      => '_public_id',
		'transformation' => '_transformations',
		'sync_error'     => '_sync_error',
		'cloudinary'     => '_cloudinary_v2',
		'folder_sync'    => '_folder_sync',
		'suffix'         => '_suffix',
		'syncing'        => '_cloudinary_syncing',
		'downloading'    => '_cloudinary_downloading',
	);

	/**
	 * Push_Sync constructor.
	 *
	 * @param Plugin $plugin Global instance of the main plugin.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin               = $plugin;
		$this->managers['push']     = new Push_Sync( $this->plugin );
		$this->managers['upload']   = new Upload_Sync( $this->plugin );
		$this->managers['download'] = new Download_Sync( $this->plugin );
		$this->managers['delete']   = new Delete_Sync( $this->plugin );
		$this->managers['queue']    = new Upload_Queue( $this->plugin );
	}

	/**
	 * Setup assets/scripts.
	 */
	public function enqueue_assets() {
		if ( $this->plugin->config['connect'] ) {
			$data = array(
				'restUrl' => esc_url_raw( rest_url() ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			);
			wp_add_inline_script( 'cloudinary', 'var cloudinaryApi = ' . wp_json_encode( $data ), 'before' );
		}
	}

	/**
	 * Register Assets.
	 */
	public function register_assets() {
	}


	/**
	 * Is the component Active.
	 */
	public function is_active() {
		return $this->plugin->components['settings']->is_active() && 'sync_media' === $this->plugin->components['settings']->active_tab();
	}

	/**
	 * Checks if an asset is synced and up to date.
	 *
	 * @param int $post_id The post id to check.
	 *
	 * @return bool
	 */
	public function is_synced( $post_id ) {
		$signature = $this->get_signature( $post_id );
		$expecting = $this->generate_signature( $post_id );

		if ( ! empty( $signature ) && ! empty( $expecting ) && $expecting === $signature ) {
			return true;
		}

		if ( $this->plugin->components['settings']->is_auto_sync_enabled() && apply_filters( 'cloudinary_flag_sync', '__return_false' ) && ! get_post_meta( $post_id, Sync::META_KEYS['downloading'], true ) ) {
			update_post_meta( $post_id, Sync::META_KEYS['syncing'], true );
		}

		return false;
	}

	/**
	 * Generate a signature based on whats required for a full sync.
	 *
	 * @param int $attachment_id The Attachment id to generate a signature for.
	 *
	 * @return string|bool
	 */
	public function generate_signature( $attachment_id ) {
		// Check if has an error (ususally due to file quotas).
		$can_sync = $this->can_sync( $attachment_id );
		if ( is_wp_error( $can_sync ) ) {
			$this->plugin->components['media']->get_post_meta( $attachment_id, self::META_KEYS['sync_error'], $can_sync->get_error_message() );

			return false;
		}
		$sync_base = $this->sync_base( $attachment_id );
		$return    = array_map(
			function ( $item ) {
				if ( is_array( $item ) ) {
					$item = wp_json_encode( $item );
				}

				return md5( $item );
			},
			$sync_base
		);

		return $return;
	}

	/**
	 * Check if an asset can be synced.
	 *
	 * @param int $attachment_id The attachment ID to check if it  can be synced.
	 *
	 * @return bool
	 */
	public function can_sync( $attachment_id ) {

		/**
		 * Filter to allow changing if an asset is allowed to be synced.
		 * Return a WP Error with reason why it can't be synced.
		 *
		 * @param int $attachment_id The attachment post ID.
		 *
		 * @return bool|\WP_Error
		 */
		return apply_filters( 'cloudinary_can_sync_asset', true, $attachment_id );
	}

	/**
	 * Get the current sync signature of an asset.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return array|bool
	 */
	public function get_signature( $post_id ) {
		static $signatures = array(); // Cache signatures already fetched.

		$return = false;
		if ( ! empty( $signatures[ $post_id ] ) ) {
			$return = $signatures[ $post_id ];
		} else {
			$signature = $this->plugin->components['media']->get_post_meta( $post_id, self::META_KEYS['signature'], true );
			if ( ! empty( $signature ) ) {
				$base_signatures        = $this->generate_signature( $post_id );
				$signatures[ $post_id ] = wp_parse_args( $signature, $base_signatures );
				$return                 = $signatures[ $post_id ];
			}
		}

		return $return;
	}

	/**
	 * Generate a new Public ID for an asset.
	 *
	 * @param int $attachment_id The attachment ID for the new public ID.
	 *
	 * @return string|null
	 */
	public function generate_public_id( $attachment_id ) {
		$settings   = $this->plugin->config['settings'];
		$cld_folder = trailingslashit( $settings['sync_media']['cloudinary_folder'] );
		$file       = get_attached_file( $attachment_id );
		$file_info  = pathinfo( $file );
		$public_id  = $cld_folder . $file_info['filename'];

		return $public_id;
	}

	/**
	 * Maybe add a suffix to the public ID if it's not unique.
	 *
	 * @param string      $public_id     The public ID to maybe add a suffix.
	 * @param int         $attachment_id The attachment ID.
	 * @param string|null $suffix        The suffix to maybe add.
	 *
	 * @return string The public ID.
	 */
	public function add_suffix_maybe( $public_id, $attachment_id, $suffix = null ) {

		// Test if asset exists by calling just the head on the asset url, to prevent API rate limits.
		$url         = $this->plugin->components['connect']->api->cloudinary_url( $public_id . $suffix );
		$req         = wp_remote_head( $url, array( 'body' => array( 'rdm' => wp_rand( 100, 999 ) ) ) );
		$asset_error = strtolower( wp_remote_retrieve_header( $req, 'x-cld-error' ) );
		$code        = wp_remote_retrieve_response_code( $req );

		// If the request is not a 404 & does not have a cld-error header stating resource not found, it exists and should be checked that it's not a resync or generate a prefixed ID.
		if ( 404 !== $code && false === strpos( $asset_error, 'resource not found' ) ) {

			// Get the attachment type.
			if ( wp_attachment_is( 'image', $attachment_id ) ) {
				$type = 'image';
			} elseif ( wp_attachment_is( 'video', $attachment_id ) ) {
				$type = 'video';
			} elseif ( wp_attachment_is( 'audio', $attachment_id ) ) {
				$type = 'audio';
			} else {
				// not supported.
				return null;
			}
			$cld_asset = $this->plugin->components['connect']->api->get_asset_details( $public_id, $type );
			if ( ! is_wp_error( $cld_asset ) && ! empty( $cld_asset['public_id'] ) ) {
				$context_id = null;

				// Exists, check to see if this asset originally belongs to this ID.
				if ( ! empty( $cld_asset['context'] ) && ! empty( $cld_asset['context']['custom'] ) && ! empty( $cld_asset['context']['custom']['wp_id'] ) ) {
					$context_id = (int) $cld_asset['context']['custom']['wp_id'];
				}

				// Generate new ID only if context ID is not related.
				if ( $context_id !== $attachment_id ) {
					// Generate a new ID with a uniqueID prefix.
					$suffix = '-' . uniqid();

					// Return new potential suffixed ID.
					return $this->add_suffix_maybe( $public_id, $attachment_id, $suffix );
				}
			}
		}

		return $suffix;
	}

	/**
	 * Get the sync_setup_base of part callbacks.
	 *
	 * @return array
	 */
	public function setup_sync_base_struct() {

		$base_struct = array(
			'file'        => 'get_attached_file',
			'folder'      => array( $this->managers['media'], 'get_cloudinary_folder' ),
			'public_id'   => array( $this->managers['media'], 'get_public_id' ),
			'suffix'      => array( $this, 'get_suffix_maybe' ),
			'breakpoints' => array( $this->managers['media'], 'get_breakpoint_options' ),
			'options'     => array( $this->managers['media'], 'get_upload_options' ),
			'cloud_name'  => array( 'Media', 'get_cloud_name' ),
		);

		return apply_filters( 'cloudinary_sync_base_struct', $base_struct );
	}

	/**
	 * Get the core Sync Base.
	 *
	 * @param int|\WP_Post $post The attachment to prepare.
	 *
	 * @return array|\WP_Error
	 */
	public function sync_base( $post ) {

		if ( ! $this->managers['media']->is_media( $post ) ) {
			return new \WP_Error( 'attachment_post_expected', __( 'An attachment post was expected.', 'cloudinary' ) );
		}

		$return = array();
		foreach ( $this->sync_base_struct as $key => $callback ) {
			$return[ $key ] = null;
			if ( is_callable( $callback ) ) {
				$return[ $key ] = call_user_func( $callback, $post );
			}
		}

		/**
		 * Filter the sync base to allow other plugins to add requested sync components for the sync signature.
		 *
		 * @param array    $options The options array.
		 * @param \WP_Post $post    The attachment post.
		 * @param \Cloudinary\Sync The sync object instance.
		 *
		 * @return array
		 */
		$return = apply_filters( 'cloudinary_sync_base', $return, $post );

		return $return;
	}

	/**
	 * Additional component setup.
	 */
	public function setup() {
		if ( $this->plugin->config['connect'] ) {
			$this->managers['upload']->setup();
			$this->managers['delete']->setup();
			$this->managers['push']->setup();
			// Setup additional components.
			$this->managers['media'] = $this->plugin->components['media'];
			// Setup the sync_base_structure.
			$this->sync_base_struct = $this->setup_sync_base_struct();
		}
	}
}
