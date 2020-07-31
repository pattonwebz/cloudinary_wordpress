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
	 * Contains the sync types and  callbacks.
	 *
	 * @var array
	 */
	protected $sync_types;

	/**
	 * Holds a list of unsynced images to push on end.
	 *
	 * @var array
	 */
	private $to_sync = array();

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
		static $signatures = array(); // cache signatures.
		if ( ! empty( $signatures[ $attachment_id ] ) ) {
			$return = $signatures[ $attachment_id ];
		} else {
			$sync_base                    = $this->sync_base( $attachment_id );
			$return                       = array_map(
				function ( $item ) {
					if ( is_array( $item ) ) {
						$item = wp_json_encode( $item );
					}

					return md5( $item );
				},
				$sync_base
			);
			$signatures[ $attachment_id ] = $return;
		}

		return $return;
	}

	/**
	 * Check if an asset can be synced.
	 *
	 * @param int    $attachment_id The attachment ID to check if it  can be synced.
	 * @param string $type          The type of sync to attempt.
	 *
	 * @return bool
	 */
	public function can_sync( $attachment_id, $type = 'file' ) {

		/**
		 * Filter to allow changing if an asset is allowed to be synced.
		 * Return a WP Error with reason why it can't be synced.
		 *
		 * @param int $attachment_id The attachment post ID.
		 *
		 * @return bool|\WP_Error
		 */
		return apply_filters( 'cloudinary_can_sync_asset', true, $attachment_id, $type );
	}

	/**
	 * Get the current sync signature of an asset.
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return array|bool
	 */
	public function get_signature( $attachment_id ) {
		static $signatures = array(); // Cache signatures already fetched.

		$return = array();
		if ( ! empty( $signatures[ $attachment_id ] ) ) {
			$return = $signatures[ $attachment_id ];
		} else {
			$signature = $this->managers['media']->get_post_meta( $attachment_id, self::META_KEYS['signature'], true );
			if ( ! empty( $signature ) ) {
				$base_signatures              = $this->generate_signature( $attachment_id );
				$signatures[ $attachment_id ] = wp_parse_args( $signature, $base_signatures );
				$return                       = $signatures[ $attachment_id ];
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
	 */
	public function setup_sync_base_struct() {

		$base_struct = array(
			'file'        => array(
				'generate' => 'get_attached_file',
				'sync'     => array(
					'priority' => 0,
					'callback' => array( $this->managers['upload'], 'upload_asset' ),
				),
			),
			'folder'      => array(
				'generate' => array( $this->managers['media'], 'get_cloudinary_folder' ),
				'sync'     => array(
					'priority' => 10,
					'callback' => array( $this->managers['upload'], 'upload_asset' ),
				),
			),
			'public_id'   => array(
				'generate' => array( $this->managers['media'], 'get_public_id' ),
				'sync'     => array(
					'priority' => 20,
					'callback' => array( $this->managers['push'], 'push_attachments' ), // Rename
				),
			),
			'suffix'      => array(
				'generate' => array( $this, 'get_suffix_maybe' ),
				'sync'     => array(
					'priority' => 10,
					'callback' => array( $this->managers['upload'], 'upload_asset' ),
				),
			),
			'breakpoints' => array(
				'generate' => array( $this->managers['media'], 'get_breakpoint_options' ),
				'sync'     => array(
					'priority' => 25,
					'callback' => array( $this->managers['upload'], 'explicit_update' ),
				),
			),
			'options'     => array(
				'generate' => array( $this->managers['media'], 'get_upload_options' ),
				'sync'     => array(
					'priority' => 30,
					'callback' => array( $this->managers['upload'], 'context_update' ),
				),
			),
			'cloud_name'  => array(
				'generate' => array( $this->managers['connect'], 'get_cloud_name' ),
				'sync'     => array(
					'priority' => 5,
					'callback' => array( $this->managers['upload'], 'upload_asset' ),
				),
			),
		);

		/**
		 * Filter the sync base structure to allow other plugins to sync component callbacks.
		 *
		 * @param array $base_struct The base sync structure.
		 *
		 * @return array
		 */
		$base_struct = apply_filters( 'cloudinary_sync_base_struct', $base_struct );

		// Apply a default to ensure parts exist.s
		$structs = array_map(
			function ( $component ) {
				$sync_default = array(
					'priority' => 50,
					'callback' => '__return_null',
				);
				$default      = array(
					'generate' => '__return_null',
					'sync'     => array(),
				);

				// Ensure correct struct.
				$component                     = wp_parse_args( $component, $default );
				$component['sync']             = wp_parse_args( $component['sync'], $sync_default );
				$component['sync']['priority'] = is_int( $component['sync']['priority'] ) ? (int) $component['sync']['priority'] : 50;

				return $component;
			},
			$base_struct
		);

		$this->sync_base_struct = $structs;
	}

	/**
	 * Setup the sync types in priority order based on sync struct.
	 */
	public function setup_sync_types() {

		$sync_types = array();
		foreach ( $this->sync_base_struct as $type => $struct ) {
			if ( is_callable( $struct['sync']['callback'] ) ) {
				$sync_types[ $type ] = $struct['sync']['priority'];
			}
		}

		asort( $sync_types );

		$this->sync_types = $sync_types;
	}

	/**
	 * Get the method to do a sync for the specified type.
	 *
	 * @param $type
	 *
	 * @return bool|mixed
	 */
	public function get_sync_method( $type ) {
		$method = false;
		if ( isset( $this->sync_base_struct[ $type ]['sync']['callback'] ) ) {
			$method = $this->sync_base_struct[ $type ]['sync']['callback'];
		}

		return $method;
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
		foreach ( $this->sync_base_struct as $key => $struct ) {
			if ( isset( $struct['generate'] ) ) {
				$return[ $key ] = null;
				if ( is_callable( $struct['generate'] ) ) {
					$return[ $key ] = call_user_func( $struct['generate'], $post );
				}
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
	 * Prepare an asset to be synced, maybe.
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return void
	 */
	public function maybe_prepare_sync( $attachment_id ) {

		$type = $this->get_sync_type( $attachment_id );
		// Check if has an error (ususally due to file quotas).
		$can_sync = $this->can_sync( $attachment_id, $type );
		if ( is_wp_error( $can_sync ) ) {
			$this->managers['media']->get_post_meta( $attachment_id, self::META_KEYS['sync_error'], $can_sync->get_error_message() );

			return;
		}
		if ( ! $this->is_pending( $attachment_id ) && apply_filters( 'cloudinary_on_demand_sync_enabled', $this->enabled, $attachment_id ) ) {
			$this->add_to_sync( $attachment_id );
		}
	}

	/**
	 * Get the type of sync, with the lowest priority for this asset.
	 *
	 * @param $attachment_id
	 *
	 * @return mixed|string|\WP_Error
	 */
	public function get_sync_type( $attachment_id ) {
		$attachment_signature = $this->get_signature( $attachment_id );
		// Set default sync type.
		$type = array_shift( array_keys( $this->sync_types ) ); // Lowest sync type should always be a full sync.
		if ( ! empty( $attachment_signature ) ) {
			// Has signature find differences and use specific sync method.
			$required_signature = $this->generate_signature( $attachment_id );
			if ( is_array( $required_signature ) ) {
				$sync_items = array_diff( $required_signature, $attachment_signature );
				$ordered    = array_intersect_key( $this->sync_types, $sync_items );
				$type       = array_shift( array_keys( $ordered ) );
			}
		}

		// Nothing to sync.
		if ( empty( $type ) ) {
			$type = new \WP_Error( 'attachment_synced', __( 'Attachment is already fully synced.', 'cloudinary' ) );
		}

		return $type;
	}

	/**
	 * Checks the status of the media item.
	 *
	 * @param array $status        Array of state and note.
	 * @param int   $attachment_id The attachment id.
	 *
	 * @return array
	 */
	public function filter_status( $status, $attachment_id ) {

		if ( $this->is_pending( $attachment_id ) ) {
			$status['state'] = 'warning';
			$status['note']  = __( 'Upload sync pending', 'cloudinary' );
		}

		// Check if there's an error.
		$has_error = $this->managers['media']->get_post_meta( $attachment_id, Sync::META_KEYS['sync_error'], true );
		if ( ! empty( $has_error ) ) {
			$status['state'] = 'error';
			$status['note']  = $has_error;
		}

		return $status;
	}

	/**
	 * Check if the attachment is pending an upload sync.
	 *
	 * @param int $attachment_id The attachment ID to check.
	 *
	 * @return bool
	 */
	public function is_pending( $attachment_id ) {
		// Check if it's not already in the to sync array.
		if ( ! in_array( $attachment_id, $this->to_sync, true ) ) {
			$is_pending = $this->managers['media']->get_post_meta( $attachment_id, Sync::META_KEYS['pending'], true );
			if ( empty( $is_pending ) || $is_pending < time() - 5 * 60 ) {
				// No need to delete pending meta, since it will be updated with the new timestamp anyway.
				return false;
			}
		}

		return true;
	}

	/**
	 * Add an attachment ID to the to_sync array.
	 *
	 * @param int $attachment_id The attachment ID to add.
	 */
	public function add_to_sync( $attachment_id ) {
		if ( ! in_array( $attachment_id, $this->to_sync, true ) && ! $this->is_pending( $attachment_id ) ) {
			// Flag image as pending to prevent duplicate upload.
			$this->managers['media']->update_post_meta( $attachment_id, Sync::META_KEYS['pending'], time() );
			//$this->managers['media']->update_post_meta( $attachment_id, Sync::META_KEYS['folder_sync'], true );
			$this->to_sync[] = $attachment_id;
		}
	}

	/**
	 * Update a part of the signature based on type.
	 *
	 * @param int    $attachment_id The attachment ID.
	 * @param string $type          The type of sync.
	 */
	public function update_signature( $attachment_id, $type ) {

		$current_signature   = $this->get_signature( $attachment_id );
		$expecting           = $this->generate_signature( $attachment_id );
		$current_sync_method = $this->sync_base_struct[ $type ]['sync']['callback'];
		$meta                = wp_get_attachment_metadata( $attachment_id, true );

		// Go over all other types that share the same sync method and include them here.
		foreach ( $this->sync_base_struct as $sync_type => $struct ) {
			if ( $struct['sync']['callback'] === $current_sync_method ) {
				$current_signature[ $sync_type ] = $expecting[ $sync_type ];
			}
		}
		$meta[ Sync::META_KEYS['cloudinary'] ][ Sync::META_KEYS['signature'] ] = $current_signature;
		wp_update_attachment_metadata( $attachment_id, $meta );

	}

	/**
	 * Initialize the background sync on requested images needing to be synced.
	 */
	public function init_background_upload() {
		if ( ! empty( $this->to_sync ) ) {
			$params = array(
				'attachment_ids' => $this->to_sync,
			);
			$this->managers['api']->background_request( 'process', $params );
		}
	}

	/**
	 * Additional component setup.
	 */
	public function setup() {
		if ( $this->plugin->config['connect'] ) {

			// Show sync status.
			add_filter( 'cloudinary_media_status', array( $this, 'filter_status' ), 10, 2 );
			// Hook for on demand upload push.
			add_action( 'shutdown', array( $this, 'init_background_upload' ) );

			$this->managers['upload']->setup();
			$this->managers['delete']->setup();
			$this->managers['push']->setup();
			// Setup additional components.
			$this->managers['media']   = $this->plugin->components['media'];
			$this->managers['connect'] = $this->plugin->components['connect'];
			$this->managers['api']     = $this->plugin->components['api'];
			// Setup the sync_base_structure.
			$this->setup_sync_base_struct();
			// Setup sync types.
			$this->setup_sync_types();
		}
	}
}
