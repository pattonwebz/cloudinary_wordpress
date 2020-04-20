<?php
/**
 * HTML content for the Sync Media tab.
 *
 * @package Cloudinary
 */

$autosync = false;
if ( isset( $this->plugin->config['settings']['sync_media']['auto_sync'] ) && 'on' === $this->plugin->config['settings']['sync_media']['auto_sync'] ) {
	$autosync = true;
}
?>
<?php if ( ! empty( $this->plugin->config['connect'] ) ) : ?>
	<div class="settings-tab-section-card">
		<div class="settings-tab-section-fields-dashboard-success">
			<?php if ( true === $autosync ) : ?>
				<span class="sync-status-enabled"><?php esc_html_e( 'Auto Sync is on', 'cloudinary' ); ?></span>
				<p class="description">
					<?php esc_html_e( 'WordPress Media Library assets are synced with Cloudinary automatically when you connect your account.', 'cloudinary' ); ?><br>
					<?php esc_html_e( 'If you had existing assets in your WordPress Media Library you may need to perform a Bulk-Sync below.', 'cloudinary' ); ?>
				</p>
			<?php else: ?>
				<span class="sync-status-disabled"><?php esc_html_e( 'Auto Sync is off', 'cloudinary' ); ?></span>
				<p class="description">
					<?php esc_html_e( 'WordPress Media Library assets are synced with Cloudinary manually.', 'cloudinary' ); ?><br>
					<?php esc_html_e( 'If you had existing assets in your WordPress Media Library you may need to perform a Bulk-Sync below.', 'cloudinary' ); ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
