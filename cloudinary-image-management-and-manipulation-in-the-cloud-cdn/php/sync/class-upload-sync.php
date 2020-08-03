<?php
/**
 * Upload Sync to Cloudinary.
 *
 * @package Cloudinary
 */

namespace Cloudinary\Sync;

use Cloudinary\Sync;

/**
 * Class Upload_Sync.
 *
 * Push media to Cloudinary on upload.
 */
class Upload_Sync {

	/**
	 * Holds the plugin instance.
	 *
	 * @since   0.1
	 *
	 * @var     \Cloudinary\Plugin Instance of the global plugin.
	 */
	private $plugin;

	/**
	 * The Push_Sync object.
	 *
	 * @var \Cloudinary\Sync\Push_Sync
	 */
	private $pusher;

	/**
	 * Holds the main Sync Class.
	 *
	 * @var \Cloudinary\Sync
	 */
	protected $sync;

	/**
	 * Holds the Connect Class.
	 *
	 * @var \Cloudinary\Connect
	 */
	protected $connect;

	/**
	 * Holds the Media Class.
	 *
	 * @var \Cloudinary\Media
	 */
	protected $media;

	/**
	 * This feature is enabled.
	 *
	 * @var bool
	 */
	private $enabled;

	/**
	 * Upload_Sync constructor.
	 *
	 * @param \Cloudinary\Plugin $plugin  The plugin.
	 * @param bool               $enabled Is this feature enabled.
	 * @param object             $pusher  An object that implements `push_attachments`. Default: null.
	 */
	public function __construct( \Cloudinary\Plugin $plugin, $enabled = false, $pusher = null ) {
		$this->plugin  = $plugin;
		$this->pusher  = $pusher;
		$this->enabled = $enabled;
	}

	/**
	 * Register any hooks that this component needs.
	 */
	private function register_hooks() {
		// Add action to upload.
		add_action( 'add_attachment', array( $this, 'push_on_upload' ), 10 );
		// Action Cloudinary id for on-demand upload sync.
		//		add_action( 'cloudinary_id', array( $this, 'prep_on_demand_upload' ), 9, 2 );
		// Hook into auto upload sync.
		add_filter( 'cloudinary_on_demand_sync_enabled', array( $this, 'auto_sync_enabled' ), 10, 2 );
		// Handle bulk and inline actions.
		add_filter( 'handle_bulk_actions-upload', array( $this, 'handle_bulk_actions' ), 10, 3 );
		// Add inline action.
		add_filter( 'media_row_actions', array( $this, 'add_inline_action' ), 10, 2 );

		// Add Bulk actions.
		add_filter( 'bulk_actions-upload', function ( $actions ) {
			$cloudinary_actions = array(
				'cloudinary-push' => __( 'Push to Cloudinary', 'cloudinary' ),
			);

			return array_merge( $cloudinary_actions, $actions );
		} );
	}

