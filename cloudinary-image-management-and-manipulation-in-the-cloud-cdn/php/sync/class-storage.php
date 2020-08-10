<?php
/**
 * Storage management options.
 *
 * @package Cloudinary
 */

namespace Cloudinary;

namespace Cloudinary\Sync;

use Cloudinary\Sync;

/**
 * Class Filter.
 *
 * Handles filtering of HTML content.
 */
class Storage {

	/**
	 * Holds the Plugin instance.
	 *
	 * @since   0.1
	 *
	 * @var     \Cloudinary\Plugin Instance of the plugin.
	 */
	protected $plugin;


	/**
	 * Holds the Plugin Media instance.
	 *
	 * @since   0.1
	 *
	 * @var     \Cloudinary\Media Instance of the media object.
	 */
	protected $media;

	/**
	 * Holds the Sync instance.
	 *
	 * @since   0.1
	 *
	 * @var     \Cloudinary\Sync Instance of the plugin.
	 */
	protected $sync;

	/**
	 * Holds the Download Sync instance.
	 *
	 * @since   0.1
	 *
	 * @var     \Cloudinary\Sync\Download_Sync Instance of the plugin.
	 */
	protected $download;

	/**
	 * Filter constructor.
	 *
	 * @param \Cloudinary\Plugin $plugin The plugin.
	 */
	public function __construct( \Cloudinary\Plugin $plugin ) {
		$this->plugin   = $plugin;
		$this->sync     = $this->plugin->components['sync'];
		$this->media    = $this->plugin->components['media'];
		$this->download = $this->sync->managers['download'];
		add_action( 'cloudinary_register_sync_types', array( $this, 'setup' ), 20 );
	}

	/**
	 * Generate a signature for this sync type.s
	 *
	 * @return string
	 */
	public function generate() {
		$settings = $this->plugin->config['settings']['storage'];

		return $settings['offload'] . $settings['low_res'];
	}

	/**
	 * Process the storage sync for an attachment.
	 *
	 * @param int $attachment_id The attachment ID.
	 */
	public function sync( $attachment_id ) {
		$settings = $this->plugin->config['settings']['storage'];

		switch ( $settings['offload'] ) {
			case 'cld':
				$this->remove_local_assets( $attachment_id );
				break;
			case 'dual_low':
				$url = $this->media->cloudinary_url( $attachment_id, 'full', array( array( 'quality' => $settings['low_res'] ) ), null, false, true );
				break;
			case 'dual_full':
				$state = $this->media->get_post_meta( $attachment_id, Sync::META_KEYS['storage'], true );
				if( !empty( $state ) && 'dual_full' !== $state ) {
					// Only do this is it's changing a state.
					$url = $this->media->cloudinary_url( $attachment_id, '', array(), null, false, false );
				}
				break;
		}

		if ( ! empty( $url ) ) {
			$this->remove_local_assets( $attachment_id );
			$this->create_local_assets( $attachment_id, $url );
		}

		$this->sync->set_signature_item( $attachment_id, 'storage' );
		$this->media->update_post_meta( $attachment_id, Sync::META_KEYS['storage'], $settings['offload'] ); // Save the state.
	}

	/**
	 * @param $attachment_id
	 *
	 * @return bool
	 */
	protected function remove_local_assets( $attachment_id ) {
		// Delete local versions of images.
		$meta = wp_get_attachment_metadata( $attachment_id );

		return wp_delete_attachment_files( $attachment_id, $meta, array(), get_attached_file( $attachment_id ) );
	}

	/**
	 * Create local assets from a download URL.
	 *
	 * @param int    $attachment_id The attachment ID to download to.
	 * @param string $url           The source URL.
	 */
	protected function create_local_assets( $attachment_id, $url ) {
		$download = $this->download->download_asset( $attachment_id, $url );
		$this->download->update_attachment( $attachment_id, $download );
	}

	/**
	 * Setup hooks for the filters.
	 */
	public function setup() {
		$this->sync = $this->plugin->components['sync'];

		$structure = array(
			'generate' => array( $this, 'generate' ),
			'priority' => 5.2,
			'sync'     => array( $this, 'sync' ),
			'state'    => 'info syncing',
			'note'     => function () {
				$settings = $this->plugin->config['settings']['storage'];
				if ( 'cld' === $settings['offload'] ) {
					return __( 'Off-loading to Cloudinary', 'cloudinary' );
				} elseif ( 'dual_low' === $settings['offload'] ) {
					return __( 'Reducing local resolutions', 'cloudinary' );
				} else {
					return __( 'Rebuilding local copies.', 'cloudinary' );
				}

			},
		);
		$this->sync->register_sync_type( 'storage', $structure );
	}
}
