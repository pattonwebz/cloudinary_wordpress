<?php
/**
 * Push Sync to Cloudinary.
 *
 * @package Cloudinary
 */

namespace Cloudinary\Sync;

use Cloudinary\Connect\Api;
use Cloudinary\Sync;

/**
 * Class Push_Sync
 *
 * Push media library to Cloudinary.
 */
class Push_Sync {

	/**
	 * Holds the plugin instance.
	 *
	 * @since   0.1
	 *
	 * @var     \Cloudinary\Plugin Instance of the global plugin.
	 */
	public $plugin;

	/**
	 * Holds the ID of the last attachment synced.
	 *
	 * @var int
	 */
	protected $post_id;

	/**
	 * Holds the media component.
	 *
	 * @var \Cloudinary\Media
	 */
	protected $media;

	/**
	 * Holds the sync component.
	 *
	 * @var \Cloudinary\Sync
	 */
	protected $sync;

	/**
	 * Holds the connect component.
	 *
	 * @var \Cloudinary\Connect
	 */
	protected $connect;

	/**
	 * Holds the Rest_API component.
	 *
	 * @var \Cloudinary\REST_API
	 */
	protected $api;

	/**
	 * Push_Sync constructor.
	 *
	 * @param \Cloudinary\Plugin $plugin Global instance of the main plugin.
	 */
	public function __construct( \Cloudinary\Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->register_hooks();
	}

	/**
	 * Register any hooks that this component needs.
	 */
	private function register_hooks() {
		add_filter( 'cloudinary_api_rest_endpoints', array( $this, 'rest_endpoints' ) );
	}

	/**
	 * Setup this component.
	 */
	public function setup() {
		// Setup components.
		$this->media   = $this->plugin->components['media'];
		$this->sync    = $this->plugin->components['sync'];
		$this->connect = $this->plugin->components['connect'];
		$this->api     = $this->plugin->components['api'];
		add_action( 'cloudinary_run_queue', array( $this, 'process_queue' ) );
		add_action( 'cloudinary_sync_items', array( $this, 'process_assets' ) );
	}

	/**
	 * Add endpoints to the \Cloudinary\REST_API::$endpoints array.
	 *
	 * @param array $endpoints Endpoints from the filter.
	 *
	 * @return array
	 */
	public function rest_endpoints( $endpoints ) {

		$endpoints['attachments'] = array(
			'method'              => \WP_REST_Server::READABLE,
			'callback'            => array( $this, 'rest_get_queue_status' ),
			'args'                => array(),
			'permission_callback' => array( $this, 'rest_can_manage_options' ),
		);

		$endpoints['sync'] = array(
			'method'              => \WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'rest_start_sync' ),
			'args'                => array(),
			'permission_callback' => array( $this, 'rest_can_manage_options' ),
		);

		$endpoints['process'] = array(
			'method'   => \WP_REST_Server::CREATABLE,
			'callback' => array( $this, 'process_sync' ),
			'args'     => array(),
		);

