<?php
/**
 * Defines the tab structure for Cloudinary settings page.
 *
 * @package Cloudinary
 */

$struct = array(
	'heading'     => __( 'Gallery Block and Widget', 'cloudinary' ),
	'hide_button' => false,
	'assets'      => array(
		'style'  => array(
			'wp-color-picker',
		),
		'script' => array(
			'wp-color-picker',
		),
	),
	'fields'      => array(
		'enable_gallery'                    => array(
			'label'   => __( 'Enable Gallery', 'cloudinary' ),
			'suffix'  => __( 'Enable gallery widget for WooCommerce and Gutenberg.', 'cloudinary' ),
			'type'    => 'checkbox',
			'default' => 'off',
		),
		'color_header'                      => array(
			'type'        => 'heading',
			'label'       => __( 'Colors', 'cloudinary' ),
			'description' => __( 'Set the type of transition that occurs when switching the images in the gallery.', 'cloudinary' ),
		),
		'primary_color'                     => array(
			'label' => __( 'Primary Color', 'cloudinary' ),
			'type'  => 'color',
		),
		'on_primary_color'                  => array(
			'label' => __( 'On Primary Color', 'cloudinary' ),
			'type'  => 'color',
		),
		'active_color'                      => array(
			'label' => __( 'Active Color', 'cloudinary' ),
			'type'  => 'color',
		),
		'on_active_color'                   => array(
			'label' => __( 'On Active Color', 'cloudinary' ),
			'type'  => 'color',
		),
		'main_header'                       => array(
			'type'  => 'heading',
			'label' => __( 'Main Settings', 'cloudinary' ),
		),
		'transition'                        => array(
			'label'       => __( 'Transition Style', 'cloudinary' ),
			'description' => __( 'Set the type of transition that occurs when switching the images in the gallery.', 'cloudinary' ),
			'type'        => 'select',
			'choices'     => array(
				'none'  => __( 'None', 'cloudinary' ),
				'fade'  => __( 'Fade', 'cloudinary' ),
				'slide' => __( 'Slide', 'cloudinary' ),
			),
			'default'     => 'none',
		),
		'aspect_ratio'                      => array(
			'label'       => __( 'Aspect Ratio', 'cloudinary' ),
			'description' => __( 'Set the aspect ratio for the images.', 'cloudinary' ),
			'type'        => 'select',
			'choices'     => array(
				'1:1'  => __( '1:1', 'cloudinary' ),
				'3:4'  => __( '3:4', 'cloudinary' ),
				'4:3'  => __( '4:3', 'cloudinary' ),
				'4:6'  => __( '4:6', 'cloudinary' ),
				'6:4'  => __( '6:4', 'cloudinary' ),
				'5:7'  => __( '5:7', 'cloudinary' ),
				'7:5'  => __( '7:5', 'cloudinary' ),
				'5:8'  => __( '5:8', 'cloudinary' ),
				'8:5'  => __( '8:5', 'cloudinary' ),
				'9:16' => __( '9:16', 'cloudinary' ),
				'16:9' => __( '16:9', 'cloudinary' ),
			),
			'default'     => 'none',
		),
		'navigation'                        => array(
			'label'       => __( 'Show Navigation', 'cloudinary' ),
			'description' => __( 'Choose when to show the navigation options.', 'cloudinary' ),
			'type'        => 'radio',
			'choices'     => array(
				'never'     => __( 'Never', 'cloudinary' ),
				'always'    => __( 'Always', 'cloudinary' ),
				'mouseover' => __( 'Mouse over', 'cloudinary' ),
			),
			'default'     => 'none',
		),
		'zoom_trigger'                      => array(
			'label'       => __( 'Zoom', 'cloudinary' ),
			'description' => __( 'Choose the trigger for zooming or disable it entirely.', 'cloudinary' ),
			'type'        => 'radio',
			'choices'     => array(
				'none'  => __( 'No Zoom', 'cloudinary' ),
				'click' => __( 'Click', 'cloudinary' ),
				'hover' => __( 'Hover', 'cloudinary' ),
			),
			'default'     => 'none',
		),
		'zoom_type'                         => array(
			'label'   => __( 'Zoom Type', 'cloudinary' ),
			'type'    => 'select',
			'choices' => array(
				'inline' => __( 'Inline', 'cloudinary' ),
				'flyout' => __( 'Flyout', 'cloudinary' ),
				'popup'  => __( 'Popup', 'cloudinary' ),
			),
			'default' => 'none',
		),
		'zoom_viewer_position'              => array(
			'label'   => __( 'Zoom Viewer Position', 'cloudinary' ),
			'type'    => 'select',
			'choices' => array(
				'top'    => __( 'Top', 'cloudinary' ),
				'right'  => __( 'Right', 'cloudinary' ),
				'left'   => __( 'Left', 'cloudinary' ),
				'bottom' => __( 'Bottom', 'cloudinary' ),
			),
			'default' => 'none',
		),
		'carousel_header'                   => array(
			'type'  => 'heading',
			'label' => __( 'Carousel Settings', 'cloudinary' ),
		),
		'carousel_location'                 => array(
			'label'   => __( 'Carousel Location', 'cloudinary' ),
			'type'    => 'select',
			'choices' => array(
				'top'    => __( 'Top', 'cloudinary' ),
				'right'  => __( 'Right', 'cloudinary' ),
				'left'   => __( 'Left', 'cloudinary' ),
				'bottom' => __( 'Bottom', 'cloudinary' ),
			),
			'default' => 'none',
		),
		'carousel_offset'                   => array(
			'label'   => __( 'Carousel Offset', 'cloudinary' ),
			'type'    => 'number',
			'default' => 5,
		),
		'carousel_style'                    => array(
			'label'   => __( 'Carousel Style', 'cloudinary' ),
			'type'    => 'select',
			'choices' => array(
				'none'       => __( 'None', 'cloudinary' ),
				'thumbnails' => __( 'Thumbnails', 'cloudinary' ),
				'indicators' => __( 'Indicators', 'cloudinary' ),
			),
			'default' => 'none',
		),
		'carousel_thumbnail_width'          => array(
			'label'     => __( 'Thumbnail Width', 'cloudinary' ),
			'type'      => 'number',
			'default'   => 64,
			'condition' => array(
				'carousel_style' => 'thumbnails',
			),
		),
		'carousel_thumbnail_height'         => array(
			'label'     => __( 'Thumbnail Height', 'cloudinary' ),
			'type'      => 'number',
			'default'   => 64,
			'condition' => array(
				'carousel_style' => 'thumbnails',
			),
		),
		'carousel_button_shape'             => array(
			'label'   => __( 'Button Shape', 'cloudinary' ),
			'type'    => 'select',
			'choices' => array(
				'none'      => __( 'None', 'cloudinary' ),
				'round'     => __( 'Round', 'cloudinary' ),
				'square'    => __( 'Square', 'cloudinary' ),
				'radius'    => __( 'Radius', 'cloudinary' ),
				'rectangle' => __( 'Rectangle', 'cloudinary' ),
			),
			'default' => 'none',
		),
		'carousel_thumbnail_selected_style' => array(
			'label'     => __( 'Thumbnail Selected Style', 'cloudinary' ),
			'type'      => 'select',
			'choices'   => array(
				'border'   => __( 'Border', 'cloudinary' ),
				'gradient' => __( 'Gradient', 'cloudinary' ),
				'all'      => __( 'All', 'cloudinary' ),
			),
			'default'   => 'none',
			'condition' => array(
				'carousel_style' => 'thumbnails',
			),
		),
		'custom_header'                     => array(
			'type'  => 'heading',
			'label' => __( 'Custom Settings', 'cloudinary' ),
		),
		'custom_settings'                   => array(
			'label'       => __( 'Custom JSON (Advanced)', 'cloudinary' ),
			'description' => __( 'Input your own JSON settings in order further customize the gallery with further options that were not included above.', 'cloudinary' ),
			'type'        => 'textarea',
		),
	),
);

return apply_filters( 'cloudinary_admin_tab_gallery', $struct );
