<?php
/**
 * Defines the main settings page for Cloudinary.
 *
 * @package Cloudinary
 */

// A repeatable UI element can be pre-defined, then added to the structure array where needed.
$header = array(
	'type'    => 'header',
	'content' => array(
		array(
			'type'  => 'icon',
			'src'   => $this->plugin->dir_url . '/css/logo.svn',
			'width' => '135px',
		),
		array(
			'type'       => 'link',
			'text'       => __( 'Need Help?' ),
			'attributes' => array(
				'href'   => 'https://cloudinary.com/documentation/wordpress_integration',
				'target' => '_blank',
			),
		),
	),
);
// Footer to be used on each page.
$footer = array(
	'type'        => 'footer',
	'dismissible' => true,
	'content'     => array(
		array(
			'type' => 'text',
			'text' => __( 'Thanks for using Cloudinary, please take a minute to rate our plugin.' ),
		),
		array(
			'type' => 'rating',
		),
	),
);

// Core definition. First page fully defined to illustrate UI components.
$definition = array(
	'page_title' => __( 'Cloudinary', 'cloudinary' ),
	'menu_title' => __( 'Cloudinary', 'cloudinary' ),
	'version'    => $this->plugin->version,
	'slug'       => 'cloudinary',
	'capability' => 'manage_options',
	'pages'      => array( // Pages is a specific setting, which as it currently does, registers pages.
		'cloudinary'            => array(
			'page_title' => __( 'Cloudinary Dashboard', 'cloudinary' ),
			'menu_title' => __( 'Dashboard', 'cloudinary' ),
			'slug'       => 'cloudinary',
			/**
			 * The `content` element is the deviation from the current system
			 * This is an array of UI components that are added to the page, in order.
			 */
			'content'    => array(
				$header,
				array(
					'type'    => 'panel',
					'title'   => __( 'Your Current Plan', 'cloudinary' ),
					'width'   => '719px', // The width forms part of basic settings for a component. This is rendered in the container, before placing in the content.
					// A special thing to note, is that a UI component, can contain embedded components, infinitely.
					'content' => array(
						array(
							'type'    => 'panel',
							'display' => 'block',
							'class'   => 'inner-panel',
							'content' => array(
								array(
									'type' => 'plan_summary' // Plan Summary is a UI component that can be registered.
								),
							),
						),
						array(
							'type'       => 'link',
							'text'       => __( 'Upgrade Plan' ),
							'attributes' => array(
								'href'   => 'https://cloudinary.com/console/lui/upgrade_options',
								'target' => '_blank',
								'class'  => 'button-primary',
							),
						),
					),
				),
				array(
					'type'    => 'panel',
					'title'   => __( 'Plan Status', 'cloudinary' ),
					'content' => array(
						array(
							'type'    => 'panel',
							'width'   => '33%',
							'content' => array(
								array(
									'type' => 'storage_usage',
								),
							),
						),
						array(
							'type'    => 'panel',
							'width'   => '33%',
							'content' => array(
								array(
									'type' => 'transformation_usage',
								),
							),
						),
						array(
							'type'    => 'panel',
							'width'   => '33%',
							'content' => array(
								array(
									'type' => 'bandwidth_usage',
								),
							),
						),
					),
				),
				array(
					'type'    => 'panel',
					'title'   => __( 'Your Media Sync Status', 'cloudinary' ),
					'content' => array(
						array(
							'type'    => 'panel',
							'content' => array(
								array(
									'type' => 'media_sync_status',
								),
							),
						),
					),
				),
				$footer,
			),
		),
		'connect'               => array(
			'page_title' => __( 'Cloudinary Connection', 'cloudinary' ),
			'menu_title' => __( 'Connect', 'cloudinary' ),
			'slug'       => 'cld_connect',
			'tabs'       => array(
				'connect',
			),
		),
		'global_transformation' => array(
			'page_title'      => __( 'Global Transformations Settings', 'cloudinary' ),
			'menu_title'      => __( 'Global Transformations', 'cloudinary' ),
			'slug'            => 'cld_global_transformation',
			'requires_config' => true,
			'tabs'            => array(
				'global_transformations',
				'global_video_transformations',
			),
		),
		'sync_media'            => array(
			'page_title'      => __( 'Sync Media to Cloudinary', 'cloudinary' ),
			'menu_title'      => __( 'Sync Media', 'cloudinary' ),
			'slug'            => 'cld_sync_media',
			'requires_config' => true,
			'tabs'            => array(
				'sync_media',
			),
		),
	),
);

return $definition;