	/**
	 * Add an inline action for manual sync.
	 *
	 * @param array    $actions All actions.
	 * @param \WP_Post $post    The current post object.
	 *
	 * @return array
	 */
	function add_inline_action( $actions, $post ) {
		if ( current_user_can( 'delete_post', $post->ID ) ) {
			$action_url = add_query_arg(
				array(
					'action'   => 'cloudinary-push',
					'media[]'  => $post->ID,
					'_wpnonce' => wp_create_nonce( 'bulk-media' ),
				),
				'upload.php'
			);
			if ( ! $this->plugin->components['sync']->is_synced( $post->ID ) ) {
				$actions['cloudinary-push'] = sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					$action_url,
					/* translators: %s: Attachment title. */
					esc_attr( sprintf( __( 'Push to Cloudinary &#8220;%s&#8221;' ), 'asd' ) ),
					__( 'Push to Cloudinary', 'cloudinary' )
				);
			} else {
				$actions['cloudinary-push'] = sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					$action_url,
					/* translators: %s: Attachment title. */
					esc_attr( sprintf( __( 'Push to Cloudinary &#8220;%s&#8221;' ), 'asd' ) ),
					__( 'Re-sync to Cloudinary', 'cloudinary' )
				);
			}
		}

		return $actions;
	}

	/**
	 * Handles bulk actions for attachments.
	 *
	 * @param string $location The location to redirect after.
	 * @param string $action   The action to handle.
	 * @param array  $post_ids Post ID's to action.
	 *
	 * @return string
	 */
	public function handle_bulk_actions( $location, $action, $post_ids ) {

		switch ( $action ) {
			case 'cloudinary-push' :
				foreach ( $post_ids as $post_id ) {
					$this->media->delete_post_meta( $post_id, Sync::META_KEYS['signature'] );
					$this->sync->add_to_sync( $post_id );
				}
				break;
		}

		return $location;

	}

	/**
	 * Check if auto-sync is enabled.
	 *
	 * @param bool $enabled Flag to determine if autosync is enabled.
	 * @param int  $post_id The post id currently processing.
	 *
	 * @return bool
	 */
	public function auto_sync_enabled( $enabled, $post_id ) {
		if ( $this->plugin->components['settings']->is_auto_sync_enabled() ) {
			$enabled = true;
		}

		// Check if it was synced before to allow re-sync for changes.
		if ( ! empty( $this->plugin->components['sync']->get_signature( $post_id ) ) ) {
			$enabled = true;
		}

		return $enabled;
	}

	/**
	 * Push new attachment to Cloudinary on upload.
	 *
	 * @param int $post_id The post.
	 */
	public function push_on_upload( $post_id ) {

		// Only if this is a media file and feature is enabled.
		if ( $this->plugin->components['media']->is_media( $post_id ) && apply_filters( 'cloudinary_upload_sync_enabled', $this->enabled ) ) {
			// Lets do the background upload to keep the upload window as fast as possible.
			update_post_meta( $post_id, '_cloudinary_pending', time() ); // Make sure it doesn't get resynced.
			$params = array(
				'attachment_ids' => array( $post_id ),
			);
			$this->plugin->components['api']->background_request( 'process', $params );
		}
	}

	/**
	 * Setup this component.
	 */
	public function setup() {
		if ( empty( $this->pusher ) ) {
			$this->pusher  = $this->plugin->components['sync']->managers['push'];
			$this->sync    = $this->plugin->components['sync'];
			$this->connect = $this->plugin->components['connect'];
			$this->media   = $this->plugin->components['media'];
		}
		$this->register_hooks();
	}

	/**
	 * Prepare an attachment without a cloudinary id, for background, on-demand push.
	 *
	 * @param string|bool $cloudinary_id The public ID for a cloudinary asset.
	 * @param int         $attachment_id The local attachment ID.
	 *
	 * @return string
	 */
	public function prep_on_demand_upload( $cloudinary_id, $attachment_id ) {
		$attachment_id = intval( $attachment_id );
		if ( $attachment_id && false === $cloudinary_id ) {
			// Check that this has not already been prepared for upload.
			if ( ! $this->is_pending( $attachment_id ) && apply_filters( 'cloudinary_on_demand_sync_enabled', $this->enabled, $attachment_id ) ) {
				$max_size = ( wp_attachment_is_image( $attachment_id ) ? 'max_image_size' : 'max_video_size' );
				$file     = get_attached_file( $attachment_id );
				// Get the file size to make sure it can exist in cloudinary.
				if ( ! empty( $this->plugin->components['connect']->usage[ $max_size ] ) && file_exists( $file ) && filesize( $file ) < $this->plugin->components['connect']->usage[ $max_size ] ) {
					$this->add_to_sync( $attachment_id );
				} else {
					// Check if the src is a url.
					$file = get_post_meta( $attachment_id, '_wp_attached_file', true );
					if ( $this->plugin->components['media']->is_cloudinary_url( $file ) ) {
						// Download sync.
						$this->add_to_sync( $attachment_id );
					}
				}
			}
		}

		return $cloudinary_id;
	}

	/**
	 * Upload an asset to Cloudinary.
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return array|\WP_Error
	 */
	public function upload_asset( $attachment_id ) {

		$type      = $this->sync->get_sync_type( $attachment_id );
		$options   = $this->media->get_upload_options( $attachment_id );
		$public_id = $options['public_id'];
		$result    = $this->connect->api->upload( $attachment_id, $options );

		if ( ! is_wp_error( $result ) ) {
			// Set public_id.
			$this->media->update_post_meta( $attachment_id, Sync::META_KEYS['public_id'], $public_id );
			// Update signature for all that use the same method.
			$this->sync->update_signature( $attachment_id, $type );
			// Update options and public_id as well (full sync)
			$this->sync->set_signature_item( $attachment_id, 'options' );
			$this->sync->set_signature_item( $attachment_id, 'public_id' );

			$this->update_breakpoints( $attachment_id, $result );
			$this->update_content( $attachment_id );
		}

		return $result;
	}

	/**
	 * Update an assets context..
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return array|\WP_Error
	 */
	public function context_update( $attachment_id ) {
		// dynamic sync type..
		$type    = $this->sync->get_sync_type( $attachment_id );
		$options = $this->media->get_upload_options( $attachment_id );
		$result  = $this->connect->api->context( $options );

		if ( ! is_wp_error( $result ) ) {
			$this->sync->set_signature_item( $attachment_id, $type );
			$this->media->update_post_meta( $attachment_id, Sync::META_KEYS['public_id'], $options['public_id'] );
		}

		return $result;
	}

	/**
	 * Perform an explicit update to Cloudinary.
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return array|\WP_Error|bool
	 */
	public function explicit_update( $attachment_id ) {
		// Explicit update.
		$type = $this->sync->get_sync_type( $attachment_id );
		$args = $this->media->get_breakpoint_options( $attachment_id );
		if ( ! empty( $args ) ) {
			$result = $this->connect->api->explicit( $args );
			if ( ! is_wp_error( $result ) ) {
				$this->update_breakpoints( $attachment_id, $result );
			}
		} else {
			$this->update_breakpoints( $attachment_id, array() );
			$result = true;
		}
		$this->sync->set_signature_item( $attachment_id, $type );

		return $result;
	}

	/**
	 * Update breakpoints for an asset.
	 *
	 * @param int   $attachment_id The attachment ID.
	 * @param array $breakpoints   Structure of the breakpoints.
	 */
	public function update_breakpoints( $attachment_id, $breakpoints ) {

		if ( ! empty( $this->plugin->config['settings']['global_transformations']['enable_breakpoints'] ) ) {
			if ( ! empty( $breakpoints['responsive_breakpoints'] ) ) { // Images only.
				$this->media->update_post_meta( $attachment_id, Sync::META_KEYS['breakpoints'], $breakpoints['responsive_breakpoints'][0]['breakpoints'] );
			} elseif ( wp_attachment_is_image( $attachment_id ) ) {
				$this->media->delete_post_meta( $attachment_id, Sync::META_KEYS['breakpoints'] );
			}
			$this->sync->set_signature_item( $attachment_id, 'breakpoints' );
		}
	}

	/**
	 * Prep an attachment for upload.
	 *
	 * @param int $attachment_id The attachment ID to prep for upload.
	 */
	public function prep_upload( $attachment_id ) {
		$max_size = ( wp_attachment_is_image( $attachment_id ) ? 'max_image_size' : 'max_video_size' );
		$file     = get_attached_file( $attachment_id );
		// Get the file size to make sure it can exist in cloudinary.
		if ( file_exists( $file ) && filesize( $file ) < $this->plugin->components['connect']->usage[ $max_size ] ) {
			$this->add_to_sync( $attachment_id );
		} else {
			// Check if the src is a url.
			$file = get_post_meta( $attachment_id, '_wp_attached_file', true );
			if ( $this->plugin->components['media']->is_cloudinary_url( $file ) ) {
				// Download sync.
				$this->add_to_sync( $attachment_id );
			}
		}
	}

	/**
	 * Trigger an update on content that contains the same attachment ID to allow filters to capture and process.
	 *
	 * @param int $attachment_id The attachment id to find and init an update.
	 */
	private function update_content( $attachment_id ) {
		// Search and update link references in content.
		$content_search = new \WP_Query( array( 's' => 'wp-image-' . $attachment_id, 'fields' => 'ids', 'posts_per_page' => 1000 ) );
		if ( ! empty( $content_search->found_posts ) ) {
			$content_posts = array_unique( $content_search->get_posts() ); // ensure post only gets updated once.
			foreach ( $content_posts as $content_id ) {
				wp_update_post( array( 'ID' => $content_id ) ); // Trigger an update, internal filters will filter out remote URLS.
			}
		}
	}
}
