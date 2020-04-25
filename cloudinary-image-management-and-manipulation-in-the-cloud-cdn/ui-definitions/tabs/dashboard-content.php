<?php
/**
 * Dashboard html content for the dashboard tab.
 *
 * @package Cloudinary
 */

$video_url = ''; // Left blank for when we get the final video URL.
$usage     = $this->plugin->components['connect']->usage;
// Different account version have usage limits.
if ( isset( $usage['credits'] ) ) {
	// New credit system.
	$usage['credits']         = array(
		'limit'        => $usage['credits']['limit'],
		'used_percent' => $usage['credits']['used_percent'],
	);
	$usage['storage']         = array(
		'limit'        => $usage['storage']['usage'],
		'used_percent' => $usage['storage']['credits_usage'],
	);
	$usage['transformations'] = array(
		'limit'        => $usage['transformations']['usage'],
		'used_percent' => $usage['transformations']['credits_usage'],
	);
	$usage['bandwidth']       = array(
		'limit'        => $usage['bandwidth']['usage'],
		'used_percent' => $usage['bandwidth']['credits_usage'],
	);
}

?>
<div class="settings-tab-section-fields-dashboard">
	<div class="settings-tab-section-fields-dashboard-description">
		<?php esc_html_e( 'Cloudinary supercharges your application media! It enables you to easily upload images and videos to the cloud and deliver them optimized, via a lightning-fast CDN, using industry best practices. Perform smart resizing, add watermarks, apply effects, and much more without leaving your WordPress console or installing any software.', 'cloudinary' ); ?>
		<?php if ( empty( $this->plugin->config['connect'] ) ) : ?>
			<h3><?php esc_html_e( 'Get Started', 'cloudinary' ); ?></h3>
			<div class="settings-tab-section-fields-dashboard-error">
				<span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Not connected to Cloudinary', 'cloudinary' ); ?>
				<p>
					<a href="admin.php?page=cld_connect" class="button button-primary"><?php esc_html_e( 'Connect to Cloudinary', 'cloudinary' ); ?></a>
				</p>
				<p>
					<?php esc_html_e( 'Don\'t have an account?', 'cloudinary' ); ?>
					<a href="https://cloudinary.com/users/register/free" target="_blank"><?php esc_html_e( 'Sign up for FREE!', 'cloudinary' ); ?></a>
				</p>
			</div>
		<?php else : ?>
			<div class="settings-tab-section-fields-dashboard-success expanded">
				<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Connected to Cloudinary', 'cloudinary' ); ?>
			</div>
			<hr>
			<div class="cloudinary-stats">
				<strong><?php esc_html_e( $usage['plan'] ); ?></strong> |
				<?php if ( isset( $usage['credits'] ) ) : ?>
					<span class="cloudinary-stat" title="<?php esc_attr_e( 'Credits', 'cloudinary' ); ?>">
					<span class="dashicons dashicons-marker"></span> <?php esc_html_e( number_format_i18n( $usage['credits']['limit'] ) ); ?>
					<span class="cloudinary-percent"> <?php esc_html_e( $usage['credits']['used_percent'] . '%' ); ?></span> |
				</span>
				<?php endif; ?>

				<span class="cloudinary-stat" title="<?php esc_attr_e( 'Storage', 'cloudinary' ); ?>">
					<span class="dashicons dashicons-cloud"></span> <?php esc_html_e( size_format( $usage['storage']['limit'] ) ); ?>
					<span class="cloudinary-percent"> <?php esc_html_e( $usage['storage']['used_percent'] . '%' ); ?></span> |
				</span>
				<span class="cloudinary-stat" title="<?php esc_attr_e( 'Transformations', 'cloudinary' ); ?>">
					<span class="dashicons dashicons-image-filter"></span> <?php esc_html_e( number_format_i18n( $usage['transformations']['limit'] ) ); ?>
					<span class="cloudinary-percent success"> <?php esc_html_e( $usage['transformations']['used_percent'] . '%' ); ?></span> |
				</span>
				<span class="cloudinary-stat" title="<?php esc_attr_e( 'Bandwidth', 'cloudinary' ); ?>">
					<span class="dashicons dashicons-dashboard"></span> <?php esc_html_e( size_format( $usage['bandwidth']['limit'] ) ); ?>
					<span class="cloudinary-percent success"> <?php esc_html_e( $usage['bandwidth']['used_percent'] . '%' ); ?></span>
				</span>
			</div>
			<hr>
			<div class="cloudinary-stats">
				<a href="https://cloudinary.com/console/lui/upgrade_options" class="button button-primary" target="_blank"><?php esc_html_e( 'Upgrade Plan', 'cloudinary' ); ?></a>
				<a href="https://cloudinary.com/console" class="button" target="_blank"><?php esc_html_e( 'Cloudinary Dashboard', 'cloudinary' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
	<div class="settings-tab-section-fields-dashboard-content">
		<?php
		$manage_text = sprintf(
		// translators: Placeholders are URLS.
			__(
				'Manage <a href="%1$s">Image</a> or <a href="%2$s">Video</a> Settings.',
				'cloudinary'
			),
			'admin.php?page=cld_global_transformation',
			'admin.php?page=cld_global_transformation&tab=global_video_transformations'
		);

		// Video Player for dashboard.
		$player   = array();
		$player[] = 'var cld = cloudinary.Cloudinary.new({ cloud_name: \'demo\' });';
		$player[] = 'var samplePlayer = cld.videoPlayer(\'dashboard-player\', { fluid : true } );';
		wp_add_inline_script( 'cld-player', implode( $player ) );

		?>
		<?php if ( ! empty( $video_url ) ) : ?>
			<video id="dashboard-player" controls class="cld-video-player cld-fluid"></video>
		<?php endif; ?>
		<h3><?php esc_html_e( 'More Actions', 'cloudinary' ); ?></h3>
		<p><span class="dashicons dashicons-image-crop"></span> <?php echo wp_kses_post( $manage_text ); ?></p>
		<p><span class="dashicons dashicons-welcome-learn-more"></span>
			<a href="https://cloudinary.com/documentation/wordpress_integration" target="_blank"> <?php esc_html_e( 'Learn more about getting started' ); ?></a></p>
	</div>
</div>
<?php if ( ! empty( $video_url ) ) : ?>
	<script type="application/javascript">
        document.addEventListener( 'DOMContentLoaded', function() {
            samplePlayer.source( <?php echo esc_url( $video_url ); ?> );
        } );
	</script>
<?php endif; ?>
