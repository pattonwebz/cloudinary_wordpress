<?php
/**
 * HTML content for the Sync Media tab.
 *
 * @package Cloudinary
 */

?>
<?php if ( ! empty( $this->plugin->config['connect'] ) ) : ?>
	<div class="settings-tab-section-card">
		<div class="settings-tab-section-fields-dashboard-success">
			<?php esc_html_e( 'Auto Sync', 'cloudinary' ); ?>
			<p class="description">
				<?php esc_html_e( 'All of your assets will be kept in-sync with Cloudinary automatically when you connect your account. Existing WordPress assets will be uploaded to the Cloudinary folder specified below and any assets that exist in the Cloudinary folder will sync back to local storage on WordPress. Auto-syncing allows all assets to be available for delivery from your WordPress Media Library in case the plugin is disabled.', 'cloudinary' ); ?><br>
			</p>
		</div>
	</div>
<?php endif; ?>
