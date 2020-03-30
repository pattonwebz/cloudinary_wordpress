<?php
/**
 * Plugin Name: Cloudinary Update Tester
 * Plugin URI:
 * Description: Test Cloudinary Plugin Update Process (This will deactivate itself, once activated.)
 * Version: 1.0
 * Author: XWP
 * Author URI: https://xwp.co
 * Text Domain: cld-update-tester
 * License: GPL2+
 *
 * @package Cloudinary
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Alter the update plugins object.
 *
 * @param object $data plugin update data.
 *
 * @return object
 */
function cld_test_check_update( $data ) {
	if ( ! empty( $data->no_update ) ) {
		$slug = 'cloudinary-image-management-and-manipulation-in-the-cloud-cdn/cloudinary.php';
		if ( ! empty( $data->no_update[ $slug ] ) ) {
			$file                                  = plugin_dir_path( __FILE__ ) . 'cloudinary.zip';
			$data->no_update[ $slug ]->package     = $file;
			$data->no_update[ $slug ]->new_version = 2.0;
			$data->response[ $slug ]               = $data->no_update[ $slug ];
			unset( $data->no_update[ $slug ] );
			deactivate_plugins( 'cloudinary-update-tester/cloudinary-update-tester.php' );
		}
	}

	return $data;
}

add_filter( 'pre_set_site_transient_update_plugins', 'cld_test_check_update', 100 );

/**
 * Delete the update transient on activation.
 */
function cld_test_init_update() {
	delete_site_transient( 'update_plugins' );
}

register_activation_hook( __FILE__, 'cld_test_init_update' );