		return $endpoints;
	}

	/**
	 * Admin permission callback.
	 *
	 * Explicitly defined to allow easier testability.
	 *
	 * @return bool
	 */
	public function rest_can_manage_options() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get status of the current queue via REST API.
	 *
	 * @return \WP_REST_Response
	 */
	public function rest_get_queue_status() {

		return rest_ensure_response(
			array(
				'success' => true,
				'data'    => $this->sync->managers['queue']->get_queue_status(),
			)
		);
	}

	/**
	 * Starts a sync backbround process.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function rest_start_sync( \WP_REST_Request $request ) {

		$stop  = $request->get_param( 'stop' );
		$queue = $this->sync->managers['queue']->get_queue();
		if ( empty( $queue['pending'] ) || ! empty( $stop ) ) {
			$this->sync->managers['queue']->stop_queue();

			return $this->rest_get_queue_status(); // Nothing to sync.
		}

		return $this->sync->managers['queue']->start_queue();
	}

	/**
	 * Process asset sync.
	 *
	 * @param int|array $attachments An attachment ID or an array of ID's.
	 *
	 * @return array
	 */
	public function process_assets( $attachments = array() ) {

		$stat = array();
		// If a single specified ID, push and return response.
		$ids = array_map( 'intval', (array) $attachments );
		// Handle based on Sync Type.
		foreach ( $ids as $attachment_id ) {
			// Flag attachment as being processed.
			update_post_meta( $attachment_id, Sync::META_KEYS['syncing'], time() );
			while ( $type = $this->sync->get_sync_type( $attachment_id, false ) ) {
				if ( isset( $stat[ $attachment_id ][ $type ] ) ) {
					// Loop prevention.
					break;
				}
				$callback                        = $this->sync->get_sync_method( $type );
				$stat[ $attachment_id ][ $type ] = call_user_func( $callback, $attachment_id );
			}
			// remove pending.
			if ( $this->sync->is_pending( $attachment_id ) ) {
				$this->media->delete_post_meta( $attachment_id, Sync::META_KEYS['pending'] );
			}
			// Record Process log.
			$this->media->update_post_meta( $attachment_id, Sync::META_KEYS['process_log'], $stat[ $attachment_id ] );
			// Remove processing flag.
			delete_post_meta( $attachment_id, Sync::META_KEYS['syncing'] );

			// Create synced post meta as a way to search for synced / unsynced items.
			update_post_meta( $attachment_id, Sync::META_KEYS['public_id'], $this->media->get_public_id( $attachment_id ) );
		}

		return $stat;
	}

	/**
	 * Process assets to sync vai WP REST API.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function process_sync( \WP_REST_Request $request ) {
		$process_key = $request->get_param( 'process_key' );
		$note        = 'no process key';
		if ( ! empty( $process_key ) ) {
			$attachments = get_transient( $process_key );
			if ( ! empty( $attachments ) ) {
				delete_transient( $process_key );

				return rest_ensure_response(
					array(
						'success' => true,
						'data'    => $this->process_assets( $attachments ),
					)
				);
			}
			$note = 'no attachments';
		}

		return rest_ensure_response(
			array(
				'success' => false,
				'note'    => $note,
			)
		);
	}

	/**
	 * Pushes attachments via WP REST API.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function rest_push_attachments( \WP_REST_Request $request ) {


		$option_key = $request->get_param( 'synckey' );
		$data       = get_transient( $option_key );
		// Get thread ID.
		$last_id     = $request->get_param( 'last_id' );
		$last_result = $request->get_param( 'last_result' );

		// Get attachment_id in case this is a single direct request to upload.
		$attachments        = $request->get_param( 'attachment_ids' );
		$background_process = false;

		if ( empty( $attachments ) ) {
			// No attachments posted. Pull from the queue.
			$attachments        = array();
			$background_process = true;
			$attachments[]      = $this->sync->managers['queue']->get_post();

			$attachments = array_filter( $attachments );
		}
		$stat = array();
		// If not a single request, process based on queue.
		if ( ! empty( $attachments ) ) {

			// If a single specified ID, push and return response.
			$ids = array_map( 'intval', $attachments );
			// Handle based on Sync Type.
			$stat = $this->process_assets( $ids );

		}

		return rest_ensure_response(
			array(
				'success' => true,
				'data'    => $stat,
			)
		);

	}

	/**
	 * Resume the bulk sync.
	 *
	 * @return void
	 */
	public function process_queue() {
		if ( $this->sync->managers['queue']->is_running() ) {
			$queue = $this->sync->managers['queue']->get_queue();
			if ( ! empty( $queue['pending'] ) ) {
				wp_schedule_single_event( time() + 20, 'cloudinary_run_queue' );
				if ( ! empty( $queue['last_update'] ) ) {
					if ( $queue['last_update'] > current_time( 'timestamp' ) - 60 ) {
						return;
					}
				}
				while ( $attachment_id = $this->sync->managers['queue']->get_post() ) {
					$this->process_assets( $attachment_id );
					$this->sync->managers['queue']->mark( $attachment_id, 'done' );
				}
			}
		}
	}
}
