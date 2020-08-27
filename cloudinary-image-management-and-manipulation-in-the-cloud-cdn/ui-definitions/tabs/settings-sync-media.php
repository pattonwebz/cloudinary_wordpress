<?php
/**
 * Defines the tab structure for Cloudinary settings page.
 *
 * @package Cloudinary
 */
$dirs = wp_get_upload_dir();
$base = wp_parse_url( $dirs['baseurl'] );

$struct = array(
	'title'           => __( 'Sync Media', 'cloudinary' ),
	'description'     => __( 'Sync WordPress Media with Cloudinary', 'cloudinary' ),
	'hide_button'     => true,
	'requires_config' => true,
	'fields'          => array(
		'auto_sync'         => array(
			'label'       => __( 'Sync method', 'cloudinary' ),
			'description' => __( 'Auto Sync: Media is synchronized automatically on demand; Manual Sync: Manually synchronize assets from the Media page.', 'cloudinary' ),
			'type'        => 'radio',
			'default'     => 'on',
			'choices'	  => array(
				'on'  => __( 'Auto Sync', 'cloudinary' ),
				'off' => __( 'Manual Sync', 'cloudinary' ),
			)
		),
		'cloudinary_folder' => array(
			'label'             => __( 'Cloudinary folder path', 'cloudinary' ),
			'placeholder'       => __( 'e.g.: wordpress_assets/', 'cloudinary' ),
			'description'       => __( 'Specify the folder in your Cloudinary account where WordPress assets are uploaded to. All assets uploaded to WordPress from this point on will be synced to the specified folder in Cloudinary. Leave blank to use the root of your Cloudinary library.', 'cloudinary' ),
			'sanitize_callback' => array( '\Cloudinary\Media', 'sanitize_cloudinary_folder' ),
		),
	),
);

return apply_filters( 'cloudinary_admin_tab_sync_media', $struct );
